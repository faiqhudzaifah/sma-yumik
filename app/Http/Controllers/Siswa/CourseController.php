<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Menampilkan daftar Mapel/Jadwal milik siswa di semester aktif.
     */
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $activeSemester = Semester::where('is_active', true)->firstOrFail();

        // 1. Cari siswa ini ada di Rombel mana pada semester aktif
        $classroom = $siswa->classrooms()->where('semester_id', $activeSemester->id)->first();

        if (!$classroom) {
            return view('siswa.course.empty', ['message' => 'Anda belum dimasukkan ke rombongan belajar manapun di semester ini.']);
        }

        // 2. Tarik semua jadwal mapel untuk Rombel tersebut
        $schedules = Schedule::with(['subject', 'guru.user'])
            ->where('classroom_id', $classroom->id)
            ->where('semester_id', $activeSemester->id)
            ->orderBy('jam_ke_mulai', 'asc')
            ->get();

        return view('siswa.course.index', compact('schedules', 'classroom'));
    }

    /**
     * Menampilkan detail satu Mapel (Materi, Tugas, Ujian, Riwayat Presensi).
     */
    public function show(Schedule $schedule)
    {
        // Validasi keamanan: Pastikan siswa benar-benar berada di kelas pemilik jadwal ini
        $siswa = Auth::user()->siswa;
        $isEnrolled = $siswa->classrooms()->where('classrooms.id', $schedule->classroom_id)->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
        }

        // Muat semua aktivitas belajar
        $schedule->load(['materials', 'assignments', 'assessments', 'meetings.attendances' => function ($query) use ($siswa) {
            $query->where('siswa_id', $siswa->id);
        }]);

        return view('siswa.course.show', compact('schedule'));
    }
}
