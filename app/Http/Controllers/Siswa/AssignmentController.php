<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $activeSemester = Semester::where('is_active', true)->first();
        $classroom = $siswa->classrooms()->where('semester_id', $activeSemester?->id)->first();

        $assignments = collect();

        if ($classroom) {
            $assignments = Assignment::with(['schedule.subject', 'schedule.guru'])
                ->whereHas('schedule', fn($q) => $q->where('classroom_id', $classroom->id))
                ->orderByDesc('due_date')
                ->get()
                ->map(function ($assignment) use ($siswa) {
                    $grade = Grade::where('assignment_id', $assignment->id)
                        ->where('siswa_id', $siswa->id)
                        ->first();

                    // Status: 'belum' (belum submit), 'submitted' (sudah submit, belum nilai),
                    //         'graded' (sudah nilai), 'late' (lewat due date & belum submit)
                    if ($grade && $grade->submitted_at) {
                        $assignment->status = $grade->score !== null ? 'graded' : 'submitted';
                    } else {
                        $assignment->status = $assignment->due_date && $assignment->due_date->isPast() ? 'late' : 'belum';
                    }

                    $assignment->grade = $grade;
                    return $assignment;
                });
        }

        return view('siswa.tugas.index', compact('assignments', 'activeSemester'));
    }

    public function show(Assignment $assignment)
    {
        $siswa = Auth::user()->siswa;

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
        $grade = Grade::where('assignment_id', $assignment->id)
            ->where('siswa_id', $siswa->id)
            ->firstOrFail();

        if ($grade->score !== null) {
            return back()->with('error', 'Tugas ini sudah dinilai dan tidak dapat diubah lagi.');
        }

        try {
            $data = [
                'submitted_text' => $request->submitted_text,
                'submitted_at'   => now(),
            ];

            if ($request->hasFile('file')) {
                if ($grade->file_path && Storage::disk('public')->exists($grade->file_path)) {
                    Storage::disk('public')->delete($grade->file_path);
                }

                $data['file_path'] = $request->file('file')->store('submissions', 'public');
            }

            $grade->update($data);

            return back()->with('success', 'Tugas berhasil dikumpulkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengumpulkan tugas: ' . $e->getMessage());
        }
    }
}
