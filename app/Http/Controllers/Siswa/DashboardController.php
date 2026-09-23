<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\Schedule;
use App\Models\Assignment;
use App\Models\Assessment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $siswa = $user->siswa;

        $activeSemester = Semester::where('is_active', 1)->first();

        // Default values
        $classroom = null;
        $activeCoursesCount = 0;
        $pendingAssignmentsCount = 0;
        $availableExamsCount = 0;
        $recentCourses = collect();
        $upcomingAssignments = collect();
        $availableExams = collect();
        $todaySchedules = collect();
        $todayName = $this->getIndonesianDayName(now()->format('l'));

        if ($siswa && $activeSemester) {
            $classroom = $siswa->classrooms()
                ->where('semester_id', $activeSemester->id)
                ->first();

            if ($classroom) {
                // ==========================================
                // 1. MATA PELAJARAN (Course) — unik per subject
                // ==========================================
                $recentCourses = Schedule::with(['subject', 'guru'])
                    ->where('classroom_id', $classroom->id)
                    ->where('semester_id', $activeSemester->id)
                    ->get()
                    ->unique('subject_id')
                    ->values();

                $activeCoursesCount = $recentCourses->count();

                // ==========================================
                // 2. JADWAL HARI INI
                // ==========================================
                $todaySchedules = Schedule::with(['subject', 'guru'])
                    ->where('classroom_id', $classroom->id)
                    ->where('semester_id', $activeSemester->id)
                    ->where('day_of_week', $todayName)
                    ->orderBy('jam_ke_mulai')
                    ->get();

                // ==========================================
                // 3. TUGAS MENDATANG (belum dikerjakan, due_date >= now)
                // ==========================================
                $upcomingAssignments = Assignment::with(['schedule.subject', 'schedule.guru'])
                    ->whereHas('schedule', function ($q) use ($classroom) {
                        $q->where('classroom_id', $classroom->id);
                    })
                    ->whereNotIn('id', function ($q) use ($siswa) {
                        $q->select('assignment_id')
                            ->from('grades')
                            ->where('siswa_id', $siswa->id)
                            ->whereNotNull('assignment_id')
                            ->whereNotNull('submitted_at'); // sudah submit
                    })
                    ->where('due_date', '>=', Carbon::now())
                    ->orderBy('due_date')
                    ->take(5)
                    ->get();

                $pendingAssignmentsCount = $upcomingAssignments->count();

                // ==========================================
                // 4. UJIAN TERSEDIA (PUBLISHED & dalam window)
                // ==========================================
                $availableExams = Assessment::with(['schedule.subject', 'schedule.guru'])
                    ->whereHas('schedule', function ($q) use ($classroom) {
                        $q->where('classroom_id', $classroom->id);
                    })
                    ->where('status', 'PUBLISHED')
                    ->where('start_at', '<=', Carbon::now())
                    ->where('end_at', '>=', Carbon::now())
                    ->orderBy('end_at')
                    ->take(5)
                    ->get();

                $availableExamsCount = $availableExams->count();
            }
        }

        return view('siswa.dashboard', compact(
            'siswa',
            'activeSemester',
            'classroom',
            'activeCoursesCount',
            'pendingAssignmentsCount',
            'availableExamsCount',
            'recentCourses',
            'upcomingAssignments',
            'availableExams',
            'todaySchedules',
            'todayName'
        ));
    }

    /**
     * Konversi nama hari Inggris → Indonesia
     */
    private function getIndonesianDayName(string $englishDay): string
    {
        return [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ][$englishDay] ?? $englishDay;
    }
}
