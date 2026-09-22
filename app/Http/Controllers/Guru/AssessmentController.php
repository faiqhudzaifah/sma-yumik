<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Schedule;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Schedule $schedule)
    {
        $schedule->load(['classroom', 'subject']);
        $assessments = $schedule->assessments()->orderBy('created_at', 'desc')->paginate(15);

        return view('guru.pembelajaran.cbt.index', compact('schedule', 'assessments'));
    }

    public function create(Schedule $schedule)
    {
        return view('guru.pembelajaran.cbt.create', compact('schedule'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'type'              => 'required|in:QUIZ,EXAM',
            'description'       => 'nullable|string',
            'start_at'          => 'nullable|date',
            'end_at'            => 'nullable|date|after_or_equal:start_at',
            'duration'          => 'required|integer|min:1',
            'attempt_limit'     => 'required|integer|min:1',
            'shuffle_questions' => 'boolean',
            'status'            => 'required|in:DRAFT,PUBLISHED,CLOSED'
        ]);

        try {
            $schedule->assessments()->create($request->all());
            return redirect()->route('guru.schedule.assessment.index', $schedule->id)
                ->with('success', 'Ujian/Kuis berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat ujian.');
        }
    }

    public function show(Schedule $schedule, Assessment $assessment)
    {
        // Memuat relasi soal dan opsinya untuk ditampilkan di halaman detail/preview
        $assessment->load(['questions.options']);
        return view('guru.pembelajaran.cbt.show', compact('schedule', 'assessment'));
    }

    public function edit(Schedule $schedule, Assessment $assessment)
    {
        return view('guru.pembelajaran.cbt.edit', compact('schedule', 'assessment'));
    }

    public function update(Request $request, Schedule $schedule, Assessment $assessment)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'type'              => 'required|in:QUIZ,EXAM',
            'start_at'          => 'nullable|date',
            'end_at'            => 'nullable|date|after_or_equal:start_at',
            'duration'          => 'required|integer|min:1',
            'attempt_limit'     => 'required|integer|min:1',
            'shuffle_questions' => 'boolean',
            'status'            => 'required|in:DRAFT,PUBLISHED,CLOSED'
        ]);

        $assessment->update($request->all());
        return redirect()->route('guru.schedule.assessment.show', [$schedule->id, $assessment->id])
            ->with('success', 'Pengaturan ujian diperbarui.');
    }

    public function destroy(Schedule $schedule, Assessment $assessment)
    {
        $assessment->delete();
        return redirect()->route('guru.schedule.assessment.index', $schedule->id)
            ->with('success', 'Ujian beserta seluruh soal dan nilainya berhasil dihapus.');
    }
}
