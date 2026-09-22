<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\Promotion;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /**
     * Langkah 1: Pilih Kelas Asal yang ingin diproses.
     */
    public function index(Request $request)
    {
        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();

        $query = Classroom::with(['semester.academicYear', 'waliKelas.user'])->withCount('siswas');

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        $classrooms = $query->get();

        return view('admin.akademik.promotion.index', compact('semesters', 'classrooms'));
    }

    /**
     * Langkah 2: Halaman Review (Sistem memberi rekomendasi, Operator memvalidasi)
     */
    public function review(Classroom $rombel)
    {
        $rombel->load(['semester.academicYear', 'siswas.user']);

        // Ambil data semester berikutnya (sebagai target tujuan)
        $nextSemester = Semester::where('id', '>', $rombel->semester_id)->orderBy('id', 'asc')->first();

        // Ambil daftar kelas di semester berikutnya sebagai opsi pilihan "Rombel Tujuan"
        $targetClassrooms = collect();
        if ($nextSemester) {
            $targetClassrooms = Classroom::where('semester_id', $nextSemester->id)->get();
        }

        // Generate Rekomendasi Otomatis untuk Front-End
        $recommendations = [];
        foreach ($rombel->siswas as $siswa) {
            // Logika Sederhana: Jika Kelas 12 -> LULUS. Selain itu -> NAIK.
            // (Nantinya bisa dikembangkan dengan mengecek tabel Grade/Nilai)
            if ($rombel->grade_level == 12) {
                $recommendations[$siswa->id] = 'LULUS';
            } else {
                $recommendations[$siswa->id] = 'NAIK';
            }
        }

        return view('admin.akademik.promotion.review', compact('rombel', 'nextSemester', 'targetClassrooms', 'recommendations'));
    }

    /**
     * Langkah 3: Eksekusi Kenaikan Kelas secara Massal
     */
    public function process(Request $request, Classroom $rombel)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'decisions'        => 'required|array', // Format: [siswa_id => ['status' => 'NAIK', 'to_classroom_id' => 5]]
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->decisions as $siswa_id => $decision) {
                $status = $decision['status'];
                $to_classroom_id = isset($decision['to_classroom_id']) ? $decision['to_classroom_id'] : null;

                // 1. Catat ke tabel riwayat (promotions)
                Promotion::create([
                    'siswa_id'          => $siswa_id,
                    'from_classroom_id' => $rombel->id,
                    'to_classroom_id'   => $status === 'LULUS' ? null : $to_classroom_id,
                    'status'            => $status,
                    'academic_year_id'  => $request->academic_year_id,
                ]);

                // 2. Jika LULUS, ubah status Siswa menjadi Alumni
                if ($status === 'LULUS') {
                    Siswa::where('id', $siswa_id)->update(['status' => 'Lulus']);
                }
                // 3. Jika NAIK atau TINGGAL, generate enrollment baru TANPA menghapus yang lama
                else if ($to_classroom_id) {
                    $targetClassroom = Classroom::find($to_classroom_id);
                    if ($targetClassroom) {
                        // syncWithoutDetaching menginjeksi baris baru di pivot table
                        $targetClassroom->siswas()->syncWithoutDetaching([$siswa_id]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.promotion.index')->with('success', 'Proses kenaikan kelas berhasil dieksekusi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses kenaikan kelas: ' . $e->getMessage());
        }
    }
}
