@extends('layouts.app')

@section('title', 'Ujian & Kuis - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8">

    <x-card class="!bg-gradient-to-r from-purple-500/10 to-transparent border-purple-500/20 p-6 lg:p-8">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-purple-500/30 shrink-0">
                <i class="ph-fill ph-exam text-3xl"></i>
            </div>
            <div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">Ujian & Kuis</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">
                    Total {{ $assessments->count() }} ujian
                    @if($activeSemester) · Semester {{ $activeSemester->type }}@endif
                </p>
            </div>
        </div>
    </x-card>

    @if($assessments->isEmpty())
        <x-card class="!p-0">
            <x-empty-state
                icon="exam"
                title="Belum Ada Ujian"
                description="Ujian akan muncul di sini setelah gurumu mempublikasikannya."
            />
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($assessments as $assessment)
                @php
                    $submission = $assessment->submission;
                    $isDone = $submission && $submission->submitted_at;
                    $isAvailable = $assessment->is_available;
                    $isClosed = $assessment->status === 'CLOSED';
                @endphp

                <a href="{{ route('siswa.cbt.show', $assessment->id) }}" class="block group">
                    <x-card class="!p-6 h-full hover:!shadow-[0_20px_50px_-15px_rgba(139,92,246,0.3)] hover:-translate-y-1 transition-all">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-[1.25rem] bg-purple-100 dark:bg-purple-500/20 text-purple-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="ph-fill ph-exam text-2xl"></i>
                            </div>
                            <x-badge color="{{ $assessment->type === 'EXAM' ? 'accent' : 'neutral' }}">
                                {{ $assessment->type }}
                            </x-badge>
                        </div>

                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2 line-clamp-2 group-hover:text-yumik-primary transition-colors">
                            {{ $assessment->title }}
                        </h3>

                        <p class="text-xs text-slate-500 font-medium mb-5">
                            {{ $assessment->schedule->subject->name ?? '-' }}
                            · {{ $assessment->duration }} menit
                        </p>

                        <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-white/5">
                            @if($isDone)
                                <span class="text-xs font-bold text-green-500 flex items-center gap-1">
                                    <i class="ph-fill ph-check-circle"></i> Selesai
                                </span>
                                @if($submission->score !== null)
                                    <span class="text-lg font-black text-yumik-primary">{{ $submission->score }}</span>
                                @endif
                            @elseif($isClosed)
                                <span class="text-xs font-bold text-slate-400 flex items-center gap-1">
                                    <i class="ph-fill ph-lock"></i> Ditutup
                                </span>
                            @elseif($isAvailable)
                                <span class="text-xs font-bold text-green-500 flex items-center gap-1">
                                    <i class="ph-fill ph-play-circle"></i> Tersedia
                                </span>
                                <span class="text-[10px] font-bold text-slate-400">
                                    s/d {{ $assessment->end_at->translatedFormat('d M, H:i') }}
                                </span>
                            @else
                                <span class="text-xs font-bold text-amber-500 flex items-center gap-1">
                                    <i class="ph-fill ph-clock"></i> Belum Dibuka
                                </span>
                                <span class="text-[10px] font-bold text-slate-400">
                                    {{ $assessment->start_at ? $assessment->start_at->translatedFormat('d M, H:i') : '-' }}
                                </span>
                            @endif
                        </div>
                    </x-card>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection