<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function show(Material $material)
    {
        $siswa = Auth::user()->siswa;
        $schedule = $material->schedule;

        $isEnrolled = $siswa->classrooms()
            ->where('classroom_id', $schedule->classroom_id)
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        // FIX: Kolom status tidak ada di migration materials.
        // Hapus pengecekan status agar tidak error.
        // Kalau nanti kolom status ditambah, aktifkan lagi baris di bawah.

        // if ($material->status !== 'PUBLISHED') {
        //     abort(404, 'Materi belum dipublikasikan.');
        // }

        return view('siswa.materi.show', compact('material'));
    }
}
