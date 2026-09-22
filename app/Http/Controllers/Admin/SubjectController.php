<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Semester;
use App\Models\Guru;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();
        $query = Subject::with(['semester.academicYear', 'guru.user']);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        }

        // Filter Semester Aktif
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        } else {
            $activeSemester = $semesters->firstWhere('is_active', true);
            if ($activeSemester) {
                $query->where('semester_id', $activeSemester->id);
            }
        }

        $subjects = $query->latest()->paginate(15)->withQueryString();

        return view('admin.akademik.mapel.index', compact('subjects', 'semesters'));
    }

    public function create()
    {
        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();
        $gurus = Guru::with('user')->get();

        return view('admin.akademik.mapel.create', compact('semesters', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'guru_id'     => 'nullable|exists:gurus,id',
            'name'        => 'required|string|max:100',
            'code'        => 'nullable|string|max:20',
        ]);

        try {
            Subject::create($request->all());
            return redirect()->route('admin.mapel.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit(Subject $mapel)
    {
        $semesters = Semester::with('academicYear')->orderByDesc('id')->get();
        $gurus = Guru::with('user')->get();

        return view('admin.akademik.mapel.edit', compact('mapel', 'semesters', 'gurus'));
    }

    public function update(Request $request, Subject $mapel)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'guru_id'     => 'nullable|exists:gurus,id',
            'name'        => 'required|string|max:100',
            'code'        => 'nullable|string|max:20',
        ]);

        try {
            $mapel->update($request->all());
            return redirect()->route('admin.mapel.index')->with('success', 'Data Mata Pelajaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(Subject $mapel)
    {
        try {
            $mapel->delete();
            return redirect()->route('admin.mapel.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data karena terkait dengan modul lain.');
        }
    }
}
