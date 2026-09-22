<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;

class GradebookController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $activeSemester = Semester::where('is_active', true)->first();

        // Asumsi model Grade memiliki relasi 'assessment' dan 'assignment' bawaan dari backend Anda
        $grades = Grade::with(['assessment', 'assignment'])
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->get();

        return view('siswa.akademik.rapor', compact('grades', 'activeSemester'));
    }

    public function attendance()
    {
        $siswa = Auth::user()->siswa;
        $activeSemester = Semester::where('is_active', true)->first();

        // Mengambil histori kehadiran langsung dari tabel attendances
        $attendances = Attendance::where('siswa_id', $siswa->id)
            ->latest()
            ->get();

        return view('siswa.akademik.kehadiran', compact('attendances', 'activeSemester'));
    }
}
