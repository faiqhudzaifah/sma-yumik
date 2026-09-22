<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;
use App\Models\Schedule;
use App\Models\Assignment;
use App\Models\Assessment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;
        $activeSemester = Semester::where('is_active', true)->first();

        if (!$activeSemester || !$siswa) {
            abort(403, 'Semester aktif atau data siswa tidak ditemukan.');
        }

        $enrollmentAktif = $siswa->classrooms()->where('semester_id', $activeSemester->id)->first();

        if (!$enrollmentAktif) {
            // Jika tidak ada kelas, kembalikan data kosong agar Front-End tidak error
            return view('siswa.dashboard', [
                'jadwalHariIni' => collect(),
                'tugasMendesak' => collect(),
                'kuisTersedia' => collect()
            ]);
        }

        $hariMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hariIni = $hariMap[now()->format('l')];

        // 1. Jadwal Hari Ini
        $jadwalHariIni = Schedule::with(['subject', 'guru', 'lessonHour'])
            ->where('classroom_id', $enrollmentAktif->id)
            ->where('semester_id', $activeSemester->id)
            ->where('day_of_week', $hariIni)
            ->orderBy('jam_ke_mulai', 'asc')
            ->get();

        // 2. Tugas Mendesak (Tanpa mengubah model/relasi, menggunakan subquery murni ke tabel grades)
        $tugasMendesak = Assignment::with('subject')
            ->whereHas('schedule', function ($q) use ($enrollmentAktif) {
                $q->where('classroom_id', $enrollmentAktif->id);
            })
            ->whereNotIn('id', function ($query) use ($siswa) {
                $query->select('assignment_id')
                    ->from('grades')
                    ->where('siswa_id', $siswa->id)
                    ->whereNotNull('assignment_id');
            })
            ->where('deadline', '>=', now())
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        // 3. Kuis Tersedia
        $kuisTersedia = Assessment::whereHas('schedule', function ($q) use ($enrollmentAktif) {
            $q->where('classroom_id', $enrollmentAktif->id);
        })
            ->where('status', 'PUBLISHED')
            ->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->get();

        return view('siswa.dashboard', compact('enrollmentAktif', 'jadwalHariIni', 'tugasMendesak', 'kuisTersedia'));
    }
}
