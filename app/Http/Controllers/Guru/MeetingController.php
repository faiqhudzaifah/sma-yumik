<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    /**
     * Menampilkan daftar pertemuan pada satu jadwal pelajaran tertentu.
     */
    public function index(Schedule $schedule)
    {
        // Pastikan relasi terkait dimuat untuk efisiensi query
        $schedule->load(['classroom', 'subject']);

        $meetings = $schedule->meetings()->orderBy('meeting_number', 'asc')->paginate(15);

        return view('guru.pembelajaran.pertemuan.index', compact('schedule', 'meetings'));
    }

    public function create(Schedule $schedule)
    {
        // Mencari nomor pertemuan terakhir untuk di-auto-increment di form
        $lastMeeting = $schedule->meetings()->orderBy('meeting_number', 'desc')->first();
        $nextMeetingNumber = $lastMeeting ? $lastMeeting->meeting_number + 1 : 1;

        return view('guru.pembelajaran.pertemuan.create', compact('schedule', 'nextMeetingNumber'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        $request->validate([
            'date'           => 'required|date',
            'meeting_number' => 'required|integer|min:1',
            'topic'          => 'required|string|max:255',
            'description'    => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan data pertemuan
            $meeting = $schedule->meetings()->create($request->only(['date', 'meeting_number', 'topic', 'description']));

            // 2. Ambil daftar siswa yang terdaftar di kelas (Rombel) tersebut
            $siswas = $schedule->classroom->siswas;

            // 3. Generate template presensi otomatis dengan status 'Hadir'
            $attendances = [];
            $now = now();
            foreach ($siswas as $siswa) {
                $attendances[] = [
                    'meeting_id' => $meeting->id,
                    'siswa_id'   => $siswa->id,
                    'status'     => 'Hadir',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Insert massal agar jauh lebih cepat dan ringan untuk database
            if (!empty($attendances)) {
                Attendance::insert($attendances);
            }

            DB::commit();
            return redirect()->route('guru.schedule.meeting.show', [$schedule->id, $meeting->id])
                ->with('success', 'Pertemuan berhasil dibuat. Silakan periksa daftar presensi siswa.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat pertemuan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail pertemuan sekaligus form daftar presensi siswa.
     */
    public function show(Schedule $schedule, Meeting $meeting)
    {
        // Memuat presensi beserta data siswa dan user-nya
        $meeting->load(['attendances.siswa.user']);

        return view('guru.pembelajaran.pertemuan.show', compact('schedule', 'meeting'));
    }

    public function edit(Schedule $schedule, Meeting $meeting)
    {
        return view('guru.pembelajaran.pertemuan.edit', compact('schedule', 'meeting'));
    }

    public function update(Request $request, Schedule $schedule, Meeting $meeting)
    {
        $request->validate([
            'date'           => 'required|date',
            'meeting_number' => 'required|integer|min:1',
            'topic'          => 'required|string|max:255',
            'description'    => 'nullable|string'
        ]);

        try {
            $meeting->update($request->only(['date', 'meeting_number', 'topic', 'description']));
            return redirect()->route('guru.schedule.meeting.index', $schedule->id)->with('success', 'Data pertemuan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui pertemuan.');
        }
    }

    public function destroy(Schedule $schedule, Meeting $meeting)
    {
        try {
            $meeting->delete(); // Akan otomatis menghapus child di tabel attendances karena cascadeOnDelete
            return redirect()->route('guru.schedule.meeting.index', $schedule->id)->with('success', 'Pertemuan dan seluruh data presensinya berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus pertemuan.');
        }
    }

    /**
     * Fungsi khusus untuk menyimpan perubahan data presensi (Bulk Update).
     */
    public function updateAttendances(Request $request, Schedule $schedule, Meeting $meeting)
    {
        $request->validate([
            'attendances'          => 'required|array',
            'attendances.*.id'     => 'required|exists:attendances,id',
            'attendances.*.status' => 'required|in:Hadir,Sakit,Izin,Alpa',
            'attendances.*.notes'  => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Loop untuk mengupdate setiap baris presensi yang diubah oleh guru
            foreach ($request->attendances as $data) {
                Attendance::where('id', $data['id'])
                    ->where('meeting_id', $meeting->id) // Pengamanan ekstra
                    ->update([
                        'status' => $data['status'],
                        'notes'  => $data['notes'],
                    ]);
            }

            DB::commit();
            return back()->with('success', 'Data presensi siswa berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan presensi.');
        }
    }
}
