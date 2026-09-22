@extends('layouts.app')

@section('title', 'Koreksi Ujian - ' . $submission->siswa->user->name)

@section('content')
<div class="space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="#">{{ $assessment->title }}</x-breadcrumb-item>
        <x-breadcrumb-item href="{{ route('guru.schedule.assessment.grading.index', [$schedule->id, $assessment->id]) }}">Daftar Pengumpulan</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Koreksi Lembar Jawaban</x-breadcrumb-item>
    </x-breadcrumbs>

    <!-- HEADER IDENTITAS SISWA -->
    <x-card class="bg-gradient-to-r from-slate-900 to-slate-800 border-none p-6 text-white shadow-xl">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-4">
                <x-avatar src="{{ $submission->siswa->photo ?? '' }}" size="lg" />
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">{{ $submission->siswa->user->name }}</h2>
                    <p class="text-slate-400 text-sm mt-1">NISN: {{ $submission->siswa->nisn }} • Percobaan ke-{{ $submission->attempt_number }}</p>
                </div>
            </div>
            
            <div class="text-right">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Skor Saat Ini</p>
                <div class="text-4xl font-black text-yumik-primary">{{ number_format($submission->score, 1) }}</div>
            </div>
        </div>
    </x-card>

    <!-- FORM KOREKSI MANUAL -->
    <!-- Terhubung ke AssessmentGradingController@updateScore -->
    <form action="{{ route('guru.schedule.assessment.grading.update', [$schedule->id, $assessment->id, $submission->id]) }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="flex justify-between items-end px-2">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Lembar Jawaban Siswa</h3>
            <x-button type="submit" variant="primary" class="shadow-lg shadow-yumik-primary/30">
                <i class="ph-bold ph-floppy-disk"></i> Simpan Nilai Koreksi
            </x-button>
        </div>

        @foreach($submission->answers as $index => $answer)
            @php
                $question = $answer->question;
                $isEssay = $question->type == 'ESSAY';
                // Tentukan warna card berdasarkan jawaban (Benar/Salah/Perlu Koreksi)
                $cardBorder = '';
                if (!$isEssay) {
                    $cardBorder = $answer->is_correct ? 'border-emerald-500' : 'border-red-500';
                } else {
                    $cardBorder = $answer->points_awarded === null ? 'border-amber-500' : 'border-yumik-primary';
                }
            @endphp

            <x-card class="p-6 lg:p-8 border-l-4 {{ $cardBorder }}">
                <!-- Hidden input untuk ID jawaban agar updateScore bisa mendeteksinya -->
                <input type="hidden" name="answers[{{ $index }}][id]" value="{{ $answer->id }}">

                <div class="flex justify-between items-start gap-4 mb-4">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-white font-black flex items-center justify-center shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <x-badge color="neutral" class="!text-[10px] mb-2">{{ $question->type }}</x-badge>
                            <div class="text-slate-800 dark:text-white font-medium text-lg">
                                {!! nl2br(e($question->question_text)) !!}
                            </div>
                        </div>
                    </div>
                    
                    <!-- INPUT NILAI MANUAL (Khusus Essay) / TAMPILAN NILAI OTOMATIS -->
                    <div class="shrink-0 text-right">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Poin</p>
                        @if($isEssay)
                            <div class="flex items-center gap-2">
                                <input type="number" step="0.1" max="{{ $question->points }}" name="answers[{{ $index }}][points_awarded]" value="{{ $answer->points_awarded ?? 0 }}" class="w-20 py-2 text-center rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-500/30 font-bold text-amber-700 dark:text-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <span class="text-sm font-bold text-slate-400">/ {{ number_format($question->points, 0) }}</span>
                            </div>
                        @else
                            <div class="text-xl font-black {{ $answer->is_correct ? 'text-emerald-500' : 'text-red-500' }}">
                                {{ $answer->points_awarded ?? 0 }} <span class="text-sm text-slate-400">/ {{ number_format($question->points, 0) }}</span>
                            </div>
                            <!-- Berikan hidden input agar PG tetap terbawa di request tanpa error validasi -->
                            <input type="hidden" name="answers[{{ $index }}][points_awarded]" value="{{ $answer->points_awarded }}">
                        @endif
                    </div>
                </div>

                <!-- TAMPILAN JAWABAN SISWA -->
                <div class="ml-14 p-4 rounded-xl bg-slate-50 dark:bg-black/20 border border-slate-100 dark:border-white/5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jawaban Siswa:</p>
                    
                    @if($isEssay)
                        <p class="text-slate-700 dark:text-slate-300 font-medium whitespace-pre-wrap">{{ $answer->answer_text ?? 'Siswa tidak menjawab.' }}</p>
                    @else
                        <!-- Tampilkan teks opsi yang dipilih siswa -->
                        <div class="flex items-center gap-2">
                            <i class="ph-fill {{ $answer->is_correct ? 'ph-check-circle text-emerald-500' : 'ph-x-circle text-red-500' }} text-xl"></i>
                            <span class="text-sm font-bold {{ $answer->is_correct ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400' }}">
                                {{ $answer->option ? $answer->option->option_text : 'Tidak dijawab' }}
                            </span>
                        </div>
                    @endif
                </div>
            </x-card>
        @endforeach
    </form>
</div>
@endsection