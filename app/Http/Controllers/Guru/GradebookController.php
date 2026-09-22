<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Grade;
use App\Models\Submission;
use App\Exports\GradebookExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GradebookController extends Controller
{
    public function index(Schedule $schedule)
    {
        $data = $this->generateMatrix($schedule);
        return view('guru.pembelajaran.gradebook.index', array_merge(['schedule' => $schedule], $data));
    }

    public function export(Schedule $schedule)
    {
        $data = $this->generateMatrix($schedule);

        $fileName = 'Rekap_Nilai_' . str_replace(' ', '_', $schedule->classroom->name) . '_' . $schedule->subject->code . '.xlsx';

        return Excel::download(new GradebookExport($schedule, $data), $fileName);
    }

    /**
     * Private function untuk menyatukan kalkulasi nilai agar kode tetap DRY (Don't Repeat Yourself)
     */
    private function generateMatrix(Schedule $schedule): array 
    {
        $schedule->load(['classroom.siswas.user', 'subject']);

        $assignments = $schedule->assignments()->orderBy('created_at', 'asc')->get();
        $assessments = $schedule->assessments()->whereIn('status', ['PUBLISHED', 'CLOSED'])->orderBy('start_at', 'asc')->get();

        $allGrades = Grade::whereIn('assignment_id', $assignments->pluck('id'))->get()->groupBy('siswa_id');
        $allSubmissions = Submission::whereIn('assessment_id', $assessments->pluck('id'))->get()->groupBy('siswa_id');

        $gradeMatrix = [];
        $siswas = $schedule->classroom->siswas;

        foreach ($siswas as $siswa) {
            $studentData = [
                'siswa' => $siswa,
                'assignments' => [],
                'assessments' => [],
                'avg_assignments' => 0,
                'avg_assessments' => 0,
                'final_score' => 0,
            ];

            $totalAssignmentScore = 0;
            $siswaGrades = $allGrades->get($siswa->id, collect());
            foreach ($assignments as $assignment) {
                $grade = $siswaGrades->firstWhere('assignment_id', $assignment->id);
                $score = $grade ? (float) $grade->score : 0;
                $studentData['assignments'][$assignment->id] = $score;
                $totalAssignmentScore += $score;
            }
            if ($assignments->count() > 0) {
                $studentData['avg_assignments'] = $totalAssignmentScore / $assignments->count();
            }

            $totalAssessmentScore = 0;
            $siswaSubmissions = $allSubmissions->get($siswa->id, collect());
            foreach ($assessments as $assessment) {
                $submission = $siswaSubmissions->where('assessment_id', $assessment->id)->sortByDesc('score')->first();
                $score = $submission ? (float) $submission->score : 0;
                $studentData['assessments'][$assessment->id] = $score;
                $totalAssessmentScore += $score;
            }
            if ($assessments->count() > 0) {
                $studentData['avg_assessments'] = $totalAssessmentScore / $assessments->count();
            }

            // Pembobotan: 40% Tugas, 60% Ujian
            $studentData['final_score'] = ($studentData['avg_assignments'] * 0.4) + ($studentData['avg_assessments'] * 0.6);
            $gradeMatrix[] = $studentData;
        }

        usort($gradeMatrix, function ($a, $b) {
            return strcmp($a['siswa']->user->name, $b['siswa']->user->name);
        });

        return [
            'assignments' => $assignments,
            'assessments' => $assessments,
            'gradeMatrix' => $gradeMatrix
        ];
    }
}
