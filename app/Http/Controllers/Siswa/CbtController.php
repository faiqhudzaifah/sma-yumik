<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Submission;
use App\Models\StudentAnswer;
use App\Models\Question;
use App\Models\Option;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CbtController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $activeSemester = Semester::where('is_active', true)->first();
        $classroom = $siswa->classrooms()->where('semester_id', $activeSemester?->id)->first();

        $assessments = collect();

        if ($classroom) {
            $assessments = Assessment::with(['schedule.subject', 'schedule.guru'])
                ->whereHas('schedule', fn($q) => $q->where('classroom_id', $classroom->id))
                ->whereIn('status', ['PUBLISHED', 'CLOSED'])
                ->orderByDesc('start_at')
                ->get()
                ->map(function ($assessment) use ($siswa) {
                    $submission = Submission::where('assessment_id', $assessment->id)
                        ->where('siswa_id', $siswa->id)
                        ->first();

                    $assessment->submission = $submission;
                    $assessment->is_available = $assessment->status === 'PUBLISHED'
                        && $assessment->start_at && $assessment->start_at->isPast()
                        && $assessment->end_at && $assessment->end_at->isFuture();

                    return $assessment;
                });
        }

        return view('siswa.cbt.index', compact('assessments', 'activeSemester'));
    }

    public function show(Assessment $assessment)
    {
        $siswa = Auth::user()->siswa;
        $submission = Submission::where('assessment_id', $assessment->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        return view('siswa.cbt.show', compact('assessment', 'submission'));
    }

    public function start(Assessment $assessment)
    {
        if ($assessment->status !== 'PUBLISHED') {
            return back()->with('error', 'Ujian belum dibuka.');
        }

        $siswa = Auth::user()->siswa;

        $submission = Submission::where('assessment_id', $assessment->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        if ($submission && $submission->submitted_at !== null) {
            return back()->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        if (!$submission) {
            $submission = Submission::create([
                'assessment_id'  => $assessment->id,
                'siswa_id'       => $siswa->id,
                'started_at'     => now(),
                'attempt_number' => 1,
            ]);
        }

        return redirect()->route('siswa.cbt.exam', $assessment->id);
    }

    public function exam(Assessment $assessment)
    {
        $siswa = Auth::user()->siswa;
        $submission = Submission::where('assessment_id', $assessment->id)
            ->where('siswa_id', $siswa->id)
            ->firstOrFail();

        if ($submission->submitted_at !== null) {
            return redirect()->route('siswa.cbt.show', $assessment->id)
                ->with('info', 'Ujian telah selesai.');
        }

        $query = $assessment->questions()->with('options');
        $questions = $assessment->shuffle_questions ? $query->inRandomOrder()->get() : $query->get();

        $answers = $submission->answers->keyBy('question_id');

        return view('siswa.cbt.exam', compact('assessment', 'submission', 'questions', 'answers'));
    }

    public function submit(Request $request, Assessment $assessment)
    {
        $siswa = Auth::user()->siswa;
        $submission = Submission::where('assessment_id', $assessment->id)
            ->where('siswa_id', $siswa->id)
            ->firstOrFail();

        if ($submission->submitted_at !== null) {
            return redirect()->route('siswa.cbt.index')->with('error', 'Akses ditolak.');
        }

        try {
            DB::beginTransaction();

            $totalScore = 0;

            if ($request->has('answers')) {
                foreach ($request->answers as $question_id => $answer_val) {
                    $question = Question::find($question_id);
                    if (!$question) continue;

                    $isCorrect = null;
                    $pointsAwarded = 0;
                    $optionId = null;
                    $answerText = null;

                    if (in_array($question->type, ['MULTIPLE_CHOICE', 'TRUE_FALSE'])) {
                        $optionId = $answer_val;
                        $selectedOption = Option::find($optionId);

                        if ($selectedOption) {
                            $isCorrect = $selectedOption->is_correct;
                            $pointsAwarded = $isCorrect ? $question->points : 0;
                            $totalScore += $pointsAwarded;
                        }
                    } else {
                        $answerText = $answer_val;
                    }

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

            $submission->update([
                'submitted_at' => now(),
                'score'        => $totalScore,
            ]);

            DB::commit();
            return redirect()->route('siswa.cbt.show', $assessment->id)
                ->with('success', 'Ujian berhasil diselesaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
