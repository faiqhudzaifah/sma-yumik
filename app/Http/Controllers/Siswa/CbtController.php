<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Submission;
use App\Models\StudentAnswer;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CbtController extends Controller
{
    /**
     * Halaman persiapan sebelum memulai ujian.
     */
    public function show(Assessment $assessment)
    {
        $siswa = Auth::user()->siswa;
        $submission = Submission::where('assessment_id', $assessment->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        return view('siswa.cbt.show', compact('assessment', 'submission'));
    }

    /**
     * Memulai ujian dan meng-generate lembar jawaban.
     */
    public function start(Assessment $assessment)
    {
        if ($assessment->status !== 'PUBLISHED') {
            return back()->with('error', 'Ujian belum dibuka.');
        }

        $siswa = Auth::user()->siswa;

        // Cek apakah siswa sudah punya submission
        $submission = Submission::where('assessment_id', $assessment->id)->where('siswa_id', $siswa->id)->first();

        if ($submission && $submission->submitted_at !== null) {
            return back()->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        if (!$submission) {
            // Buat sesi ujian baru
            $submission = Submission::create([
                'assessment_id'  => $assessment->id,
                'siswa_id'       => $siswa->id,
                'started_at'     => now(),
                'attempt_number' => 1,
            ]);
        }

        return redirect()->route('siswa.cbt.exam', $assessment->id);
    }

    /**
     * Menampilkan soal satu per satu atau sekaligus (lembar ujian aktif).
     */
    public function exam(Assessment $assessment)
    {
        $siswa = Auth::user()->siswa;
        $submission = Submission::where('assessment_id', $assessment->id)->where('siswa_id', $siswa->id)->firstOrFail();

        if ($submission->submitted_at !== null) {
            return redirect()->route('siswa.cbt.show', $assessment->id)->with('info', 'Ujian telah selesai.');
        }

        // Muat soal. Jika ada aturan shuffle_questions, acak urutannya
        $query = $assessment->questions()->with('options');
        $questions = $assessment->shuffle_questions ? $query->inRandomOrder()->get() : $query->get();

        // Muat jawaban sementara jika siswa sempat keluar halaman
        $answers = $submission->answers->keyBy('question_id');

        return view('siswa.cbt.exam', compact('assessment', 'submission', 'questions', 'answers'));
    }

    /**
     * Menyimpan jawaban (Bisa ditembak pakai AJAX tiap klik opsi, atau Submit massal di akhir).
     */
    public function submit(Request $request, Assessment $assessment)
    {
        $siswa = Auth::user()->siswa;
        $submission = Submission::where('assessment_id', $assessment->id)->where('siswa_id', $siswa->id)->firstOrFail();

        if ($submission->submitted_at !== null) {
            return redirect()->route('siswa.course.index')->with('error', 'Akses ditolak.');
        }

        try {
            DB::beginTransaction();

            $totalScore = 0;

            // $request->answers berbentuk array: [question_id => option_id] atau [question_id => teks esai]
            if ($request->has('answers')) {
                foreach ($request->answers as $question_id => $answer_val) {
                    $question = Question::find($question_id);
                    if (!$question) continue;

                    $isCorrect = null;
                    $pointsAwarded = 0;
                    $optionId = null;
                    $answerText = null;

                    if ($question->type === 'MULTIPLE_CHOICE' || $question->type === 'TRUE_FALSE') {
                        $optionId = $answer_val;
                        $selectedOption = Option::find($optionId);

                        if ($selectedOption) {
                            $isCorrect = $selectedOption->is_correct;
                            $pointsAwarded = $isCorrect ? $question->points : 0;
                            $totalScore += $pointsAwarded;
                        }
                    } else { // ESSAY
                        $answerText = $answer_val;
                    }

                    // Insert atau Update jawaban siswa (UpdateOrCreate)
                    StudentAnswer::updateOrCreate(
                        ['submission_id' => $submission->id, 'question_id' => $question->id],
                        [
                            'option_id'      => $optionId,
                            'answer_text'    => $answerText,
                            'is_correct'     => $isCorrect,
                            'points_awarded' => $pointsAwarded,
                        ]
                    );
                }
            }

            // Kunci ujian dan simpan nilai
            $submission->update([
                'submitted_at' => now(),
                'score'        => $totalScore // Nilai PG langsung masuk. Esai menunggu koreksi guru.
            ]);

            DB::commit();
            return redirect()->route('siswa.cbt.show', $assessment->id)->with('success', 'Ujian berhasil diselesaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan jawaban.');
        }
    }
}
