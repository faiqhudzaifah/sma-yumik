<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Illuminate\Support\Facades\File;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        // Patokan data sekarang adalah Semester, karena 1 Tahun Ajaran punya 2 Semester
        $query = Semester::with('academicYear');

        if ($request->filled('search')) {
            $query->whereHas('academicYear', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        $semesters = $query->latest()->paginate(10);
        return view('admin.akademik.tahun-ajaran.index', compact('semesters'));
    }

    public function create()
    {
        return view('admin.akademik.tahun-ajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'semester' => 'required|in:Ganjil,Genap',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            DB::beginTransaction();

            // 1. Cari atau buat Tahun Ajaran Induk (Mencegah Error UNIQUE)
            $academicYear = AcademicYear::firstOrCreate([
                'name' => $request->name
            ]);

            // 2. Cek agar tidak ada 2 Semester Ganjil di Tahun yang sama
            $exists = Semester::where('academic_year_id', $academicYear->id)
                ->where('type', $request->semester)->exists();

            if ($exists) {
                return back()->withInput()->with('error', "Semester {$request->semester} untuk Tahun Ajaran {$request->name} sudah ada!");
            }

            $isActive = $request->has('is_active');

            // 3. Matikan semester lain jika yang baru ini diaktifkan
            if ($isActive) {
                Semester::query()->update(['is_active' => false]);
            }

            // 4. Simpan detail waktu ke tabel semesters
            Semester::create([
                'academic_year_id' => $academicYear->id,
                'type' => $request->semester,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $isActive,
            ]);

            DB::commit();
            return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Periode Akademik berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $tahun_ajaran = Semester::with('academicYear')->findOrFail($id);
        return view('admin.akademik.tahun-ajaran.edit', compact('tahun_ajaran'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'semester' => 'required|in:Ganjil,Genap',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            DB::beginTransaction();

            $semesterData = Semester::findOrFail($id);
            $academicYear = AcademicYear::firstOrCreate(['name' => $request->name]);

            $isActive = $request->has('is_active');

            if ($isActive && !$semesterData->is_active) {
                Semester::where('id', '!=', $semesterData->id)->update(['is_active' => false]);
            }

            $semesterData->update([
                'academic_year_id' => $academicYear->id,
                'type' => $request->semester,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $isActive,
            ]);

            DB::commit();
            return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $semesterData = Semester::findOrFail($id);

        if ($semesterData->is_active) {
            return back()->with('error', 'Tidak dapat menghapus periode yang sedang aktif.');
        }

        $semesterData->delete();
        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Periode dihapus.');
    }

    /**
     * Meng-generate file ZIP berisi struktur arsip akademik untuk diunduh.
     */
    public function export($id)
    {
        // Sesuaikan tarikan data karena ID yang dikirim sekarang adalah ID Semester
        $semesterData = Semester::with('academicYear')->findOrFail($id);
        $tahun_name = $semesterData->academicYear->name;

        // 1. Persiapan Nama File & Direktori
        $safeName = str_replace('/', '-', $tahun_name);
        $zipFileName = "Arsip_Akademik_{$safeName}_Semester_{$semesterData->type}.zip";
        $tempDir = storage_path('app/temp_exports');

        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $zipPath = $tempDir . '/' . $zipFileName;
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            $rootDir = "Tahun_Ajaran_{$safeName}_{$semesterData->type}";
            $status = $semesterData->is_active ? 'Aktif' : 'Arsip';

            // 1. Data Akademik
            $zip->addEmptyDir("{$rootDir}/1_Data_Akademik");
            $zip->addFromString("{$rootDir}/1_Data_Akademik/01_Tahun_Ajaran_Semester.txt", "Tahun Ajaran: {$tahun_name}\nSemester: {$semesterData->type}\nStatus: {$status}");
            $zip->addFromString("{$rootDir}/1_Data_Akademik/02_Daftar_Kelas_Rombel.csv", "Kode Kelas,Nama Kelas,Wali Kelas\n(Data menyusul)");
            $zip->addFromString("{$rootDir}/1_Data_Akademik/03_Data_Siswa.csv", "NISN,Nama Siswa,Kelas,Status\n(Data menyusul)");
            $zip->addFromString("{$rootDir}/1_Data_Akademik/04_Data_Guru.csv", "NIP,Nama Guru,Status Kepegawaian\n(Data menyusul)");
            $zip->addFromString("{$rootDir}/1_Data_Akademik/05_Mata_Pelajaran.csv", "Kode Mapel,Nama Mapel,KKM\n(Data menyusul)");

            // 2. Jadwal
            $zip->addEmptyDir("{$rootDir}/2_Jadwal");
            $zip->addFromString("{$rootDir}/2_Jadwal/01_Jadwal_Pelajaran.csv", "Hari,Jam,Kelas,Mapel,Guru\n(Data menyusul)");
            $zip->addFromString("{$rootDir}/2_Jadwal/02_Guru_Pengampu.csv", "Nama Guru,Mapel yang Diampu,Total Jam\n(Data menyusul)");

            // 3. Kehadiran
            $zip->addEmptyDir("{$rootDir}/3_Kehadiran");
            $zip->addFromString("{$rootDir}/3_Kehadiran/01_Presensi_Siswa_Harian.csv", "Tanggal,NISN,Nama,Status(H/I/S/A)\n(Data menyusul)");
            $zip->addFromString("{$rootDir}/3_Kehadiran/02_Rekap_Kehadiran_Semester.csv", "NISN,Nama,Total Hadir,Total Izin,Total Sakit,Total Alpa\n(Data menyusul)");

            // 4. Penilaian
            $zip->addEmptyDir("{$rootDir}/4_Penilaian");
            $zip->addFromString("{$rootDir}/4_Penilaian/01_Nilai_Tugas.csv", "NISN,Nama Siswa,Tugas 1,Tugas 2,Tugas 3,Rata-rata\n(Data menyusul)");
            $zip->addFromString("{$rootDir}/4_Penilaian/02_Nilai_Kuis.csv", "NISN,Nama Siswa,Kuis 1,Kuis 2\n(Data menyusul)");
            $zip->addFromString("{$rootDir}/4_Penilaian/03_Nilai_Ujian.csv", "NISN,Nama Siswa,UTS,UAS\n(Data menyusul)");
            $zip->addFromString("{$rootDir}/4_Penilaian/04_Nilai_Akhir_Raport.csv", "NISN,Nama Siswa,Nilai Angka,Predikat\n(Data menyusul)");

            // 5. Pembelajaran
            $zip->addEmptyDir("{$rootDir}/5_Pembelajaran");
            $zip->addFromString("{$rootDir}/5_Pembelajaran/01_Log_Materi_Dibagikan.csv", "Tanggal,Mapel,Guru,Judul Materi\n(Data menyusul)");
            $zip->addFromString("{$rootDir}/5_Pembelajaran/02_Aktivitas_Pembelajaran.csv", "Tanggal,Log Aktivitas\n(Data menyusul)");

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function show($id)
    {
        $tahun_ajaran = Semester::with('academicYear')->findOrFail($id);

        // Menghitung statistik untuk dasbor detail semester
        $stats = [
            'rombel' => \App\Models\Classroom::where('semester_id', $id)->count(),
            'mapel'  => \App\Models\Subject::where('semester_id', $id)->count(),
            'jadwal' => \App\Models\Schedule::where('semester_id', $id)->count(),
        ];

        return view('admin.akademik.tahun-ajaran.show', compact('tahun_ajaran', 'stats'));
    }
}
