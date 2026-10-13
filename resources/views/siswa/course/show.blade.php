@extends('layouts.app')

@section('title', ($subject->name ?? 'Mata Pelajaran') . ' - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('siswa.course.index') }}" class="hover:text-yumik-primary transition-colors">Mata Pelajaran</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold">{{ $subject->name ?? '-' }}</span>
    </nav>

    {{-- HERO MAPEL --}}
    <x-card class="!bg-gradient-to-br from-yumik-primary/10 to-transparent border-yumik-primary/20 p-6 lg:p-8">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="w-20 h-20 rounded-[1.5rem] bg-gradient-to-br from-yumik-primary to-green-600 flex items-center justify-center text-white shadow-lg shadow-yumik-primary/30 shrink-0">
                <i class="ph-fill ph-book-bookmark text-4xl"></i>
            </div>

            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3 mb-2">
                    <x-badge color="accent">{{ $subject->code ?? 'MAPEL' }}</x-badge>
                    <x-badge color="neutral">Kelas {{ $classroom->name ?? '-' }}</x-badge>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white mb-2">{{ $subject->name ?? '-' }}</h1>
                <p class="text-slate-600 dark:text-slate-400 font-medium flex items-center gap-2">
                    <i class="ph-fill ph-chalkboard-teacher"></i>
                    {{ $guru->name ?? 'Guru' }}
                </p>
            </div>
        </div>
    </x-card>
    {{-- PENGUMUMAN MAPEL INI --}}
@php
    $mapelAnnouncements = \App\Models\Announcement::with(['user'])
        ->where('schedule_id', $schedule->id)
        ->orderByDesc('priority')
        ->orderByDesc('start_at')
        ->take(5)
        ->get()
        ->map(function ($ann) {
            $ann->is_read = \App\Models\AnnouncementRead::where('announcement_id', $ann->id)
                ->where('user_id', auth()->id())
                ->exists();
            return $ann;
        });
@endphp

@if($mapelAnnouncements->isNotEmpty())
    <x-card class="p-6 lg:p-8">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="ph-fill ph-megaphone text-yumik-primary text-xl"></i>
                Pengumuman Mapel Ini
            </h3>
        </div>

        <div class="space-y-3">
            @foreach($mapelAnnouncements as $announcement)
                <x-announcement-card :announcement="$announcement" :showSchedule="false" :allowMarkRead="true" :isRead="$announcement->is_read" />
            @endforeach
        </div>
    </x-card>
@endif
    {{-- GRID: MATERI + TUGAS + UJIAN --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        {{-- KOLOM KIRI: MATERI --}}
        <div class="xl:col-span-2 space-y-8">
            <x-card class="p-6 lg:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[1rem] bg-blue-500/10 flex items-center justify-center text-blue-500">
                            <i class="ph-fill ph-books text-2xl"></i>
                        </div>
                        Materi Pembelajaran
                    </h2>
                    <span class="text-xs font-bold text-slate-400">{{ $materiPublished->count() }} materi</span>
                </div>

                @if($materiPublished->isEmpty())
                    <div class="text-center py-12">
                        <i class="ph-fill ph-folder-open text-5xl text-slate-200 dark:text-slate-700 mb-3"></i>
                        <p class="text-sm text-slate-500 font-medium">Belum ada materi</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($materiPublished as $materi)
                            <a href="{{ route('siswa.material.show', $materi->id) }}"
                               class="flex items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group border border-transparent hover:border-slate-100 dark:hover:border-white/10">
                                <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-500/20 text-blue-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="ph-fill ph-file-pdf text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate group-hover:text-yumik-primary transition-colors">
                                        {{ $materi->title }}
                                    </h4>
                                    @if($materi->description)
                                        <p class="text-xs text-slate-500 mt-1 line-clamp-1">{{ $materi->description }}</p>
                                    @endif
                                    <p class="text-[11px] font-bold text-slate-400 mt-1.5">
                                        {{ $materi->created_at->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <i class="ph-bold ph-caret-right text-slate-300 group-hover:text-yumik-primary group-hover:translate-x-1 transition-all self-center"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        {{-- KOLOM KANAN: TUGAS + UJIAN --}}
        <div class="space-y-8">

            {{-- TUGAS --}}
            <x-card class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="ph-fill ph-clipboard-text text-amber-500 text-xl"></i>
                        Tugas
                    </h3>
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-500 bg-amber-50 dark:bg-amber-500/10 px-2 py-1 rounded-full">
                        {{ $tugasDaftar->count() }}
                    </span>
                </div>

                @if($tugasDaftar->isEmpty())
                    <div class="text-center py-6">
                        <i class="ph-fill ph-check-circle text-4xl text-slate-200 dark:text-slate-700 mb-2"></i>
                        <p class="text-xs text-slate-500 font-medium">Belum ada tugas</p>
                    </div>
                @else
                    <div class="space-y-1 -mx-2">
                        @foreach($tugasDaftar as $tugas)
                            @php
                                $sudahSubmit = $tugas->grade && $tugas->grade->submitted_at;
                                $sudahDinilai = $tugas->grade && $tugas->grade->score !== null;
                            @endphp
                            <a href="{{ route('siswa.assignment.show', $tugas->id) }}"
                               class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                                <div class="w-9 h-9 rounded-lg {{ $sudahDinilai ? 'bg-green-100 dark:bg-green-500/20 text-green-500' : ($sudahSubmit ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-500' : 'bg-amber-100 dark:bg-amber-500/20 text-amber-500') }} flex items-center justify-center shrink-0">
                                    <i class="ph-bold {{ $sudahDinilai ? 'ph-check' : ($sudahSubmit ? 'ph-paper-plane-tilt' : 'ph-clipboard-text') }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate group-hover:text-yumik-primary transition-colors">
                                        {{ $tugas->title }}
                                    </p>
                                    @if($sudahDinilai)
                                        <p class="text-[10px] font-bold text-green-500 mt-0.5">Nilai: {{ $tugas->grade->score }}</p>
                                    @elseif($sudahSubmit)
                                        <p class="text-[10px] font-bold text-blue-500 mt-0.5">Sudah dikumpulkan</p>
                                    @elseif($tugas->due_date)
                                        <p class="text-[10px] font-bold text-amber-500 mt-0.5">
                                            Tenggat: {{ $tugas->due_date->translatedFormat('d M, H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-card>

            {{-- UJIAN --}}
            <x-card class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="ph-fill ph-exam text-purple-500 text-xl"></i>
                        Ujian & Kuis
                    </h3>
                    <span class="text-[10px] font-black uppercase tracking-widest text-purple-500 bg-purple-50 dark:bg-purple-500/10 px-2 py-1 rounded-full">
                        {{ $ujianDaftar->count() }}
                    </span>
                </div>

                @if($ujianDaftar->isEmpty())
                    <div class="text-center py-6">
                        <i class="ph-fill ph-coffee text-4xl text-slate-200 dark:text-slate-700 mb-2"></i>
                        <p class="text-xs text-slate-500 font-medium">Belum ada ujian</p>
                    </div>
                @else
                    <div class="space-y-1 -mx-2">
                        @foreach($ujianDaftar as $ujian)
                            <a href="{{ route('siswa.cbt.show', $ujian->id) }}"
                               class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                                <div class="w-9 h-9 rounded-lg bg-purple-100 dark:bg-purple-500/20 text-purple-500 flex items-center justify-center shrink-0">
                                    <i class="ph-bold ph-exam"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate group-hover:text-yumik-primary transition-colors">
                                        {{ $ujian->title }}
                                    </p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">
                                        {{ $ujian->type }} · {{ $ujian->duration }} menit
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-card>

        </div>
    </div>
</div>
@endsection