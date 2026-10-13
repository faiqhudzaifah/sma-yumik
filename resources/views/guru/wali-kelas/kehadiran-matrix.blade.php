@extends('layouts.app')

@section('title', 'Matrix Kehadiran - ' . $classroom->name)

@section('content')
<div class="space-y-6">

    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('guru.dashboard') }}" class="hover:text-yumik-primary transition-colors">Dashboard</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <a href="{{ route('guru.wali-kelas.dashboard') }}" class="hover:text-yumik-primary transition-colors">Panel Wali Kelas</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <a href="{{ route('guru.wali-kelas.kehadiran') }}" class="hover:text-yumik-primary transition-colors">Rekap</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold">Matrix</span>
    </nav>

    <x-card class="p-6 lg:p-8">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-purple-100 dark:bg-purple-500/20 text-purple-500 flex items-center justify-center shrink-0">
                <i class="ph-fill ph-grid-four text-2xl"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white">Matrix Kehadiran</h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $siswas->count() }} siswa × {{ $meetings->count() }} pertemuan
                </p>
            </div>
        </div>

        {{-- LEGENDA --}}
        <div class="flex flex-wrap gap-3 mt-6">
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600">H</span>
                Hadir
            </div>
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600">S</span>
                Sakit
            </div>
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="w-6 h-6 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center text-amber-600">I</span>
                Izin
            </div>
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="w-6 h-6 rounded-lg bg-red-100 dark:bg-red-500/20 flex items-center justify-center text-red-600">A</span>
                Alpa
            </div>
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400">-</span>
                Belum diabsen
            </div>
        </div>
    </x-card>

    @if($siswas->isEmpty() || $meetings->isEmpty())
        <x-card class="!p-0">
            <x-empty-state icon="grid-four" title="Data kosong" description="Belum ada siswa atau pertemuan untuk ditampilkan." />
        </x-card>
    @else
        <x-card class="!p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-black/20 border-b border-slate-100 dark:border-white/10">
                            <th class="p-4 pl-6 text-[10px] font-bold uppercase tracking-widest text-slate-400 sticky left-0 bg-slate-50 dark:bg-[#0A0D14] z-10 min-w-[200px]">Nama Siswa</th>
                            @foreach($meetings as $meeting)
                                <th class="p-2 text-center text-[9px] font-bold text-slate-400 min-w-[40px]"
                                    title="{{ $meeting->schedule->subject->name ?? '-' }} - Pertemuan {{ $meeting->meeting_number }} ({{ $meeting->date->translatedFormat('d M Y') }})">
                                    {{ $meeting->meeting_number }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach($siswas as $siswa)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="p-3 pl-6 sticky left-0 bg-white dark:bg-[#1A1F29] z-10">
                                    <div class="flex items-center gap-2">
                                        <x-avatar src="{{ $siswa->photo ? asset('storage/' . $siswa->photo) : '' }}" size="sm" />
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $siswa->user->name ?? '-' }}</p>
                                            <p class="text-[9px] text-slate-400">{{ $siswa->nisn }}</p>
                                        </div>
                                    </div>
                                </td>
                                @foreach($meetings as $meeting)
                                    @php
                                        $status = $matrix[$siswa->id][$meeting->id] ?? null;
                                        $color = match($status) {
                                            'Hadir' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400',
                                            'Sakit' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400',
                                            'Izin'  => 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400',
                                            'Alpa'  => 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
                                            default => 'bg-slate-100 dark:bg-white/5 text-slate-400',
                                        };
                                        $letter = match($status) {
                                            'Hadir' => 'H',
                                            'Sakit' => 'S',
                                            'Izin'  => 'I',
                                            'Alpa'  => 'A',
                                            default => '-',
                                        };
                                    @endphp
                                    <td class="p-1 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-[10px] font-black {{ $color }}">
                                            {{ $letter }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif

</div>
@endsection