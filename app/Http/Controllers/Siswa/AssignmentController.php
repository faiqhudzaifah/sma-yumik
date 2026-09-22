<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function show(Assignment $assignment)
    {
        $siswa = Auth::user()->siswa;

        // Ambil status pengumpulan siswa ini (karena form nilai kosong sudah digenerate guru saat tugas dibuat)
        $grade = Grade::where('assignment_id', $assignment->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        return view('siswa.tugas.show', compact('assignment', 'grade'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $request->validate([
            'submitted_text' => 'nullable|string',
            'file'           => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar,jpg,png|max:10240',
        ]);

        $siswa = Auth::user()->siswa;
        $grade = Grade::where('assignment_id', $assignment->id)->where('siswa_id', $siswa->id)->firstOrFail();

        // Jangan izinkan submit ulang jika sudah dinilai guru
        if ($grade->score !== null) {
            return back()->with('error', 'Tugas ini sudah dinilai dan tidak dapat diubah lagi.');
        }

        try {
            $data = [
                'submitted_text' => $request->submitted_text,
                'submitted_at'   => now(),
            ];

            if ($request->hasFile('file')) {
                // Hapus file lama jika siswa mengunggah ulang
                if ($grade->file_path && Storage::disk('public')->exists($grade->file_path)) {
                    Storage::disk('public')->delete($grade->file_path);
                }

                $data['file_path'] = $request->file('file')->store('submissions', 'public');
            }

            $grade->update($data);

            return back()->with('success', 'Tugas berhasil dikumpulkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengumpulkan tugas.');
        }
    }
}
