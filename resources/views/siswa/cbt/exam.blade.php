@extends('layouts.app')

@section('title', 'Mengerjakan: ' . $assessment->title)

@section('content')
<div x-data="cbtEngine({{ $questions->count() }}, {{ $assessment->duration }})" x-init="init()" class="space-y-6">

    {{-- TIMER STICKY --}}
    <div class="sticky top-4 z-40">
        <x-card class="!p-4 !bg-gradient-to-r from-purple-500/95 to-indigo-500/95 backdrop-blur-xl border-purple-500/50 shadow-2xl">
            <div class="flex items-center justify-between gap-4 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="ph-fill ph-exam text-xl"></i>
                    </div>
                    <div class="hidden md:block">
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">Ujian</p>
                        <p class="text-sm font-bold truncate max-w-xs">{{ $assessment->title }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">Waktu Tersisa</p>
                        <p class="text-2xl font-black tabular-nums" x-text="formatTime(timeLeft)"></p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"
                         :class="timeLeft < 300 ? 'animate-pulse !bg-red-500' : ''">
                        <i class="ph-fill ph-timer text-xl"></i>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    {{-- NAVIGASI NOMOR --}}
    <x-card class="p-5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Navigasi Soal</p>
        <div class="flex flex-wrap gap-2">
            @foreach($questions as $index => $q)
                <button type="button"
                        @click="currentIndex = {{ $index }}"
                        :class="currentIndex === {{ $index }} ? 'bg-yumik-primary text-white shadow-lg shadow-yumik-primary/30 scale-110' : (answers[{{ $q->id }}] ? 'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400' : 'bg-slate-100 dark:bg-white/5 text-slate-500')"
                        class="w-10 h-10 rounded-xl font-bold text-sm transition-all hover:scale-105">
                    {{ $index + 1 }}
                </button>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-4 text-xs font-bold">
            <div class="flex items-center gap-1.5 text-slate-500">
                <span class="w-3 h-3 rounded bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10"></span>
                Belum dijawab
            </div>
            <div class="flex items-center gap-1.5 text-green-500">
                <span class="w-3 h-3 rounded bg-green-100 dark:bg-green-500/20"></span>
                Sudah dijawab
            </div>
        </div>
    </x-card>

    {{-- FORM SOAL --}}
    <form id="cbt-form" action="{{ route('siswa.cbt.submit', $assessment->id) }}" method="POST" @submit.prevent="confirmSubmit($event)">
        @csrf

        @foreach($questions as $index => $question)
            <div x-show="currentIndex === {{ $index }}" x-cloak>
                <x-card class="p-6 lg:p-8">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-yumik-primary text-white flex items-center justify-center font-black text-lg shrink-0 shadow-lg shadow-yumik-primary/30">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <x-badge color="neutral">{{ $question->type }}</x-badge>
                                <x-badge color="accent">{{ $question->points }} poin</x-badge>
                            </div>
                            <div class="text-base text-slate-800 dark:text-white leading-relaxed whitespace-pre-line font-medium">
                                {{ $question->question_text }}
                            </div>
                        </div>
                    </div>

                    {{-- JAWABAN --}}
                    @if(in_array($question->type, ['MULTIPLE_CHOICE', 'TRUE_FALSE']))
                        <div class="space-y-3 mt-6">
                            @foreach($question->options as $optIndex => $option)
                                @php
                                    $savedAnswer = $answers[$question->id] ?? null;
                                    $isChecked = $savedAnswer && $savedAnswer->option_id == $option->id;
                                @endphp
                                <label class="block cursor-pointer">
                                    <input type="radio"
                                           name="answers[{{ $question->id }}]"
                                           value="{{ $option->id }}"
                                           @change="answers[{{ $question->id }}] = {{ $option->id }}"
                                           {{ $isChecked ? 'checked' : '' }}
                                           class="peer sr-only">
                                    <div class="flex items-center gap-4 p-4 rounded-2xl border-2 border-slate-200 dark:border-white/10 hover:border-yumik-primary/50 peer-checked:border-yumik-primary peer-checked:bg-yumik-primary/5 transition-all">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-sm font-black text-slate-500 peer-checked:bg-yumik-primary peer-checked:text-white transition-all shrink-0">
                                            {{ chr(65 + $optIndex) }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $option->option_text }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        @php
                            $savedAnswer = $answers[$question->id] ?? null;
                        @endphp
                        <div class="mt-6">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Jawaban Esai</label>
                            <textarea name="answers[{{ $question->id }}]"
                                      @input="answers[{{ $question->id }}] = $event.target.value"
                                      rows="8"
                                      placeholder="Tulis jawabanmu di sini..."
                                      class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all resize-none">{{ $savedAnswer->answer_text ?? '' }}</textarea>
                        </div>
                    @endif
                </x-card>
            </div>
        @endforeach

        {{-- NAVIGASI PREV/NEXT --}}
        <div class="flex items-center justify-between gap-4">
            <button type="button"
                    @click="currentIndex = Math.max(0, currentIndex - 1)"
                    :disabled="currentIndex === 0"
                    :class="currentIndex === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:scale-[1.02]'"
                    class="px-6 py-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-200 font-bold transition-all inline-flex items-center gap-2">
                <i class="ph-bold ph-caret-left"></i> Sebelumnya
            </button>

            <div x-show="currentIndex < totalQuestions - 1">
                <button type="button"
                        @click="currentIndex++"
                        class="px-6 py-4 rounded-2xl bg-yumik-primary hover:bg-green-600 text-white font-bold shadow-lg shadow-yumik-primary/30 transition-all hover:scale-[1.02] inline-flex items-center gap-2">
                    Selanjutnya <i class="ph-bold ph-caret-right"></i>
                </button>
            </div>

            <div x-show="currentIndex === totalQuestions - 1">
                <button type="submit"
                        class="px-6 py-4 rounded-2xl bg-green-500 hover:bg-green-600 text-white font-bold shadow-lg shadow-green-500/30 transition-all hover:scale-[1.02] inline-flex items-center gap-2">
                    <i class="ph-bold ph-paper-plane-tilt"></i> Kumpulkan Ujian
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')

<script>
function cbtEngine(totalQuestions, durationMinutes) {
    return {
        currentIndex: 0,
        totalQuestions: totalQuestions,
        timeLeft: durationMinutes * 60,
        answers: {},
        timer: null,

        init() {
            // Restore jawaban dari server (jika ada)
            @foreach($questions as $question)
                @php
                    $savedAnswer = $answers[$question->id] ?? null;
                @endphp
                @if($savedAnswer)
                    @if($savedAnswer->option_id)
                        this.answers[{{ $question->id }}] = {{ $savedAnswer->option_id }};
                    @elseif($savedAnswer->answer_text)
                        this.answers[{{ $question->id }}] = @json($savedAnswer->answer_text);
                    @endif
                @endif
            @endforeach

            // Start timer
            this.timer = setInterval(() => {
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                } else {
                    clearInterval(this.timer);
                    document.getElementById('cbt-form').submit();
                }
            }, 1000);
        },

        formatTime(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            if (h > 0) {
                return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            }
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        },

        confirmSubmit(e) {
            const unanswered = this.totalQuestions - Object.keys(this.answers).length;
            let msg = 'Kumpulkan ujian sekarang?';
            if (unanswered > 0) {
                msg = `Masih ada ${unanswered} soal yang belum dijawab. Yakin ingin mengumpulkan?`;
            }
            if (confirm(msg)) {
                clearInterval(this.timer);
                e.target.submit();
            }
        }
    }
}
</script>
@endpush
@endsection