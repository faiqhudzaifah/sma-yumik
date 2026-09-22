@extends('layouts.app')

@section('title', 'Detail Ujian - ' . $assessment->title)

@section('content')
<div class="space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.schedule.meeting.index', $schedule->id) }}">{{ $schedule->subject->name }}</x-breadcrumb-item>
        <x-breadcrumb-item href="#">CBT & Ujian</x-breadcrumb-item>
        <x-breadcrumb-item active="true">{{ $assessment->title }}</x-breadcrumb-item>
    </x-breadcrumbs>

    <!-- STATUS BANNER -->
    <x-card class="!p-0 overflow-hidden border-2 {{ $assessment->status == 'PUBLISHED' ? 'border-yumik-primary/50' : 'border-amber-500/50' }}">
        <div class="bg-slate-50 dark:bg-black/20 p-6 lg:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <x-badge color="{{ $assessment->status == 'PUBLISHED' ? 'primary' : 'neutral' }}" class="{{ $assessment->status == 'DRAFT' ? '!bg-amber-100 !text-amber-700' : '' }}">
                        STATUS: {{ $assessment->status }}
                    </x-badge>
                    <x-badge color="neutral">{{ $assessment->type }}</x-badge>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">{{ $assessment->title }}</h1>
                <p class="text-sm text-slate-500 font-medium mt-2">
                    <i class="ph-fill ph-timer"></i> Durasi: {{ $assessment->duration }} Menit • Batas Percobaan: {{ $assessment->attempt_limit }}x
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('guru.schedule.assessment.edit', [$schedule->id, $assessment->id]) }}">
                    <x-button variant="secondary" class="!px-4"><i class="ph-bold ph-gear"></i> Pengaturan</x-button>
                </a>
                @if($assessment->status == 'DRAFT')
                    <!-- Gunakan form untuk mengubah status ke PUBLISHED jika diperlukan -->
                    <x-button variant="primary" class="!px-4"><i class="ph-bold ph-rocket-launch"></i> Publikasikan</x-button>
                @else
                    <a href="{{ route('guru.schedule.assessment.grading.index', [$schedule->id, $assessment->id]) }}">
                        <x-button variant="primary" class="!px-4 bg-amber-500 hover:bg-amber-600"><i class="ph-bold ph-check-square-offset"></i> Koreksi Ujian</x-button>
                    </a>
                @endif
            </div>
        </div>
    </x-card>

    <!-- BANK SOAL (QUESTIONS) -->
    <div class="space-y-4">
        <div class="flex justify-between items-end px-2">
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Bank Soal</h2>
                <p class="text-sm text-slate-500">Total Soal: {{ $assessment->questions->count() }} • Total Poin: {{ $assessment->questions->sum('points') }}</p>
            </div>
            
            @if($assessment->status == 'DRAFT')
                <x-button variant="primary" class="!px-4 !py-2 !text-sm shadow-md" onclick="document.getElementById('modal-tambah-soal').setAttribute('data-state', 'open'); document.getElementById('modal-tambah-soal').classList.remove('hidden')">
                    <i class="ph-bold ph-plus"></i> Tambah Soal
                </x-button>
            @endif
        </div>

        @if($assessment->questions->isEmpty())
            <x-card class="p-12 text-center">
                <x-empty-state icon="files" title="Kuis Masih Kosong" description="Klik tombol tambah soal di atas untuk mulai membuat pertanyaan." />
            </x-card>
        @else
            @foreach($assessment->questions as $index => $question)
                <x-card class="p-6 relative group overflow-hidden border-l-4 border-l-yumik-primary">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 rounded-lg bg-yumik-primary/10 text-yumik-primary font-black flex items-center justify-center">{{ $index + 1 }}</span>
                                <x-badge color="neutral" class="!text-[10px]">{{ $question->type }}</x-badge>
                                <span class="text-xs font-bold text-slate-400">{{ number_format($question->points, 0) }} Poin</span>
                            </div>
                            
                            <div class="text-slate-800 dark:text-white font-medium text-lg mb-4">
                                {!! nl2br(e($question->question_text)) !!}
                            </div>

                            @if($question->type == 'MULTIPLE_CHOICE')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                                    @foreach($question->options as $optIndex => $option)
                                        <div class="p-3 rounded-xl border-2 {{ $option->is_correct ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-500/10' : 'border-slate-100 dark:border-white/5 bg-slate-50 dark:bg-black/20' }} flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-full {{ $option->is_correct ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center text-xs font-bold">{{ chr(65 + $optIndex) }}</span>
                                            <span class="text-sm font-medium {{ $option->is_correct ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300' }}">{{ $option->option_text }}</span>
                                            
                                            @if($option->is_correct)
                                                <i class="ph-fill ph-check-circle text-emerald-500 ml-auto text-lg"></i>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Tombol Hapus (Hanya muncul saat DRAFT) -->
                        @if($assessment->status == 'DRAFT')
                            <form action="{{ route('guru.schedule.assessment.question.destroy', [$schedule->id, $assessment->id, $question->id]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-[1rem] bg-red-50 dark:bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors" onclick="return confirm('Hapus soal ini?')">
                                    <i class="ph-bold ph-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </x-card>
            @endforeach
        @endif
    </div>
</div>

<!-- Modal Tambah Soal (Implementasi x-modal) -->
<!-- Memanggil method QuestionController@store -->
<x-modal id="modal-tambah-soal" title="Tambah Soal Baru">
    <form action="{{ route('guru.schedule.assessment.question.store', [$schedule->id, $assessment->id]) }}" method="POST">
        @csrf
        <!-- Form Fields untuk QuestionController... -->
        <p class="text-sm text-slate-500 mb-4">Silakan gunakan fitur form dinamis untuk menambahkan soal tipe Pilihan Ganda atau Esai.</p>
        <x-button type="submit" variant="primary" class="w-full mt-4">Simpan Soal</x-button>
    </form>
</x-modal>
@endsection