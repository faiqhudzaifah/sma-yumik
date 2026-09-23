@extends('layouts.app')

@section('title', 'Rapor Akademik - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8">

    <x-card class="!bg-gradient-to-r from-blue-500/10 to-transparent border-blue-500/20 p-6 lg:p-8">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 shrink-0">
                <i class="ph-fill ph-notebook text-3xl"></i>
            </div>
            <div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">Rapor Akademik</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">
                    Rekap nilai tugas & ujian
                    @if($activeSemester) · Semester {{ $activeSemester->type }}@endif
                </p>
            </div>
        </div>
    </x-card>

    {{-- NILAI TUGAS --}}
    <x-card class="p-6 lg:p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 rounded-[1rem] bg-amber-500/10 flex items-center justify-center text-amber-500">
                    <i class="ph-fill ph-clipboard-text text-2xl"></i>
                </div>
                Nilai Tugas
            </h2>
            <span class="text-xs font-bold text-slate-400">{{ $assignmentGrades->count() }} tugas</span>
        </div>

        @if($assignmentGrades->isEmpty())
            <div class="text-center py-8">
                <i class="ph-fill ph-clipboard-text text-5xl text-slate-200 dark:text-slate-700 mb-3"></i>
                <p class="text-sm text-slate-500">Belum ada nilai tugas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-white/10">
                            <th class="text-left py-3 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mata Pelajaran</th>
                            <th class="text-left py-3 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tugas</th>
                            <th class="text-center py-3 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nilai</th>
                            <th class="text-right py-3 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignmentGrades as $grade)
                            <tr class="border-b border-slate-50 dark:border-white/5 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <td class="py-3 px-2 font-bold text-slate-800 dark:text-white">
                                    {{ $grade->assignment->schedule->subject->name ?? '-' }}
                                </td>
                                <td class="py-3 px-2 text-slate-600 dark:text-slate-300">
                                    {{ $grade->assignment->title ?? '-' }}
                                </td>
                                <td class="py-3 px-2 text-center">
                                    @if($grade->score !== null)
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-black {{ $grade->score >= 75 ? 'bg-green-100 dark:bg-green-500/20 text-green-600' : 'bg-amber-100 dark:bg-amber-500/20 text-amber-600' }}">
                                            {{ $grade->score }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-bold">Belum dinilai</span>
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-right text-xs text-slate-500">
                                    {{ $grade->submitted_at ? $grade->submitted_at->translatedFormat('d M Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    {{-- NILAI UJIAN --}}
    <x-card class="p-6 lg:p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 rounded-[1rem] bg-purple-500/10 flex items-center justify-center text-purple-500">
                    <i class="ph-fill ph-exam text-2xl"></i>
                </div>
                Nilai Ujian
            </h2>
            <span class="text-xs font-bold text-slate-400">{{ $examScores->count() }} ujian</span>
        </div>

        @if($examScores->isEmpty())
            <div class="text-center py-8">
                <i class="ph-fill ph-exam text-5xl text-slate-200 dark:text-slate-700 mb-3"></i>
                <p class="text-sm text-slate-500">Belum ada nilai ujian.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-white/10">
                            <th class="text-left py-3 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mata Pelajaran</th>
                            <th class="text-left py-3 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ujian</th>
                            <th class="text-center py-3 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nilai</th>
                            <th class="text-right py-3 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($examScores as $submission)
                            <tr class="border-b border-slate-50 dark:border-white/5 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <td class="py-3 px-2 font-bold text-slate-800 dark:text-white">
                                    {{ $submission->assessment->schedule->subject->name ?? '-' }}
                                </td>
                                <td class="py-3 px-2 text-slate-600 dark:text-slate-300">
                                    {{ $submission->assessment->title ?? '-' }}
                                </td>
                                <td class="py-3 px-2 text-center">
                                    @if($submission->score !== null)
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-black {{ $submission->score >= 75 ? 'bg-green-100 dark:bg-green-500/20 text-green-600' : 'bg-amber-100 dark:bg-amber-500/20 text-amber-600' }}">
                                            {{ $submission->score }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-bold">Menunggu koreksi</span>
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-right text-xs text-slate-500">
                                    {{ $submission->submitted_at ? $submission->submitted_at->translatedFormat('d M Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</div>
@endsection