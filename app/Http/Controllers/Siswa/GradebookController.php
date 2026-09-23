<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Submission;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;

class GradebookController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $activeSemester = Semester::where('is_active', true)->first();

        // Nilai dari TUGAS (Grade)
        $assignmentGrades = Grade::with(['assignment.schedule.subject'])
            ->where('siswa_id', $siswa->id)
            ->whereNotNull('assignment_id')
            ->latest()
            ->get();

        // Nilai dari UJIAN (Submission)
        $examScores = Submission::with(['assessment.schedule.subject'])
            ->where('siswa_id', $siswa->id)
            ->whereNotNull('submitted_at')
            ->latest()
            ->get();

        return view('siswa.akademik.rapor', compact(
            'assignmentGrades',
            'examScores',
            'activeSemester'
        ));
    }

    public function attendance()
    {
        $siswa = Auth::user()->siswa;
        $activeSemester = Semester::where('is_active', true)->first();

        $attendances = Attendance::with(['meeting.schedule.subject'])
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->get();

        // Hitung rekap
        $summary = [
            'Hadir' => $attendances->where('status', 'Hadir')->count(),
            'Sakit' => $attendances->where('status', 'Sakit')->count(),
            'Izin'  => $attendances->where('status', 'Izin')->count(),
            'Alpa'  => $attendances->where('status', 'Alpa')->count(),
        ];

        return view('siswa.akademik.kehadiran', compact(
            'attendances',
            'activeSemester',
            'summary'
        ));
    }
}
