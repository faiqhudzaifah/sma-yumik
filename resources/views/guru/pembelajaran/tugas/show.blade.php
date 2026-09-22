@extends('layouts.app')

@section('title', 'Penilaian - ' . $assignment->title)

@section('content')
<div class="space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.schedule.assignment.index', $schedule->id) }}">Tugas & Kuis</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Penilaian Tugas</x-breadcrumb-item>
    </x-breadcrumbs>

    <div class="flex flex-col lg:flex-row gap-6 items-start">
        <!-- Panel Info Tugas (Kiri) -->
        <x-card class="w-full lg:w-1/3 p-6 shrink-0 bg-gradient-to-b from-slate-50 to-white dark:from-[#161B22] dark:to-[#0A0D14]">
            <x-badge color="primary" class="mb-4">{{ $assignment->type }}</x-badge>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">{{ $assignment->title }}</h1>
            
            <div class="space-y-4 mt-6">
                <div class="flex items-start gap-3">
                    <i class="ph-fill ph-clock text-amber-500 text-xl"></i>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Tenggat Waktu</p>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->translatedFormat('d F Y, H:i') : 'Tanpa Tenggat' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="ph-fill ph-users-three text-blue-500 text-xl"></i>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Status Pengumpulan</p>
                        <!-- Hitung jumlah siswa yang field submitted_at nya tidak null -->
                        @php $terkumpul = $assignment->grades->whereNotNull('submitted_at')->count(); @endphp
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $terkumpul }} dari {{ $assignment->grades->count() }} Terkumpul</p>
                    </div>
                </div>
            </div>

            <hr class="my-6 border-slate-100 dark:border-white/5">
            <h4 class="text-xs font-bold text-slate-500 mb-2">Instruksi Tugas:</h4>
            <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-wrap">{{ $assignment->description ?? 'Tidak ada instruksi khusus.' }}</div>
        </x-card>

        <!-- Panel Form Penilaian Massal (Kanan) -->
        <x-card class="w-full lg:w-2/3 p-0 overflow-hidden flex flex-col h-full">
            <form action="{{ route('guru.schedule.assignment.grades.update', [$schedule->id, $assignment->id]) }}" method="POST" class="flex-1 flex flex-col">
                @csrf
                <div class="p-6 border-b border-slate-100 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-black/20">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Lembar Penilaian Siswa</h3>
                    <x-button type="submit" variant="primary" class="!px-4 !py-2 !text-sm shadow-md">
                        <i class="ph-bold ph-floppy-disk"></i> Simpan Seluruh Nilai
                    </x-button>
                </div>

                <div class="flex-1 overflow-x-auto custom-scrollbar min-h-[400px]">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-white dark:bg-transparent border-b border-slate-100 dark:border-white/5 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                                <th class="p-4 pl-6">Nama Siswa</th>
                                <th class="p-4 text-center">Status / File</th>
                                <th class="p-4 text-center w-28">Nilai (0-100)</th>
                                <th class="p-4 pr-6">Feedback / Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                            @foreach($assignment->grades as $index => $grade)
                                @php
                                    $hasSubmitted = !is_null($grade->submitted_at);
                                    $isLate = $assignment->due_date && $hasSubmitted && \Carbon\Carbon::parse($grade->submitted_at)->isAfter($assignment->due_date);
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
                                    <td class="p-4 pl-6">
                                        <input type="hidden" name="grades[{{ $index }}][id]" value="{{ $grade->id }}">
                                        <div class="flex items-center gap-3">
                                            <x-avatar src="{{ $grade->siswa->photo ?? '' }}" size="sm" />
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $grade->siswa->user->name }}</span>
                                                <span class="text-[10px] font-medium text-slate-400">{{ $grade->siswa->nisn }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="p-4 text-center">
                                        @if($hasSubmitted)
                                            <div class="flex flex-col items-center gap-1">
                                                <x-badge color="{{ $isLate ? 'neutral' : 'primary' }}" class="{{ $isLate ? '!bg-amber-100 !text-amber-600' : '' }} !text-[10px]">
                                                    {{ $isLate ? 'Terlambat' : 'Diserahkan' }}
                                                </x-badge>
                                                @if($grade->file_path)
                                                    <a href="{{ asset('storage/' . $grade->file_path) }}" target="_blank" class="text-xs font-bold text-blue-500 hover:underline flex items-center gap-1 mt-1">
                                                        <i class="ph-bold ph-download-simple"></i> Buka File
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs font-semibold text-slate-400">Belum Mengumpulkan</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-center">
                                        <!-- Form Input Nilai (0-100) -->
                                        <input type="number" min="0" max="100" name="grades[{{ $index }}][score]" value="{{ $grade->score }}" placeholder="-" class="w-20 py-2 text-center rounded-xl bg-slate-100 dark:bg-black/40 border-none font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all placeholder-slate-300">
                                    </td>

                                    <td class="p-4 pr-6">
                                        <!-- Form Input Feedback -->
                                        <input type="text" name="grades[{{ $index }}][feedback]" value="{{ $grade->feedback }}" placeholder="Komentar untuk siswa..." class="w-full py-2 px-4 rounded-xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm focus:outline-none focus:ring-2 focus:ring-yumik-primary/30 transition-all text-slate-700 dark:text-slate-300">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection