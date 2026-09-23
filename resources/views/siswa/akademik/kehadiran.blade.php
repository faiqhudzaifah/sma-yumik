@extends('layouts.app')

@section('title', 'Rekap Presensi - SMA Yumik Kulsum')

@section('content')
<div class="space-y-8">

    <x-card class="!bg-gradient-to-r from-green-500/10 to-transparent border-green-500/20 p-6 lg:p-8">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center text-white shadow-lg shadow-green-500/30 shrink-0">
                <i class="ph-fill ph-calendar-check text-3xl"></i>
            </div>
            <div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">Rekap Presensi</h1>
                <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">
                    Total {{ $attendances->count() }} pertemuan
                    @if($activeSemester) · Semester {{ $activeSemester->type }}@endif
                </p>
            </div>
        </div>
    </x-card>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card class="!p-5 text-center">
            <div class="w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-500/20 text-green-500 flex items-center justify-center mx-auto mb-3">
                <i class="ph-fill ph-check-circle text-2xl"></i>
            </div>
            <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $summary['Hadir'] }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Hadir</p>
        </x-card>

        <x-card class="!p-5 text-center">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-500/20 text-blue-500 flex items-center justify-center mx-auto mb-3">
                <i class="ph-fill ph-medical-cross text-2xl"></i>
            </div>
            <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $summary['Sakit'] }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Sakit</p>
        </x-card>

        <x-card class="!p-5 text-center">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center mx-auto mb-3">
                <i class="ph-fill ph-envelope text-2xl"></i>
            </div>
            <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $summary['Izin'] }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Izin</p>
        </x-card>

        <x-card class="!p-5 text-center">
            <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-500/20 text-red-500 flex items-center justify-center mx-auto mb-3">
                <i class="ph-fill ph-x-circle text-2xl"></i>
            </div>
            <p class="text-3xl font-black text-slate-800 dark:text-white">{{ $summary['Alpa'] }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Alpa</p>
        </x-card>
    </div>

    {{-- DETAIL --}}
    <x-card class="p-6 lg:p-8">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-[1rem] bg-green-500/10 flex items-center justify-center text-green-500">
                <i class="ph-fill ph-list-checks text-2xl"></i>
            </div>
            Detail Kehadiran
        </h2>

        @if($attendances->isEmpty())
            <div class="text-center py-8">
                <i class="ph-fill ph-calendar-x text-5xl text-slate-200 dark:text-slate-700 mb-3"></i>
                <p class="text-sm text-slate-500">Belum ada data kehadiran.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($attendances as $attendance)
                    @php
                        $statusColor = [
                            'Hadir' => 'green',
                            'Sakit' => 'blue',
                            'Izin'  => 'amber',
                            'Alpa'  => 'red',
                        ][$attendance->status] ?? 'slate';
                    @endphp
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                        <div class="w-12 h-12 rounded-xl bg-{{ $statusColor }}-100 dark:bg-{{ $statusColor }}-500/20 text-{{ $statusColor }}-500 flex items-center justify-center shrink-0">
                            <i class="ph-fill ph-{{ $attendance->status === 'Hadir' ? 'check-circle' : ($attendance->status === 'Sakit' ? 'medical-cross' : ($attendance->status === 'Izin' ? 'envelope' : 'x-circle')) }} text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-white truncate">
                                {{ $attendance->meeting->schedule->subject->name ?? 'Pertemuan' }}
                            </p>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">
                                Pertemuan #{{ $attendance->meeting->meeting_number ?? '-' }}
                                · {{ $attendance->meeting->date ? $attendance->meeting->date->translatedFormat('d M Y') : '-' }}
                            </p>
                            @if($attendance->notes)
                                <p class="text-[11px] text-slate-400 mt-1 italic">"{{ $attendance->notes }}"</p>
                            @endif
                        </div>
                        <x-badge color="{{ $statusColor === 'green' ? 'primary' : ($statusColor === 'red' ? 'accent' : 'neutral') }}">
                            {{ $attendance->status }}
                        </x-badge>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
</div>
@endsection