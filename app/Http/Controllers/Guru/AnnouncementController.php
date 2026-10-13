<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\Schedule;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Simpan pengumuman baru dari guru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'priority'    => 'required|in:NORMAL,IMPORTANT',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Pastikan guru hanya bisa bikin pengumuman untuk jadwal yang dia ampu
        $schedule = Schedule::where('id', $request->schedule_id)
            ->where('guru_id', $guru->id)
            ->first();

        if (!$schedule) {
            return back()->with('error', 'Anda tidak berhak membuat pengumuman untuk kelas ini.');
        }

        try {
            Announcement::create([
                'schedule_id' => $schedule->id,
                'user_id'     => $user->id,
                'title'       => $request->title,
                'content'     => $request->content,
                'priority'    => $request->priority,
                'start_at'    => now(),
            ]);

            return back()->with('success', 'Pengumuman berhasil dikirim ke siswa.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim pengumuman: ' . $e->getMessage());
        }
    }

    /**
     * Hapus pengumuman (hanya pembuat).
     */
    public function destroy(Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus pengumuman ini.');
        }

        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Tandai pengumuman sebagai sudah dibaca oleh siswa.
     */
    public function markAsRead(Announcement $announcement)
    {
        AnnouncementRead::firstOrCreate(
            [
                'announcement_id' => $announcement->id,
                'user_id'         => Auth::id(),
            ],
            [
                'read_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }
}
