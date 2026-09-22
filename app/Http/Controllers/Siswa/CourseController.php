<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;
use App\Models\Schedule;

class CourseController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $activeSemester = Semester::where('is_active', true)->first();
        $enrollmentAktif = $siswa->classrooms()->where('semester_id', $activeSemester->id)->first();

        $courses = collect();
        if ($enrollmentAktif) {
            // Mengambil daftar mata pelajaran unik berdasarkan jadwal kelas
            $courses = Schedule::with(['subject', 'guru'])
                ->where('classroom_id', $enrollmentAktif->id)
                ->where('semester_id', $activeSemester->id)
                ->get()
                ->unique('subject_id');
        }

        return view('siswa.course.index', compact('courses'));
    }

    public function show($scheduleId)
    {
        $siswa = Auth::user()->siswa;
        $schedule = Schedule::with(['subject', 'guru', 'classroom'])->findOrFail($scheduleId);

        // Validasi hak akses (Siswa harus berada di kelas ini)
        $isEnrolled = $siswa->classrooms()->where('classroom_id', $schedule->classroom_id)->exists();
        if (!$isEnrolled) {
            abort(403, 'Anda tidak terdaftar dalam mata pelajaran ini.');
        }

        $subject = $schedule->subject;
        $guru = $schedule->guru;
        $classroom = $schedule->classroom;

        // Ambil Materi Published
        $materiPublished = $schedule->materials()->where('status', 'PUBLISHED')->latest()->get();

        // Ambil Tugas & Submission Siswa
        $tugasDaftar = $schedule->assignments()->latest()->get()->map(function ($tugas) use ($siswa) {
            $tugas->submission = $tugas->submissions()->where('siswa_id', $siswa->id)->first();
            return $tugas;
        });

        return view('siswa.course.show', compact('schedule', 'subject', 'guru', 'classroom', 'materiPublished', 'tugasDaftar'));
    }
}
