@extends('layouts.app')

@section('title', 'Jadwal Kelas - ' . $classroom->name)

@section('content')
<div class="space-y-6">

    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('guru.dashboard') }}" class="hover:text-yumik-primary transition-colors">Dashboard</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <a href="{{ route('guru.wali-kelas.dashboard') }}" class="hover:text-yumik-primary transition-colors">Panel Wali Kelas</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold">Jadwal Kelas</span>
    </nav>

    <x-card class="p-6 lg:p-8">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-orange-100 dark:bg-orange-500/20 text-orange-500 flex items-center justify-center shrink-0">
                <i class="ph-fill ph-calendar-blank text-2xl"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white">Jadwal Kelas</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelas {{ $classroom->name }} · {{ $schedules->count() }} jadwal mengajar
                </p>
            </div>
            <x-badge color="accent">{{ $activeSemester->type }} {{ $activeSemester->academicYear->name ?? '' }}</x-badge>
        </div>
    </x-card>

    @if($schedules->isEmpty())
        <x-card class="!p-0">
            <x-empty-state icon="calendar-blank" title="Belum ada jadwal" description="Admin belum menambahkan jadwal untuk kelas ini." />
        </x-card>
    @else
        @php
            $grouped = $schedules->groupBy('day_of_week');
            $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        @endphp

        <x-card class="!p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-black/20 border-b border-slate-100 dark:border-white/10">
                            <th class="p-4 pl-6 text-[10px] font-bold uppercase tracking-widest text-slate-400 w-32">Hari</th>
                            <th class="p-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 w-24">Jam</th>
                            <th class="p-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Mata Pelajaran</th>
                            <th class="p-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Guru Pengajar</th>
                            <th class="p-4 pr-6 text-[10px] font-bold uppercase tracking-widest text-slate-400">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach($dayOrder as $day)
                            @if($grouped->has($day))
                                @php $daySchedules = $grouped[$day]->sortBy('jam_ke_mulai'); @endphp
                                @foreach($daySchedules as $index => $schedule)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                        @if($index === 0)
                                            <td rowspan="{{ $daySchedules->count() }}" class="p-4 pl-6 align-top border-r border-slate-100 dark:border-white/5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-yumik-primary/10 text-yumik-primary flex items-center justify-center shrink-0">
                                                        <i class="ph-bold ph-calendar-day text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-800 dark:text-white">{{ $day }}</p>
                                                        <p class="text-[10px] text-slate-400">{{ $daySchedules->count() }} mapel</p>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif

                                        <td class="p-4">
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/5 text-xs font-bold text-slate-700 dark:text-slate-300">
                                                <i class="ph-bold ph-clock text-yumik-primary"></i>
                                                Jam {{ $schedule->jam_ke_mulai }}–{{ $schedule->jam_ke_selesai }}
                                            </div>
                                        </td>

                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 text-blue-500 flex items-center justify-center shrink-0">
                                                    <i class="ph-fill ph-book-bookmark text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800 dark:text-white text-sm">{{ $schedule->subject->name ?? '-' }}</p>
                                                    @if($schedule->subject->code)
                                                        <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $schedule->subject->code }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <x-avatar src="{{ $schedule->guru->photo ? asset('storage/' . $schedule->guru->photo) : '' }}" size="sm" />
                                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                    {{ $schedule->guru->user->name ?? '-' }}
                                                </p>
                                            </div>
                                        </td>

                                        <td class="p-4 pr-6">
                                            @if($schedule->ruangan)
                                                <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-600 dark:text-slate-300">
                                                    <i class="ph-bold ph-map-pin text-slate-400"></i>
                                                    {{ $schedule->ruangan }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400 italic">Kelas utama</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif

</div>
@endsection