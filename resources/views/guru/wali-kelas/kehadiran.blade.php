@extends('layouts.app')

@section('title', 'Rekap Kehadiran - ' . $classroom->name)

@section('content')
<div class="space-y-6">

    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('guru.dashboard') }}" class="hover:text-yumik-primary transition-colors">Dashboard</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <a href="{{ route('guru.wali-kelas.dashboard') }}" class="hover:text-yumik-primary transition-colors">Panel Wali Kelas</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold">Rekap Kehadiran</span>
    </nav>

    <x-card class="p-6 lg:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-500 flex items-center justify-center shrink-0">
                    <i class="ph-fill ph-calendar-check text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white">Rekap Kehadiran</h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Kelas {{ $classroom->name }} · {{ $meetings->count() }} pertemuan
                        @if($filterScheduleId)
                            @php $filtered = $schedules->firstWhere('id', $filterScheduleId); @endphp
                            · <span class="font-bold text-yumik-primary">{{ $filtered->subject->name ?? '' }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="flex flex-col sm:flex-row gap-3 mt-6">
            <form action="{{ route('guru.wali-kelas.kehadiran') }}" method="GET" class="flex-1">
                <div class="relative">
                    <i class="ph-bold ph-funnel absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select name="schedule_id" onchange="this.form.submit()"
                        class="w-full appearance-none pl-11 pr-10 py-3 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all cursor-pointer">
                        <option value="">Semua Mapel</option>
                        @foreach($schedules as $s)
                            <option value="{{ $s->id }}" {{ $filterScheduleId == $s->id ? 'selected' : '' }}>
                                {{ $s->subject->name }}
                            </option>
                        @endforeach
                    </select>
                    <i class="ph-bold ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </form>

            <a href="{{ route('guru.wali-kelas.kehadiran.matrix', ['schedule_id' => $filterScheduleId]) }}">
                <x-button variant="primary" class="!py-3 !text-sm w-full sm:w-auto">
                    <i class="ph-bold ph-grid-four"></i> Matrix Detail
                </x-button>
            </a>
        </div>
    </x-card>

    @if($siswas->isEmpty())
        <x-card class="!p-0">
            <x-empty-state icon="users" title="Belum ada siswa" description="Belum ada siswa terdaftar di kelas ini." />
        </x-card>
    @else
        <x-card class="!p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-black/20 border-b border-slate-100 dark:border-white/10">
                            <th class="p-4 pl-6 text-[10px] font-bold uppercase tracking-widest text-slate-400 w-12">#</th>
                            <th class="p-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Nama Siswa</th>
                            <th class="p-4 text-center text-[10px] font-bold uppercase tracking-widest text-emerald-500">Hadir</th>
                            <th class="p-4 text-center text-[10px] font-bold uppercase tracking-widest text-blue-500">Sakit</th>
                            <th class="p-4 text-center text-[10px] font-bold uppercase tracking-widest text-amber-500">Izin</th>
                            <th class="p-4 text-center text-[10px] font-bold uppercase tracking-widest text-red-500">Alpa</th>
                            <th class="p-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">Total</th>
                            <th class="p-4 pr-6 text-center text-[10px] font-bold uppercase tracking-widest text-yumik-primary">% Hadir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach($rekap as $index => $row)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="p-4 pl-6 text-sm text-slate-400 font-medium">{{ $index + 1 }}</td>
                                <td class="p-4">
                                    <a href="{{ route('guru.wali-kelas.siswa.detail', $row['siswa']->id) }}" class="flex items-center gap-3 group">
                                        <x-avatar src="{{ $row['siswa']->photo ? asset('storage/' . $row['siswa']->photo) : '' }}" size="sm" />
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-yumik-primary transition-colors">{{ $row['siswa']->user->name ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $row['siswa']->nisn ?? '-' }}</p>
                                        </div>
                                    </a>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">{{ $row['hadir'] }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">{{ $row['sakit'] }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">{{ $row['izin'] }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400">{{ $row['alpa'] }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $row['total'] }}</span>
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <span class="text-base font-black {{ $row['persen'] >= 75 ? 'text-yumik-primary' : 'text-red-500' }}">{{ $row['persen'] }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif

</div>
@endsection