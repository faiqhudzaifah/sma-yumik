<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Semester;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();
        $query = Classroom::with(['semester.academicYear', 'waliKelas.user'])
            ->withCount('siswas');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('room_code', 'like', "%{$search}%");
        }

        // Filter berdasarkan semester, default ke semester aktif jika tidak ada filter
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        } else {
            $activeSemester = $semesters->firstWhere('is_active', true);
            if ($activeSemester) {
                $query->where('semester_id', $activeSemester->id);
            }
        }

        $classrooms = $query->latest()->paginate(15)->withQueryString();

        return view('admin.akademik.rombel.index', compact('classrooms', 'semesters'));
    }

    public function create()
    {
        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();
        // Hanya ambil guru untuk pilihan wali kelas (Role 2)
        $gurus = Guru::with('user')->get();

        return view('admin.akademik.rombel.create', compact('semesters', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester_id'   => 'required|exists:semesters,id',
            'name'          => 'required|string|max:50',
            'room_code'     => 'nullable|string|max:20',
            'grade_level'   => 'required|in:10,11,12',
            'major'         => 'nullable|string|max:50',
            'capacity'      => 'required|integer|min:1',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
        ]);

        try {
            Classroom::create($request->all());
            return redirect()->route('admin.rombel.index')->with('success', 'Rombongan belajar berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data kelas: ' . $e->getMessage());
        }
    }

    public function show(Classroom $rombel)
    {
        $rombel->load(['semester.academicYear', 'waliKelas.user', 'siswas.user']);

        // Ambil daftar siswa yang aktif, dan BELUM masuk ke kelas manapun PADA SEMESTER INI
        // Ini mencegah satu siswa terdaftar di dua kelas berbeda pada semester yang sama
        $availableSiswas = Siswa::with('user')
            ->where('status', 'Aktif')
            ->whereDoesntHave('classrooms', function ($query) use ($rombel) {
                $query->where('semester_id', $rombel->semester_id);
            })->get();

        return view('admin.akademik.rombel.show', compact('rombel', 'availableSiswas'));
    }

    public function edit(Classroom $rombel)
    {
        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();
        $gurus = Guru::with('user')->get();

        return view('admin.akademik.rombel.edit', compact('rombel', 'semesters', 'gurus'));
    }

    public function update(Request $request, Classroom $rombel)
    {
        $request->validate([
            'semester_id'   => 'required|exists:semesters,id',
            'name'          => 'required|string|max:50',
            'room_code'     => 'nullable|string|max:20',
            'grade_level'   => 'required|in:10,11,12',
            'major'         => 'nullable|string|max:50',
            'capacity'      => 'required|integer|min:1|max:100',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
        ]);

        try {
            $rombel->update($request->all());
            return redirect()->route('admin.rombel.index')->with('success', 'Data rombongan belajar berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data kelas: ' . $e->getMessage());
        }
    }

    public function destroy(Classroom $rombel)
    {
        try {
            $rombel->delete();
            return redirect()->route('admin.rombel.index')->with('success', 'Rombongan belajar berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kelas karena ada data yang berelasi.');
        }
    }

    // ==========================================
    // METODE KHUSUS: MANAJEMEN SISWA DALAM KELAS
    // ==========================================

    /**
     * Memasukkan beberapa siswa ke dalam kelas sekaligus
     */
    public function assignStudents(Request $request, Classroom $rombel)
    {
        $request->validate([
            'siswa_ids'   => 'required|array',
            'siswa_ids.*' => 'exists:siswas,id'
        ], [
            'siswa_ids.required' => 'Pilih minimal satu siswa untuk dimasukkan ke kelas.'
        ]);

        try {
            // Periksa kapasitas kelas sebelum memasukkan siswa
            $currentCount = $rombel->siswas()->count();
            $incomingCount = count($request->siswa_ids);

            if (($currentCount + $incomingCount) > $rombel->capacity) {
                return back()->with('error', 'Gagal! Kapasitas kelas tidak mencukupi untuk jumlah siswa yang dipilih.');
            }

            // syncWithoutDetaching mencegah siswa yang sudah ada di kelas ini terhapus
            $rombel->siswas()->syncWithoutDetaching($request->siswa_ids);

            return back()->with('success', $incomingCount . ' Siswa berhasil ditambahkan ke kelas.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat menambahkan siswa.');
        }
    }

    /**
     * Mengeluarkan satu siswa dari kelas
     */
    public function removeStudent(Classroom $rombel, Siswa $siswa)
    {
        try {
            // Memutuskan relasi di tabel pivot classroom_siswa
            $rombel->siswas()->detach($siswa->id);
            return back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengeluarkan siswa dari kelas.');
        }
    }
}
