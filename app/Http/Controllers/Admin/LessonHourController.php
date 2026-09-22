<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonHour;
use Illuminate\Http\Request;

class LessonHourController extends Controller
{
    public function index()
    {
        // Mengambil data dan diurutkan berdasarkan tipe hari, lalu urutan jam
        $lessonHours = LessonHour::orderBy('day_type')->orderBy('start_time')->get();
        return view('admin.akademik.jam-pelajaran.index', compact('lessonHours'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'day_type'   => 'required|in:Umum,Jumat',
            'jam_ke'     => 'nullable|integer|min:1',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'type'       => 'required|in:Belajar,Istirahat,Upacara,Lainnya',
        ]);

        try {
            LessonHour::create($request->all());
            return back()->with('success', 'Jam pelajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan konfigurasi jam: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LessonHour $jam_pelajaran)
    {
        $request->validate([
            'day_type'   => 'required|in:Umum,Jumat',
            'jam_ke'     => 'nullable|integer|min:1',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'type'       => 'required|in:Belajar,Istirahat,Upacara,Lainnya',
        ]);

        try {
            $jam_pelajaran->update($request->all());
            return back()->with('success', 'Jam pelajaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui konfigurasi jam.');
        }
    }

    public function destroy(LessonHour $jam_pelajaran)
    {
        $jam_pelajaran->delete();
        return back()->with('success', 'Jam pelajaran berhasil dihapus.');
    }
}
