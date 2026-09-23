@extends('layouts.app')

@section('title', $material->title . ' - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">

    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('siswa.course.index') }}" class="hover:text-yumik-primary transition-colors">Mata Pelajaran</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        @if($material->schedule)
            <a href="{{ route('siswa.course.show', $material->schedule->id) }}" class="hover:text-yumik-primary transition-colors truncate">
                {{ $material->schedule->subject->name ?? 'Mapel' }}
            </a>
            <i class="ph-bold ph-caret-right text-xs"></i>
        @endif
        <span class="text-slate-800 dark:text-white font-bold truncate">{{ $material->title }}</span>
    </nav>

    <x-card class="p-6 lg:p-8">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 shrink-0">
                <i class="ph-fill ph-books text-3xl"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white mb-2">{{ $material->title }}</h1>
                <p class="text-sm text-slate-500 font-medium">
                    {{ $material->schedule->subject->name ?? '-' }}
                    · {{ $material->schedule->guru->name ?? '-' }}
                </p>
                <p class="text-[11px] font-bold text-slate-400 mt-2">
                    Dipublikasikan {{ $material->created_at->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>

        @if($material->description)
            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-white/10">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Deskripsi</h3>
                <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $material->description }}</div>
            </div>
        @endif
    </x-card>

    {{-- FILE / LINK --}}
    @if($material->file_path || $material->external_link)
        <x-card class="p-6 lg:p-8">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Akses Materi</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($material->file_path)
                    <a href="{{ Storage::disk('public')->url($material->file_path) }}" target="_blank"
                       class="flex items-center gap-4 p-5 rounded-2xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-all group">
                        <div class="w-12 h-12 rounded-xl bg-blue-500 text-white flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <i class="ph-fill ph-file-pdf text-2xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-white">File Materi</p>
                            <p class="text-xs text-slate-500 truncate">Klik untuk membuka / mengunduh</p>
                        </div>
                        <i class="ph-bold ph-download-simple text-blue-500 text-xl"></i>
                    </a>
                @endif

                @if($material->external_link)
                    <a href="{{ $material->external_link }}" target="_blank"
                       class="flex items-center gap-4 p-5 rounded-2xl bg-purple-50 dark:bg-purple-500/10 border border-purple-200 dark:border-purple-500/20 hover:bg-purple-100 dark:hover:bg-purple-500/20 transition-all group">
                        <div class="w-12 h-12 rounded-xl bg-purple-500 text-white flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <i class="ph-fill ph-link text-2xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Link Eksternal</p>
                            <p class="text-xs text-slate-500 truncate">{{ $material->external_link }}</p>
                        </div>
                        <i class="ph-bold ph-arrow-square-out text-purple-500 text-xl"></i>
                    </a>
                @endif
            </div>
        </x-card>
    @else
        <x-card class="p-6 text-center">
            <i class="ph-fill ph-info text-4xl text-slate-200 dark:text-slate-700 mb-3"></i>
            <p class="text-sm text-slate-500">Materi ini belum memiliki file atau link.</p>
        </x-card>
    @endif

    <div class="flex justify-center">
        <a href="{{ route('siswa.course.show', $material->schedule_id) }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-50 dark:hover:bg-white/10 transition-all">
            <i class="ph-bold ph-arrow-left"></i>
            Kembali ke Mata Pelajaran
        </a>
    </div>

</div>
@endsection