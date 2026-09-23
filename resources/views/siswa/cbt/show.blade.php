@extends('layouts.app')

@section('title', $assessment->title . ' - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8 max-w-3xl mx-auto">

    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('siswa.cbt.index') }}" class="hover:text-yumik-primary transition-colors">Ujian</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold truncate">{{ $assessment->title }}</span>
    </nav>

    <x-card class="p-6 lg:p-8">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-purple-500/30 shrink-0">
                <i class="ph-fill ph-exam text-3xl"></i>
            </div>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <x-badge color="{{ $assessment->type === 'EXAM' ? 'accent' : 'neutral' }}">{{ $assessment->type }}</x-badge>
                    <x-badge color="neutral">{{ $assessment->duration }} menit</x-badge>
                    <x-badge color="neutral">{{ $assessment->attempt_limit }}x percobaan</x-badge>
                </div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white mb-2">{{ $assessment->title }}</h1>
                <p class="text-sm text-slate-500 font-medium">
                    {{ $assessment->schedule->subject->name ?? '-' }}
                    · {{ $assessment->schedule->guru->name ?? '-' }}
                </p>
            </div>
        </div>

        @if($assessment->description)
            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-white/10">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Deskripsi</h3>
                <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $assessment->description }}</div>
            </div>
        @endif
    </x-card>

    {{-- INFO WAKTU --}}
    <x-card class="p-6">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Informasi Waktu</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-black/20">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Mulai</p>
                <p class="text-sm font-bold text-slate-800 dark:text-white">
                    {{ $assessment->start_at ? $assessment->start_at->translatedFormat('d M Y, H:i') : '-' }}
                </p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-black/20">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Berakhir</p>
                <p class="text-sm font-bold text-slate-800 dark:text-white">
                    {{ $assessment->end_at ? $assessment->end_at->translatedFormat('d M Y, H:i') : '-' }}
                </p>
            </div>
        </div>
    </x-card>

    {{-- STATUS --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-sm font-bold text-green-600 dark:text-green-400 flex items-center gap-2">
            <i class="ph-fill ph-check-circle text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-sm font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
            <i class="ph-fill ph-warning-circle text-lg"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 text-sm font-bold text-blue-600 dark:text-blue-400 flex items-center gap-2">
            <i class="ph-fill ph-info text-lg"></i>
            {{ session('info') }}
        </div>
    @endif

    @if($submission && $submission->submitted_at)
        {{-- SUDAH SELESAI --}}
        <x-card class="!bg-gradient-to-br from-green-500/10 to-transparent border-green-500/30 p-6 lg:p-8 text-center">
            <div class="w-20 h-20 rounded-full bg-green-500 flex items-center justify-center text-white shadow-lg shadow-green-500/30 mx-auto mb-4">
                <i class="ph-fill ph-check-circle text-4xl"></i>
            </div>
            <h3 class="text-xl font-extrabold text-slate-800 dark:text-white mb-1">Ujian Sudah Selesai</h3>
            <p class="text-sm text-slate-500 font-medium mb-4">
                Disubmit {{ $submission->submitted_at->translatedFormat('d M Y, H:i') }}
            </p>
            @if($submission->score !== null)
                <div class="inline-block px-6 py-3 rounded-2xl bg-white dark:bg-white/5 border border-slate-100 dark:border-white/10">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nilai</p>
                    <p class="text-4xl font-black text-yumik-primary">{{ $submission->score }}</p>
                </div>
            @endif
        </x-card>

    @elseif($assessment->status === 'PUBLISHED')
        {{-- BISA DIKERJAKAN --}}
        <x-card class="p-6 lg:p-8 text-center">
            <div class="w-20 h-20 rounded-full bg-purple-100 dark:bg-purple-500/20 text-purple-500 flex items-center justify-center mx-auto mb-4">
                <i class="ph-fill ph-play-circle text-4xl"></i>
            </div>
            <h3 class="text-xl font-extrabold text-slate-800 dark:text-white mb-1">Siap Mengerjakan?</h3>
            <p class="text-sm text-slate-500 font-medium mb-6 max-w-md mx-auto">
                Pastikan koneksi internet stabil dan kamu siap. Waktu akan terus berjalan setelah ujian dimulai.
            </p>

            <form action="{{ route('siswa.cbt.start', $assessment->id) }}" method="POST" onsubmit="return confirm('Mulai ujian sekarang? Waktu akan terus berjalan.');">
                @csrf
                <button type="submit" class="px-8 py-4 rounded-2xl bg-yumik-primary hover:bg-green-600 text-white font-bold shadow-lg shadow-yumik-primary/30 transition-all hover:scale-[1.02] active:scale-[0.98] inline-flex items-center gap-2">
                    <i class="ph-bold ph-play text-lg"></i>
                    Mulai Ujian
                </button>
            </form>
        </x-card>

    @else
        <x-card class="p-6 text-center">
            <i class="ph-fill ph-lock text-5xl text-slate-200 dark:text-slate-700 mb-3"></i>
            <p class="text-sm font-bold text-slate-500">Ujian belum dibuka atau sudah ditutup.</p>
        </x-card>
    @endif

</div>
@endsection