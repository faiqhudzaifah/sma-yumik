@extends('layouts.app')

@section('title', 'Detail Tahun Ajaran - LMS')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <x-breadcrumbs>
                <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
                <x-breadcrumb-item href="{{ route('admin.tahun-ajaran.index') }}">Tahun Ajaran</x-breadcrumb-item>
                <x-breadcrumb-item active="true">Detail Rekapitulasi</x-breadcrumb-item>
            </x-breadcrumbs>
            <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white mt-2">
                Buku Induk <span class="font-bold">Semester.</span>
            </h2>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.tahun-ajaran.index') }}">
                <x-button variant="secondary" class="shadow-none">Kembali</x-button>
            </a>
        </div>
    </div>

    <!-- Area Status & Aksi Utama (Bento Grid Header) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <x-card class="lg:col-span-8 flex flex-col justify-between bg-gradient-to-br from-yumik-primary/10 to-transparent border-yumik-primary/20">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="text-xs font-bold tracking-widest uppercase text-yumik-primary mb-1 block">Tahun Ajaran</span>
                    <h3 class="text-4xl font-bold text-slate-800 dark:text-white leading-tight">
                        {{ $tahun_ajaran->academicYear->name }} <br>
                        <span class="text-2xl font-medium text-slate-500">Semester {{ $tahun_ajaran->type }}</span>
                    </h3>
                </div>
                @if($tahun_ajaran->is_active)
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> SEDANG AKTIF
                    </span>
                @else
                    <span class="inline-flex px-4 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                        ARSIP SELESAI
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-6 mt-auto border-t border-slate-200/50 dark:border-white/5 pt-4">
                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                    <i class="ph-fill ph-calendar-plus text-slate-400"></i>
                    <span class="font-bold text-slate-800 dark:text-white">Mulai:</span> 
                    {{ $tahun_ajaran->start_date ? \Carbon\Carbon::parse($tahun_ajaran->start_date)->format('d M Y') : '-' }}
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                    <i class="ph-fill ph-calendar-check text-slate-400"></i>
                    <span class="font-bold text-slate-800 dark:text-white">Selesai:</span> 
                    {{ $tahun_ajaran->end_date ? \Carbon\Carbon::parse($tahun_ajaran->end_date)->format('d M Y') : '-' }}
                </div>
            </div>
        </x-card>

        <x-card class="lg:col-span-4 flex flex-col justify-center gap-4">
            <h4 class="text-xs font-bold tracking-widest uppercase text-slate-400 mb-2">Tindakan Semester</h4>
            
            <a href="{{ route('admin.tahun-ajaran.export', $tahun_ajaran->id) }}" class="block">
                <x-button variant="primary" class="w-full py-4 justify-start pl-5 shadow-lg bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30">
                    <i class="ph-bold ph-file-zip text-xl mr-3"></i> Export & Backup Data
                </x-button>
            </a>
            
            <a href="{{ route('admin.tahun-ajaran.edit', $tahun_ajaran->id) }}" class="block">
                <x-button variant="secondary" class="w-full py-4 justify-start pl-5">
                    <i class="ph-bold ph-pencil-simple text-xl mr-3 text-amber-500"></i> Edit / Ubah Status
                </x-button>
            </a>
        </x-card>
    </div>

    <!-- Modul Tautan Cepat (Terfilter otomatis berdasarkan semester ini) -->
    <h3 class="text-lg font-bold text-slate-800 dark:text-white mt-8 mb-4">Eksplorasi Modul Semester Ini</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Pintasan Rombel -->
        <a href="{{ route('admin.rombel.index', ['semester_id' => $tahun_ajaran->id]) }}" class="block h-full">
            <x-card class="h-full group hover:border-orange-500/50 transition-all flex flex-col justify-between">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 dark:bg-orange-500/10 flex items-center justify-center shrink-0">
                        <i class="ph-bold ph-door text-lg"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $stats['rombel'] }}</span>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-white group-hover:text-orange-500 transition-colors">Rombongan Belajar</h4>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-1 font-bold">Lihat Detail &rarr;</p>
                </div>
            </x-card>
        </a>

        <!-- Pintasan Mapel -->
        <a href="{{ route('admin.mapel.index', ['semester_id' => $tahun_ajaran->id]) }}" class="block h-full">
            <x-card class="h-full group hover:border-blue-500/50 transition-all flex flex-col justify-between">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 dark:bg-blue-500/10 flex items-center justify-center shrink-0">
                        <i class="ph-bold ph-book-bookmark text-lg"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $stats['mapel'] }}</span>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-white group-hover:text-blue-500 transition-colors">Mata Pelajaran</h4>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-1 font-bold">Lihat Detail &rarr;</p>
                </div>
            </x-card>
        </a>

        <!-- Pintasan Jadwal -->
        <a href="{{ route('admin.jadwal.index', ['semester_id' => $tahun_ajaran->id]) }}" class="block h-full">
            <x-card class="h-full group hover:border-yumik-primary/50 transition-all flex flex-col justify-between">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 flex items-center justify-center shrink-0">
                        <i class="ph-bold ph-calendar-plus text-lg"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $stats['jadwal'] }}</span>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-white group-hover:text-yumik-primary transition-colors">Matriks Jadwal</h4>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-1 font-bold">Lihat Detail &rarr;</p>
                </div>
            </x-card>
        </a>

        <!-- Pintasan Presensi & Nilai (Placeholder visual untuk pengembangan ke depan) -->
        <div class="block h-full opacity-60">
            <x-card class="h-full flex flex-col justify-between border-dashed">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 dark:bg-white/5 flex items-center justify-center shrink-0">
                        <i class="ph-bold ph-exam text-lg"></i>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-slate-500 dark:text-slate-400">Presensi & Penilaian</h4>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-1 font-bold">Modul Guru Khusus</p>
                </div>
            </x-card>
        </div>

    </div>
@endsection