<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Guru;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();
        $classrooms = Classroom::all();

        $query = Schedule::with(['semester.academicYear', 'classroom', 'subject', 'guru.user']);

        // Filter default: Semester Aktif
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        } else {
            $activeSemester = $semesters->firstWhere('is_active', true);
            if ($activeSemester) {
                $query->where('semester_id', $activeSemester->id);
            }
        }

        // Filter berdasarkan kelas
        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        // Ambil data terlebih dahulu dari database
        $schedules = $query->get();

        // Urutkan menggunakan Collection (Kompatibel dengan SQLite/MySQL/PostgreSQL)
        $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];

        $schedules = $schedules->sortBy(function ($schedule) use ($hariOrder) {
            // Urutkan berdasarkan indeks hari, lalu berdasarkan jam mulai
            $hariIndex = $hariOrder[$schedule->day_of_week] ?? 7;
            return sprintf('%s-%03d', $hariIndex, $schedule->jam_ke_mulai);
        })->values(); // Reset array keys setelah diurutkan

        // ---> TAMBAHKAN DUA BARIS INI <---
        $subjects = Subject::all();
        $gurus = Guru::with('user')->get();

        // ---> TAMBAHKAN $subjects DAN $gurus KE DALAM COMPACT <---
        return view('admin.akademik.jadwal.index', compact('schedules', 'semesters', 'classrooms', 'subjects', 'gurus'));
    }

    public function create()
    {
        $semesters = Semester::with('academicYear')->where('is_active', true)->get();
        $classrooms = Classroom::all();
        $subjects = Subject::all();
        $gurus = Guru::with('user')->get();

        return view('admin.akademik.jadwal.create', compact('semesters', 'classrooms', 'subjects', 'gurus'));
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        // 1. Jalankan Cek Bentrok
        $bentrok = $this->cekBentrok($request);
        if ($bentrok) {
            return back()->withInput()->with('error', $bentrok);
        }

        // 2. Jika aman, simpan
        try {
            Schedule::create($request->all());
            return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function edit(Schedule $jadwal)
    {
        $semesters = Semester::with('academicYear')->get();
        $classrooms = Classroom::all();
        $subjects = Subject::all();
        $gurus = Guru::with('user')->get();

        return view('admin.akademik.jadwal.edit', compact('jadwal', 'semesters', 'classrooms', 'subjects', 'gurus'));
    }

    public function update(Request $request, Schedule $jadwal)
    {
        $this->validateRequest($request);

        // 1. Jalankan Cek Bentrok (Kecualikan ID jadwal yang sedang diedit)
        $bentrok = $this->cekBentrok($request, $jadwal->id);
        if ($bentrok) {
            return back()->withInput()->with('error', $bentrok);
        }

        // 2. Jika aman, perbarui
        try {
            $jadwal->update($request->all());
            return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal pelajaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy(Schedule $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    // ==========================================
    // PRIVATE METHODS: VALIDASI & LOGIKA BENTROK
    // ==========================================

    private function validateRequest(Request $request)
    {
        $request->validate([
            'semester_id'    => 'required|exists:semesters,id',
            'classroom_id'   => 'required|exists:classrooms,id',
            'subject_id'     => 'required|exists:subjects,id',
            'guru_id'        => 'required|exists:gurus,id',
            'day_of_week'    => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_ke_mulai'   => 'required|integer|min:1',
            'jam_ke_selesai' => 'required|integer|gte:jam_ke_mulai', // Selesai harus >= Mulai
            'ruangan'        => 'nullable|string|max:50',
        ]);
    }

    /**
     * Algoritma Pendeteksi Bentrok Berdasarkan Rentang Jam (A_start <= B_end AND A_end >= B_start)
     */
    private function cekBentrok(Request $request, $ignoreId = null)
    {
        $query = Schedule::where('semester_id', $request->semester_id)
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($q) use ($request) {
                $q->where('jam_ke_mulai', '<=', $request->jam_ke_selesai)
                    ->where('jam_ke_selesai', '>=', $request->jam_ke_mulai);
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        // Cek 1: Apakah Guru sudah mengajar di kelas lain pada jam yang bersinggungan?
        $bentrokGuru = (clone $query)->where('guru_id', $request->guru_id)->first();
        if ($bentrokGuru) {
            $namaGuru = Guru::with('user')->find($request->guru_id)->user->name;
            return "Bentrok Guru! {$namaGuru} sudah memiliki jadwal di kelas lain pada jam tersebut.";
        }

        // Cek 2: Apakah Kelas ini sudah ada mata pelajaran lain pada jam yang bersinggungan?
        $bentrokKelas = (clone $query)->where('classroom_id', $request->classroom_id)->first();
        if ($bentrokKelas) {
            return "Bentrok Ruang/Kelas! Rombel ini sudah memiliki jadwal mata pelajaran lain pada jam tersebut.";
        }

        return false; // Tidak ada bentrok
    }
}
