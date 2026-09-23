@extends('layouts.app')

@section('title', $assignment->title . ' - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('siswa.assignment.index') }}" class="hover:text-yumik-primary transition-colors">Tugas</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold truncate">{{ $assignment->title }}</span>
    </nav>

    {{-- HERO TUGAS --}}
    <x-card class="p-6 lg:p-8">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30 shrink-0">
                <i class="ph-fill ph-clipboard-text text-3xl"></i>
            </div>

            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <x-badge color="accent">{{ $assignment->type }}</x-badge>
                    @if($assignment->due_date)
                        <x-badge color="neutral">
                            <i class="ph-bold ph-clock"></i>
                            {{ $assignment->due_date->translatedFormat('d M Y, H:i') }}
                        </x-badge>
                    @endif
                </div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white mb-2">{{ $assignment->title }}</h1>
                <p class="text-sm text-slate-500 font-medium">
                    {{ $assignment->schedule->subject->name ?? '-' }}
                    · {{ $assignment->schedule->guru->name ?? '-' }}
                </p>
            </div>
        </div>

        @if($assignment->description)
            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-white/10">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Deskripsi</h3>
                <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $assignment->description }}</div>
            </div>
        @endif
    </x-card>

    {{-- STATUS PENGUMPULAN --}}
    @if($grade && $grade->score !== null)
        {{-- SUDAH DINILAI --}}
        <x-card class="!bg-gradient-to-br from-green-500/10 to-transparent border-green-500/30 p-6 lg:p-8">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 rounded-2xl bg-green-500 flex items-center justify-center text-white shadow-lg shadow-green-500/30">
                    <i class="ph-fill ph-check-circle text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Tugas Sudah Dinilai</h3>
                    <p class="text-sm text-slate-500 font-medium">Nilai tidak dapat diubah lagi</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div class="p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-100 dark:border-white/10">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nilai</p>
                    <p class="text-4xl font-black text-green-500">{{ $grade->score }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-100 dark:border-white/10">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Dikumpulkan</p>
                    <p class="text-sm font-bold text-slate-800 dark:text-white">
                        {{ $grade->submitted_at ? $grade->submitted_at->translatedFormat('d M Y, H:i') : '-' }}
                    </p>
                </div>
            </div>

            @if($grade->feedback)
                <div class="p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-100 dark:border-white/10">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Feedback Guru</p>
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $grade->feedback }}</p>
                </div>
            @endif

            @if($grade->submitted_text || $grade->file_path)
                <div class="mt-5 pt-5 border-t border-slate-200 dark:border-white/10">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Jawaban Kamu</p>
                    @if($grade->submitted_text)
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-black/20 text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line mb-3">{{ $grade->submitted_text }}</div>
                    @endif
                    @if($grade->file_path)
                        <a href="{{ Storage::disk('public')->url($grade->file_path) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-500 text-xs font-bold hover:bg-blue-100 transition-colors">
                            <i class="ph-bold ph-file-arrow-down"></i> Unduh File
                        </a>
                    @endif
                </div>
            @endif
        </x-card>

    @elseif($grade && $grade->submitted_at)
        {{-- SUDAH SUBMIT, BELUM DINILAI --}}
        <x-card class="!bg-gradient-to-br from-blue-500/10 to-transparent border-blue-500/30 p-6 lg:p-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                    <i class="ph-fill ph-paper-plane-tilt text-3xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Sudah Dikumpulkan</h3>
                    <p class="text-sm text-slate-500 font-medium">
                        Dikumpulkan {{ $grade->submitted_at->translatedFormat('d M Y, H:i') }}
                        · Menunggu penilaian guru
                    </p>
                </div>
            </div>

            @if($grade->submitted_text || $grade->file_path)
                <div class="mt-5 pt-5 border-t border-slate-200 dark:border-white/10">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Jawaban Kamu</p>
                    @if($grade->submitted_text)
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-black/20 text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line mb-3">{{ $grade->submitted_text }}</div>
                    @endif
                    @if($grade->file_path)
                        <a href="{{ Storage::disk('public')->url($grade->file_path) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-500 text-xs font-bold hover:bg-blue-100 transition-colors">
                            <i class="ph-bold ph-file-arrow-down"></i> Unduh File
                        </a>
                    @endif
                </div>
            @endif

            <div class="mt-5 pt-5 border-t border-slate-200 dark:border-white/10">
                <p class="text-xs text-slate-500 font-medium mb-3">
                    <i class="ph-bold ph-info text-blue-500"></i>
                    Kamu masih bisa mengubah jawaban selama belum dinilai guru.
                </p>
            </div>
        </x-card>
    @endif

    {{-- FORM SUBMIT (HANYA JIKA BELUM DINILAI) --}}
    @if(!$grade || $grade->score === null)
        <x-card class="p-6 lg:p-8">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-[1rem] bg-yumik-primary/10 flex items-center justify-center text-yumik-primary">
                    <i class="ph-fill ph-paper-plane-tilt text-2xl"></i>
                </div>
                {{ $grade && $grade->submitted_at ? 'Perbarui Jawaban' : 'Kumpulkan Tugas' }}
            </h2>

            @if(session('success'))
                <div class="mb-4 p-4 rounded-2xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-sm font-bold text-green-600 dark:text-green-400 flex items-center gap-2">
                    <i class="ph-fill ph-check-circle text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-sm font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                    <i class="ph-fill ph-warning-circle text-lg"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('siswa.assignment.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Jawaban Teks</label>
                    <textarea name="submitted_text" rows="6"
                              placeholder="Tulis jawabanmu di sini..."
                              class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all resize-none">{{ old('submitted_text', $grade->submitted_text ?? '') }}</textarea>
                    @error('submitted_text')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">File Lampiran (opsional)</label>
                    <input type="file" name="file"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm text-slate-800 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-yumik-primary file:text-white file:text-xs file:font-bold hover:file:bg-green-600 file:cursor-pointer cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">Format: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR, JPG, PNG (maks 10MB)</p>
                    @error('file')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    @if($grade && $grade->file_path)
                        <p class="text-xs text-blue-500 font-medium mt-2">
                            <i class="ph-bold ph-info"></i> File sudah ada. Upload file baru untuk mengganti.
                        </p>
                    @endif
                </div>

                <button type="submit"
                        class="w-full py-4 rounded-2xl bg-yumik-primary hover:bg-green-600 text-white font-bold shadow-lg shadow-yumik-primary/30 transition-all hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
                    <i class="ph-bold ph-paper-plane-tilt text-lg"></i>
                    {{ $grade && $grade->submitted_at ? 'Perbarui Jawaban' : 'Kumpulkan Tugas' }}
                </button>
            </form>
        </x-card>
    @endif

</div>
@endsection