@extends('layouts.app')

@section('title', 'Panel Wali Kelas - ' . $classroom->name)

@section('content')
<div class="space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.dashboard') }}">Dashboard</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Panel Wali Kelas</x-breadcrumb-item>
    </x-breadcrumbs>

    <!-- HEADER PANEL WALI -->
    <x-card class="!bg-gradient-to-br from-amber-500/10 to-transparent border-amber-500/20 p-6 lg:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-[1.5rem] bg-amber-100 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center shrink-0">
                    <i class="ph-fill ph-star text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">
                        Wali Kelas {{ $classroom->name }}
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 mt-1 font-medium">
                        Tahun Ajaran {{ $classroom->semester->academicYear->name }} • {{ $classroom->semester->type }}
                    </p>
                </div>
            </div>
            
            <x-button variant="secondary" class="shrink-0">
                <i class="ph-bold ph-printer text-lg"></i> Cetak Rekapitulasi
            </x-button>
        </div>
    </x-card>

    <!-- METRIK KELAS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card class="p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center"><i class="ph-fill ph-users-three text-xl"></i></div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Siswa</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $classroom->siswas->count() }}</h3>
            </div>
        </x-card>
        <x-card class="p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="ph-fill ph-chart-line-up text-xl"></i></div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Rata-rata Kelas</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">82.5</h3>
            </div>
        </x-card>
        <x-card class="p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center"><i class="ph-fill ph-warning-circle text-xl"></i></div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Siswa Perhatian Khusus</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">3</h3>
            </div>
        </x-card>
    </div>

    <!-- TABEL DATA SISWA WALI -->
    <x-card class="p-0 overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-black/20">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Siswa Perwalian</h3>
            <div class="w-64"><x-input name="search" placeholder="Cari nama/NISN..." icon="magnifying-glass" class="!py-2" /></div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-white dark:bg-transparent border-b border-slate-100 dark:border-white/5 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                        <th class="p-4 pl-6">Identitas Siswa</th>
                        <th class="p-4 text-center">Kehadiran (Semester)</th>
                        <th class="p-4 text-center">Pelanggaran</th>
                        <th class="p-4 text-center">Status Akademik</th>
                        <th class="p-4 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                    @foreach($classroom->siswas as $siswa)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
                            <td class="p-4 pl-6">
                                <div class="flex items-center gap-3">
                                    <x-avatar src="{{ $siswa->photo ?? '' }}" size="sm" />
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $siswa->user->name }}</span>
                                        <span class="text-[10px] font-medium text-slate-400">{{ $siswa->nisn }} • L/P: {{ $siswa->gender }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2 text-xs font-bold">
                                    <span class="text-emerald-500" title="Hadir">H: 45</span>
                                    <span class="text-amber-500" title="Izin">I: 2</span>
                                    <span class="text-blue-500" title="Sakit">S: 1</span>
                                    <span class="text-red-500" title="Alpa">A: 0</span>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <x-badge color="neutral">0 Poin</x-badge>
                            </td>
                            <td class="p-4 text-center">
                                <x-badge color="primary">Aman</x-badge>
                            </td>
                            <td class="p-4 pr-6 text-right">
                                <x-button variant="secondary" class="!py-1.5 !px-3 !text-xs !rounded-lg"><i class="ph-bold ph-eye"></i> Detail Rapor</x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection