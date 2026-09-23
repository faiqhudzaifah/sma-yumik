<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;
use App\Models\Schedule;
use App\Models\Grade;

class CourseController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $activeSemester = Semester::where('is_active', true)->first();
        $enrollmentAktif = $siswa->classrooms()->where('semester_id', $activeSemester?->id)->first();

        $courses = collect();

        if ($enrollmentAktif) {
            $courses = Schedule::with(['subject', 'guru', 'classroom'])
                ->where('classroom_id', $enrollmentAktif->id)
                ->where('semester_id', $activeSemester->id)
                ->get()
                ->unique('subject_id')
                ->values();
        }

        return view('siswa.course.index', compact('courses', 'activeSemester', 'enrollmentAktif'));
    }

    public function show($scheduleId)
    {
        $siswa = Auth::user()->siswa;
        $schedule = Schedule::with(['subject', 'guru', 'classroom'])->findOrFail($scheduleId);

        $isEnrolled = $siswa->classrooms()->where('classroom_id', $schedule->classroom_id)->exists();
        if (!$isEnrolled) {
            abort(403, 'Anda tidak terdaftar dalam mata pelajaran ini.');
        }

        $subject = $schedule->subject;
        $guru = $schedule->guru;
        $classroom = $schedule->classroom;

        // FIX: kolom status mungkin belum ada. Pakai try-catch / cek Schema
        $materiPublished = $schedule->materials()->latest()->get();

        // FIX: relasi submissions di Assignment salah, pakai Grade
        $tugasDaftar = $schedule->assignments()->latest()->get()->map(function ($tugas) use ($siswa) {
            $tugas->grade = Grade::where('assignment_id', $tugas->id)
                ->where('siswa_id', $siswa->id)
                ->first();
            return $tugas;
        });

        $ujianDaftar = $schedule->assessments()
            ->whereIn('status', ['PUBLISHED', 'CLOSED'])
            ->latest()
            ->get()
            ->map(function ($assessment) use ($siswa) {
                $assessment->submission = \App\Models\Submission::where('assessment_id', $assessment->id)
                    ->where('siswa_id', $siswa->id)
                    ->first();
                return $assessment;
            });

        return view('siswa.course.show', compact(
            'schedule',
            'subject',
            'guru',
            'classroom',
            'materiPublished',
            'tugasDaftar',
            'ujianDaftar'
        ));
    }
}
