<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Semester;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaliKelasController extends Controller
{
    private function getWaliKelas()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            abort(403, 'Data guru tidak ditemukan.');
        }

        $activeSemester = Semester::where('is_active', true)->first();

        if (!$activeSemester) {
            abort(404, 'Tidak ada semester aktif.');
        }

        $classroom = Classroom::with(['waliKelas.user', 'siswas.user', 'semester'])
            ->where('wali_kelas_id', $guru->id)
            ->where('semester_id', $activeSemester->id)
            ->first();

        if (!$classroom) {
            abort(403, 'Anda bukan wali kelas di semester ini.');
        }

        return [$guru, $classroom, $activeSemester];
    }

    // ==========================================
    // DASHBOARD
    // ==========================================
    public function dashboard()
    {
        [$guru, $classroom, $activeSemester] = $this->getWaliKelas();

        $totalSiswa = $classroom->siswas->count();

        $totalMapel = Schedule::where('classroom_id', $classroom->id)
            ->where('semester_id', $activeSemester->id)
            ->distinct('subject_id')
            ->count('subject_id');

        $totalMeetings = Meeting::whereHas('schedule', function ($q) use ($classroom, $activeSemester) {
            $q->where('classroom_id', $classroom->id)
                ->where('semester_id', $activeSemester->id);
        })->count();

        $totalHadir = Attendance::whereHas('meeting.schedule', function ($q) use ($classroom, $activeSemester) {
            $q->where('classroom_id', $classroom->id)
                ->where('semester_id', $activeSemester->id);
        })->where('status', 'Hadir')->count();

        $totalAttendance = Attendance::whereHas('meeting.schedule', function ($q) use ($classroom, $activeSemester) {
            $q->where('classroom_id', $classroom->id)
                ->where('semester_id', $activeSemester->id);
        })->count();

        $persenKehadiran = $totalAttendance > 0
            ? round(($totalHadir / $totalAttendance) * 100, 1)
            : 0;

        return view('guru.wali-kelas.dashboard', compact(
            'guru',
            'classroom',
            'activeSemester',
            'totalSiswa',
            'totalMapel',
            'persenKehadiran',
            'totalMeetings'
        ));
    }

    // ==========================================
    // JADWAL KELAS (BARU)
    // ==========================================
    public function jadwal()
    {
        [$guru, $classroom, $activeSemester] = $this->getWaliKelas();

        $schedules = Schedule::with(['subject', 'guru.user'])
            ->where('classroom_id', $classroom->id)
            ->where('semester_id', $activeSemester->id)
            ->orderByRaw("CASE day_of_week
                WHEN 'Senin' THEN 1
                WHEN 'Selasa' THEN 2
                WHEN 'Rabu' THEN 3
                WHEN 'Kamis' THEN 4
                WHEN 'Jumat' THEN 5
                WHEN 'Sabtu' THEN 6
                ELSE 7 END")
            ->orderBy('jam_ke_mulai')
            ->get();

        return view('guru.wali-kelas.jadwal', compact(
            'guru',
            'classroom',
            'activeSemester',
            'schedules'
        ));
    }

    // ==========================================
    // DAFTAR SISWA
    // ==========================================
    public function siswa(Request $request)
    {
        [$guru, $classroom, $activeSemester] = $this->getWaliKelas();

        $search = $request->get('search');

        $siswas = $classroom->siswas()
            ->with('user')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%");
                })->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            })
            ->get();

        return view('guru.wali-kelas.siswa', compact(
            'guru',
            'classroom',
            'activeSemester',
            'siswas',
            'search'
        ));
    }

    // ==========================================
    // DETAIL SISWA — dengan rekap per mapel
    // ==========================================
    public function siswaDetail($siswaId)
    {
        [$guru, $classroom, $activeSemester] = $this->getWaliKelas();

        $siswa = $classroom->siswas()
            ->with('user')
            ->where('siswas.id', $siswaId)
            ->firstOrFail();

        // Ambil semua mapel di kelas ini
        $schedules = Schedule::with(['subject', 'guru.user'])
            ->where('classroom_id', $classroom->id)
            ->where('semester_id', $activeSemester->id)
            ->get()
            ->unique('subject_id')
            ->values();

        // Rekap kehadiran per mapel
        $rekapPerMapel = [];
        $detailPerMapel = [];

        foreach ($schedules as $schedule) {
            $meetings = Meeting::where('schedule_id', $schedule->id)
                ->orderBy('date')
                ->get();

            $meetingIds = $meetings->pluck('id');

            $attendances = Attendance::where('siswa_id', $siswa->id)
                ->whereIn('meeting_id', $meetingIds)
                ->get();

            $rekapPerMapel[] = [
                'schedule' => $schedule,
                'mapel'    => $schedule->subject->name ?? '-',
                'guru'     => $schedule->guru->user->name ?? '-',
                'hadir'    => $attendances->where('status', 'Hadir')->count(),
                'sakit'    => $attendances->where('status', 'Sakit')->count(),
                'izin'     => $attendances->where('status', 'Izin')->count(),
                'alpa'     => $attendances->where('status', 'Alpa')->count(),
                'total'    => $attendances->count(),
                'persen'   => $attendances->count() > 0
                    ? round(($attendances->where('status', 'Hadir')->count() / $attendances->count()) * 100, 1)
                    : 0,
            ];

            // Detail per pertemuan
            $detail = [];
            foreach ($meetings as $meeting) {
                $att = $attendances->firstWhere('meeting_id', $meeting->id);
                $detail[] = [
                    'meeting' => $meeting,
                    'status'  => $att->status ?? 'Belum diabsen',
                    'notes'   => $att->notes ?? null,
                ];
            }

            $detailPerMapel[$schedule->id] = $detail;
        }

        return view('guru.wali-kelas.siswa-detail', compact(
            'guru',
            'classroom',
            'activeSemester',
            'siswa',
            'schedules',
            'rekapPerMapel',
            'detailPerMapel'
        ));
    }

    // ==========================================
    // REKAP KEHADIRAN — dengan filter mapel
    // ==========================================
    public function kehadiran(Request $request)
    {
        [$guru, $classroom, $activeSemester] = $this->getWaliKelas();

        $siswas = $classroom->siswas()->with('user')->get();

        // Ambil semua mapel untuk dropdown filter
        $schedules = Schedule::with('subject')
            ->where('classroom_id', $classroom->id)
            ->where('semester_id', $activeSemester->id)
            ->get()
            ->unique('subject_id')
            ->values();

        // Filter: mapel mana?
        $filterScheduleId = $request->get('schedule_id');

        $meetingQuery = Meeting::whereHas('schedule', function ($q) use ($classroom, $activeSemester) {
            $q->where('classroom_id', $classroom->id)
                ->where('semester_id', $activeSemester->id);
        });

        if ($filterScheduleId) {
            $meetingQuery->where('schedule_id', $filterScheduleId);
        }

        $meetings = $meetingQuery->orderBy('date')->orderBy('meeting_number')->get();

        // Ambil semua attendance
        $attendances = Attendance::whereIn('meeting_id', $meetings->pluck('id'))
            ->get()
            ->groupBy('siswa_id');

        // Hitung rekap per siswa
        $rekap = $siswas->map(function ($siswa) use ($attendances) {
            $records = $attendances->get($siswa->id, collect());

            return [
                'siswa'  => $siswa,
                'hadir'  => $records->where('status', 'Hadir')->count(),
                'sakit'  => $records->where('status', 'Sakit')->count(),
                'izin'   => $records->where('status', 'Izin')->count(),
                'alpa'   => $records->where('status', 'Alpa')->count(),
                'total'  => $records->count(),
                'persen' => $records->count() > 0
                    ? round(($records->where('status', 'Hadir')->count() / $records->count()) * 100, 1)
                    : 0,
            ];
        });

        return view('guru.wali-kelas.kehadiran', compact(
            'guru',
            'classroom',
            'activeSemester',
            'siswas',
            'meetings',
            'rekap',
            'schedules',
            'filterScheduleId'
        ));
    }

    // ==========================================
    // MATRIX KEHADIRAN
    // ==========================================
    public function kehadiranMatrix(Request $request)
    {
        [$guru, $classroom, $activeSemester] = $this->getWaliKelas();

        $siswas = $classroom->siswas()->with('user')->get();

        $schedules = Schedule::with('subject')
            ->where('classroom_id', $classroom->id)
            ->where('semester_id', $activeSemester->id)
            ->get()
            ->unique('subject_id')
            ->values();

        $filterScheduleId = $request->get('schedule_id');

        $meetingQuery = Meeting::with(['schedule.subject'])
            ->whereHas('schedule', function ($q) use ($classroom, $activeSemester) {
                $q->where('classroom_id', $classroom->id)
                    ->where('semester_id', $activeSemester->id);
            });

        if ($filterScheduleId) {
            $meetingQuery->where('schedule_id', $filterScheduleId);
        }

        $meetings = $meetingQuery->orderBy('date')->orderBy('meeting_number')->get();

        $matrix = [];
        $attendances = Attendance::whereIn('meeting_id', $meetings->pluck('id'))->get();

        foreach ($attendances as $att) {
            $matrix[$att->siswa_id][$att->meeting_id] = $att->status;
        }

        return view('guru.wali-kelas.kehadiran-matrix', compact(
            'guru',
            'classroom',
            'activeSemester',
            'siswas',
            'meetings',
            'matrix',
            'schedules',
            'filterScheduleId'
        ));
    }
}
