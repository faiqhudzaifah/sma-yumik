<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Schedule;
use App\Models\Submission;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentGradingController extends Controller
{
    /**
     * Menampilkan daftar siswa yang sudah/belum mengumpulkan kuis.
     */
    public function index(Schedule $schedule, Assessment $assessment)
    {
        // Ambil semua siswa di kelas ini
        $siswas = $schedule->classroom->siswas()->with(['user'])->get();

        // Ambil data lembar kerja (submission) yang sudah masuk
        $submissions = $assessment->submissions()->with('siswa')->get()->keyBy('siswa_id');

        return view('guru.pembelajaran.cbt.grading_index', compact('schedule', 'assessment', 'siswas', 'submissions'));
    }

    /**
     * Menampilkan detail lembar jawaban satu siswa untuk dikoreksi.
     */
    public function show(Schedule $schedule, Assessment $assessment, Submission $submission)
    {
        // Memuat jawaban siswa beserta soal dan opsi aslinya
        $submission->load(['siswa.user', 'answers.question', 'answers.option']);

        return view('guru.pembelajaran.cbt.grading_show', compact('schedule', 'assessment', 'submission'));
    }

    /**
     * Menyimpan nilai manual dari guru (terutama untuk soal ESSAY).
     */
    public function updateScore(Request $request, Schedule $schedule, Assessment $assessment, Submission $submission)
    {
        $request->validate([
            'answers'                  => 'required|array',
            'answers.*.id'             => 'required|exists:student_answers,id',
            'answers.*.points_awarded' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalPoints = 0;

            foreach ($request->answers as $data) {
                $answer = StudentAnswer::find($data['id']);
                $answer->update([
                    'points_awarded' => $data['points_awarded'],
                    // Jika nilai yang diberikan > 0, anggap benar
                    'is_correct' => $data['points_awarded'] > 0 ? true : false
                ]);
            }

            // Hitung ulang total nilai (gabungan PG yang otomatis dan Esai yang manual)
            $totalScore = $submission->answers()->sum('points_awarded');

            $submission->update([
                'score' => $totalScore
            ]);

            DB::commit();
            return back()->with('success', 'Koreksi berhasil disimpan. Nilai akhir: ' . $totalScore);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan hasil koreksi.');
        }
    }
}
