@extends('layouts.app')

@section('title', 'Panel Wali Kelas - ' . $classroom->name)

@section('content')
<div class="space-y-8">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('guru.dashboard') }}" class="hover:text-yumik-primary transition-colors">Dashboard</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold">Panel Wali Kelas</span>
    </nav>

    {{-- HERO --}}
    <x-card class="!bg-gradient-to-br from-amber-500/10 to-transparent border-amber-500/20 p-6 lg:p-8">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
            <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30 shrink-0">
                <i class="ph-fill ph-star text-3xl"></i>
            </div>
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <x-badge color="accent">Wali Kelas</x-badge>
                    <x-badge color="neutral">{{ $activeSemester->type }} {{ $activeSemester->academicYear->name ?? '' }}</x-badge>
                </div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800 dark:text-white">
                    {{ $classroom->name }}
                </h1>
                <p class="text-slate-600 dark:text-slate-400 font-medium mt-1">
                    {{ $classroom->major ?? 'Umum' }} · Tingkat {{ $classroom->grade_level }}
                </p>
            </div>
        </div>
    </x-card>

    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <x-card class="!p-6">
            <div class="w-12 h-12 rounded-2xl bg-blue-500 text-white flex items-center justify-center mb-4 shadow-lg shadow-blue-500/30">
                <i class="ph-fill ph-users-three text-2xl"></i>
            </div>
            <h2 class="text-4xl font-black text-slate-800 dark:text-white">{{ $totalSiswa }}</h2>
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">Total Siswa</p>
        </x-card>

        <x-card class="!p-6">
            <div class="w-12 h-12 rounded-2xl bg-purple-500 text-white flex items-center justify-center mb-4 shadow-lg shadow-purple-500/30">
                <i class="ph-fill ph-books text-2xl"></i>
            </div>
            <h2 class="text-4xl font-black text-slate-800 dark:text-white">{{ $totalMapel }}</h2>
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">Mata Pelajaran</p>
        </x-card>

        <x-card class="!p-6">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center mb-4 shadow-lg shadow-emerald-500/30">
                <i class="ph-fill ph-check-circle text-2xl"></i>
            </div>
            <h2 class="text-4xl font-black text-slate-800 dark:text-white">{{ $persenKehadiran }}%</h2>
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">Rata-rata Kehadiran</p>
        </x-card>
    </div>

    {{-- MENU --}}
       {{-- MENU --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <a href="{{ route('guru.wali-kelas.siswa') }}" class="block group">
            <x-card class="!p-6 hover:!shadow-[0_20px_50px_-15px_rgba(24,201,93,0.3)] hover:-translate-y-1 transition-all h-full">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-500/20 text-blue-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ph-fill ph-users text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-yumik-primary transition-colors">Daftar Siswa</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $totalSiswa }} siswa terdaftar</p>
                        <p class="text-xs font-bold text-yumik-primary mt-3 flex items-center gap-1">
                            Lihat <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </p>
                    </div>
                </div>
            </x-card>
        </a>

        <a href="{{ route('guru.wali-kelas.jadwal') }}" class="block group">
            <x-card class="!p-6 hover:!shadow-[0_20px_50px_-15px_rgba(24,201,93,0.3)] hover:-translate-y-1 transition-all h-full">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-orange-100 dark:bg-orange-500/20 text-orange-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ph-fill ph-calendar-blank text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-yumik-primary transition-colors">Jadwal Kelas</h3>
                        <p class="text-sm text-slate-500 mt-1">Jadwal mingguan kelas ini</p>
                        <p class="text-xs font-bold text-yumik-primary mt-3 flex items-center gap-1">
                            Lihat <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </p>
                    </div>
                </div>
            </x-card>
        </a>

        <a href="{{ route('guru.wali-kelas.kehadiran') }}" class="block group">
            <x-card class="!p-6 hover:!shadow-[0_20px_50px_-15px_rgba(24,201,93,0.3)] hover:-translate-y-1 transition-all h-full">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ph-fill ph-calendar-check text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-yumik-primary transition-colors">Rekap Kehadiran</h3>
                        <p class="text-sm text-slate-500 mt-1">Presensi seluruh mapel</p>
                        <p class="text-xs font-bold text-yumik-primary mt-3 flex items-center gap-1">
                            Lihat <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </p>
                    </div>
                </div>
            </x-card>
        </a>

        <a href="{{ route('guru.wali-kelas.kehadiran.matrix') }}" class="block group">
            <x-card class="!p-6 hover:!shadow-[0_20px_50px_-15px_rgba(24,201,93,0.3)] hover:-translate-y-1 transition-all h-full">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 dark:bg-purple-500/20 text-purple-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ph-fill ph-grid-four text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-yumik-primary transition-colors">Matrix Kehadiran</h3>
                        <p class="text-sm text-slate-500 mt-1">Detail per pertemuan</p>
                        <p class="text-xs font-bold text-yumik-primary mt-3 flex items-center gap-1">
                            Lihat <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </p>
                    </div>
                </div>
            </x-card>
        </a>

        <x-card class="!p-6 opacity-60 h-full">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center shrink-0">
                    <i class="ph-fill ph-note-pencil text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-slate-500">Catatan Perilaku</h3>
                    <p class="text-sm text-slate-400 mt-1">Fitur belum tersedia</p>
                    <p class="text-xs font-bold text-slate-400 mt-3 uppercase tracking-widest">Coming Soon</p>
                </div>
            </div>
        </x-card>
    </div>
</div>
@endsection