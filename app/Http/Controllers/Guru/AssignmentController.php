<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Schedule;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Menampilkan daftar tugas/ujian pada satu jadwal pelajaran tertentu.
     */
    public function index(Schedule $schedule)
    {
        $schedule->load(['classroom', 'subject']);
        $assignments = $schedule->assignments()->orderBy('created_at', 'desc')->paginate(15);

        return view('guru.pembelajaran.tugas.index', compact('schedule', 'assignments'));
    }

    public function create(Schedule $schedule)
    {
        return view('guru.pembelajaran.tugas.create', compact('schedule'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:Tugas,Kuis,Praktik,Proyek,UTS,UAS',
            'due_date'    => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan data penugasan
            $assignment = $schedule->assignments()->create($request->only(['title', 'type', 'due_date', 'description']));

            // 2. Ambil daftar siswa dari kelas terkait
            $siswas = $schedule->classroom->siswas;

            // 3. Generate form nilai kosong (null) untuk setiap siswa
            $grades = [];
            $now = now();
            foreach ($siswas as $siswa) {
                $grades[] = [
                    'assignment_id' => $assignment->id,
                    'siswa_id'      => $siswa->id,
                    'score'         => null, // Nilai default kosong
                    'feedback'      => null,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }

            if (!empty($grades)) {
                Grade::insert($grades);
            }

            DB::commit();
            return redirect()->route('guru.schedule.assignment.show', [$schedule->id, $assignment->id])
                ->with('success', 'Tugas/Ujian berhasil dibuat. Anda dapat mulai memasukkan nilai.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat tugas: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail tugas beserta form input nilai siswa.
     */
    public function show(Schedule $schedule, Assignment $assignment)
    {
        $assignment->load(['grades.siswa.user']);

        return view('guru.pembelajaran.tugas.show', compact('schedule', 'assignment'));
    }

    public function edit(Schedule $schedule, Assignment $assignment)
    {
        return view('guru.pembelajaran.tugas.edit', compact('schedule', 'assignment'));
    }

    public function update(Request $request, Schedule $schedule, Assignment $assignment)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:Tugas,Kuis,Praktik,Proyek,UTS,UAS',
            'due_date'    => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        try {
            $assignment->update($request->only(['title', 'type', 'due_date', 'description']));
            return redirect()->route('guru.schedule.assignment.index', $schedule->id)->with('success', 'Detail evaluasi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data tugas.');
        }
    }

    public function destroy(Schedule $schedule, Assignment $assignment)
    {
        try {
            $assignment->delete(); // Cascade delete akan menghapus baris tabel grades
            return redirect()->route('guru.schedule.assignment.index', $schedule->id)->with('success', 'Tugas beserta seluruh data nilainya berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus tugas.');
        }
    }

    /**
     * Fungsi khusus untuk menyimpan daftar nilai dari guru (Bulk Update).
     */
    public function updateGrades(Request $request, Schedule $schedule, Assignment $assignment)
    {
        $request->validate([
            'grades'             => 'required|array',
            'grades.*.id'        => 'required|exists:grades,id',
            'grades.*.score'     => 'nullable|numeric|min:0|max:100', // Skala 0-100
            'grades.*.feedback'  => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Loop untuk mengupdate setiap baris nilai
            foreach ($request->grades as $data) {
                Grade::where('id', $data['id'])
                    ->where('assignment_id', $assignment->id) // Pengamanan
                    ->update([
                        'score'    => $data['score'],
                        'feedback' => $data['feedback'],
                    ]);
            }

            DB::commit();
            return back()->with('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan nilai.');
        }
    }
}
