@extends('layouts.app')

@section('title', 'Buku Nilai - ' . $schedule->subject->name)

@section('content')
<div class="space-y-6">
    <!-- BREADCRUMB -->
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.dashboard') }}">Dashboard</x-breadcrumb-item>
        <x-breadcrumb-item href="{{ route('guru.schedule.meeting.index', $schedule->id) }}">{{ $schedule->subject->name }}</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Buku Nilai (Gradebook)</x-breadcrumb-item>
    </x-breadcrumbs>

    <!-- HEADER COURSE -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gradient-to-r from-yumik-primary/10 to-transparent p-6 rounded-[2rem] border border-yumik-primary/20 shadow-sm">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                Buku Nilai Kelas
            </h1>
            <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">
                Matriks penilaian untuk kelas <span class="text-yumik-primary font-bold">{{ $schedule->classroom->name }}</span>
            </p>
        </div>
        
        <!-- Tombol Export dari GradebookController -->
        <a href="{{ route('guru.schedule.gradebook.export', $schedule->id) }}">
            <x-button variant="primary" class="shadow-md">
                <i class="ph-bold ph-microsoft-excel-logo text-xl"></i> Export ke Excel
            </x-button>
        </a>
    </div>

    <!-- TABS NAVIGASI -->
    <div class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar border-b border-slate-200 dark:border-white/10">
        <a href="{{ route('guru.schedule.meeting.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap transition-colors">
            <i class="ph-bold ph-calendar-check mr-2"></i> Alur Pertemuan
        </a>
        <a href="{{ route('guru.schedule.assignment.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap transition-colors">
            <i class="ph-bold ph-exam mr-2"></i> Tugas & Kuis
        </a>
        <a href="{{ route('guru.schedule.gradebook.index', $schedule->id) }}" class="px-6 py-3 border-b-2 border-yumik-primary text-yumik-primary font-bold whitespace-nowrap">
            <i class="ph-bold ph-notebook mr-2"></i> Buku Nilai
        </a>
    </div>

    <!-- MATRIX TABEL NILAI -->
    <x-card class="p-0 overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-white/5 flex items-center gap-3 bg-slate-50/50 dark:bg-black/20">
            <x-badge color="accent">Rumus Final: 40% Tugas + 60% Ujian</x-badge>
            <p class="text-xs text-slate-400 font-medium">Diperbarui secara otomatis setiap kali ada nilai yang masuk.</p>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-white dark:bg-transparent border-b border-slate-100 dark:border-white/10">
                        <!-- Kolom Statis -->
                        <th rowspan="2" class="p-4 pl-6 font-bold text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400 border-r border-slate-100 dark:border-white/5 w-64">Nama Siswa</th>
                        
                        <!-- Kolom Dinamis: Tugas -->
                        @if($assignments->count() > 0)
                            <th colspan="{{ $assignments->count() }}" class="p-4 text-center font-bold text-xs uppercase tracking-widest text-blue-500 bg-blue-50/50 dark:bg-blue-500/10 border-r border-slate-100 dark:border-white/5">
                                Nilai Tugas & Praktik (40%)
                            </th>
                        @endif

                        <!-- Kolom Dinamis: Ujian/CBT -->
                        @if($assessments->count() > 0)
                            <th colspan="{{ $assessments->count() }}" class="p-4 text-center font-bold text-xs uppercase tracking-widest text-amber-500 bg-amber-50/50 dark:bg-amber-500/10 border-r border-slate-100 dark:border-white/5">
                                Nilai Ujian CBT (60%)
                            </th>
                        @endif

                        <!-- Kolom Final -->
                        <th rowspan="2" class="p-4 pr-6 text-center font-bold text-xs uppercase tracking-widest text-yumik-primary bg-yumik-primary/5">Skor Akhir</th>
                    </tr>
                    
                    <!-- Sub-Header untuk Nama Tugas/Ujian -->
                    <tr class="border-b border-slate-200 dark:border-white/10">
                        @foreach($assignments as $assignment)
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-slate-600 dark:text-slate-300 border-r border-slate-100 dark:border-white/5 bg-slate-50 dark:bg-black/20" title="{{ $assignment->title }}">
                                <span class="block truncate w-20 mx-auto">{{ $assignment->title }}</span>
                            </th>
                        @endforeach

                        @foreach($assessments as $assessment)
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-slate-600 dark:text-slate-300 border-r border-slate-100 dark:border-white/5 bg-slate-50 dark:bg-black/20" title="{{ $assessment->title }}">
                                <span class="block truncate w-20 mx-auto">{{ $assessment->title }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    <!-- Meloop dari $gradeMatrix yang dikirim oleh GradebookController -->
                    @foreach($gradeMatrix as $row)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors group">
                            <!-- Info Siswa -->
                            <td class="p-4 pl-6 border-r border-slate-100 dark:border-white/5">
                                <div class="flex items-center gap-3">
                                    <x-avatar src="{{ $row['siswa']->photo ?? '' }}" size="sm" />
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $row['siswa']->user->name }}</span>
                                        <span class="text-[10px] font-medium text-slate-400">NISN: {{ $row['siswa']->nisn }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Nilai Tugas -->
                            @foreach($assignments as $assignment)
                                @php $score = $row['assignments'][$assignment->id]; @endphp
                                <td class="px-4 py-3 text-center border-r border-slate-100 dark:border-white/5">
                                    <span class="text-sm font-semibold {{ $score == 0 ? 'text-slate-300 dark:text-slate-600' : 'text-slate-700 dark:text-slate-200' }}">
                                        {{ $score > 0 ? number_format($score, 0) : '-' }}
                                    </span>
                                </td>
                            @endforeach

                            <!-- Nilai Ujian CBT -->
                            @foreach($assessments as $assessment)
                                @php $score = $row['assessments'][$assessment->id]; @endphp
                                <td class="px-4 py-3 text-center border-r border-slate-100 dark:border-white/5">
                                    <span class="text-sm font-semibold {{ $score == 0 ? 'text-slate-300 dark:text-slate-600' : 'text-slate-700 dark:text-slate-200' }}">
                                        {{ $score > 0 ? number_format($score, 0) : '-' }}
                                    </span>
                                </td>
                            @endforeach

                            <!-- Nilai Akhir (Highlight) -->
                            <td class="p-4 pr-6 text-center bg-yumik-primary/5 group-hover:bg-yumik-primary/10 transition-colors">
                                <span class="text-lg font-black {{ $row['final_score'] >= 75 ? 'text-yumik-primary' : 'text-red-500' }}">
                                    {{ number_format($row['final_score'], 1) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection