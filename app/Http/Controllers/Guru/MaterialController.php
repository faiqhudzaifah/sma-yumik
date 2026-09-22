<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Schedule $schedule)
    {
        $schedule->load(['classroom', 'subject']);
        $materials = $schedule->materials()->orderBy('created_at', 'desc')->paginate(15);

        return view('guru.pembelajaran.materi.index', compact('schedule', 'materials'));
    }

    public function create(Schedule $schedule)
    {
        return view('guru.pembelajaran.materi.create', compact('schedule'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'external_link' => 'nullable|url',
            // Validasi file (maks 10MB, ekstensi dokumen umum)
            'file'          => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:10240',
        ]);

        try {
            $data = $request->only(['title', 'description', 'external_link']);

            // Proses upload file jika ada
            if ($request->hasFile('file')) {
                // Simpan file ke folder storage/app/public/materials
                $path = $request->file('file')->store('materials', 'public');
                $data['file_path'] = $path;
            }

            $schedule->materials()->create($data);

            return redirect()->route('guru.schedule.material.index', $schedule->id)
                ->with('success', 'Materi pembelajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan materi: ' . $e->getMessage());
        }
    }

    public function show(Schedule $schedule, Material $material)
    {
        return view('guru.pembelajaran.materi.show', compact('schedule', 'material'));
    }

    public function edit(Schedule $schedule, Material $material)
    {
        return view('guru.pembelajaran.materi.edit', compact('schedule', 'material'));
    }

    public function update(Request $request, Schedule $schedule, Material $material)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'external_link' => 'nullable|url',
            'file'          => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:10240',
        ]);

        try {
            $data = $request->only(['title', 'description', 'external_link']);

            // Jika guru mengunggah file baru, hapus file lama dan simpan yang baru
            if ($request->hasFile('file')) {
                if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                    Storage::disk('public')->delete($material->file_path);
                }

                $path = $request->file('file')->store('materials', 'public');
                $data['file_path'] = $path;
            }

            $material->update($data);

            return redirect()->route('guru.schedule.material.index', $schedule->id)
                ->with('success', 'Materi pembelajaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui materi.');
        }
    }

    public function destroy(Schedule $schedule, Material $material)
    {
        try {
            // Hapus file fisik dari storage sebelum menghapus data di database
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }

            $material->delete();

            return redirect()->route('guru.schedule.material.index', $schedule->id)
                ->with('success', 'Materi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus materi.');
        }
    }
}
