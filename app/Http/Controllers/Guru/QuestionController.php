<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function store(Request $request, Schedule $schedule, Assessment $assessment)
    {
        $request->validate([
            'type'          => 'required|in:MULTIPLE_CHOICE,ESSAY,TRUE_FALSE',
            'question_text' => 'required|string',
            'points'        => 'required|numeric|min:0.1',

            // Validasi khusus Pilihan Ganda
            'options'              => 'required_if:type,MULTIPLE_CHOICE|array|min:2',
            'options.*.text'       => 'required_with:options|string',
            'options.*.is_correct' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $question = $assessment->questions()->create([
                'type'          => $request->type,
                'question_text' => $request->question_text,
                'points'        => $request->points,
            ]);

            if ($request->type === 'MULTIPLE_CHOICE' && $request->has('options')) {
                foreach ($request->options as $option) {
                    $question->options()->create([
                        'option_text' => $option['text'],
                        'is_correct'  => $option['is_correct'] ?? false,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Soal berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan soal.');
        }
    }

    public function destroy(Schedule $schedule, Assessment $assessment, Question $question)
    {
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }
}
