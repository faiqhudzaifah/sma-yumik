@extends('layouts.app')

@section('title', 'Dashboard Premium - LMS')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 auto-rows-auto">
        
        <!-- Hero Section (8 Kolom) -->
        <x-card class="md:col-span-8 lg:row-span-2 flex flex-col justify-between min-h-[280px]">
            <div>
                <p class="text-[10px] sm:text-xs font-bold tracking-widest uppercase text-yumik-primary mb-2">Panel Kontrol Utama</p>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-light tracking-tight text-slate-800 dark:text-white leading-tight">
                    Sistem Informasi<br>
                    <span class="font-bold">Akademik Terpadu.</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-4 max-w-md leading-relaxed hidden sm:block">
                    Kelola data master pengguna, kurikulum, matrik penjadwalan, hingga pelaksanaan asesmen dalam satu ekosistem terpadu.
                </p>
            </div>
            <div class="mt-6 lg:mt-8 w-full md:w-3/4">
                <form action="{{ route('admin.pengguna.index') }}" method="GET">
                    <x-search-bar name="search" placeholder="Cari pengguna / NISN..." />
                </form>
            </div>
        </x-card>

        <!-- Stat: Siswa Aktif (4 Kolom) - INTERAKTIF -->
        <a href="{{ route('admin.pengguna.index', ['role' => 'siswa']) }}" class="block h-full md:col-span-4 outline-none">
            <x-card class="flex flex-col items-start justify-center p-6 h-full hover:border-blue-500/50 hover:shadow-xl hover:shadow-blue-500/10 transition-all group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-blue-500 text-white flex items-center justify-center mb-4 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                    <i class="ph-bold ph-users text-3xl"></i>
                </div>
                <span class="text-4xl font-extrabold text-slate-800 dark:text-white leading-none mb-1">{{ number_format($totalSiswa) }}</span>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-1 group-hover:text-blue-500 transition-colors">Siswa Aktif</span>
            </x-card>
        </a>

        <!-- Stat: Total Guru (4 Kolom) - INTERAKTIF -->
        <a href="{{ route('admin.pengguna.index', ['role' => 'guru']) }}" class="block h-full md:col-span-4 outline-none">
            <x-card class="flex flex-col items-start justify-center p-6 h-full hover:border-emerald-500/50 hover:shadow-xl hover:shadow-emerald-500/10 transition-all group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center mb-4 shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                    <i class="ph-bold ph-chalkboard-teacher text-3xl"></i>
                </div>
                <span class="text-4xl font-extrabold text-slate-800 dark:text-white leading-none mb-1">{{ number_format($totalGuru) }}</span>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-1 group-hover:text-emerald-500 transition-colors">Total Guru</span>
            </x-card>
        </a>

        <!-- Modul Aksi Cepat (12 Kolom) -->
        <div class="md:col-span-12 mt-2">
            <div class="flex justify-between items-end mb-4 px-1">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Aksi Cepat Administratif</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
               <a href="{{ route('admin.pengguna.create') }}" class="block h-full outline-none">
                    <x-card class="flex items-center gap-4 cursor-pointer group h-full hover:border-blue-500/50 hover:shadow-lg transition-all p-4 sm:p-5">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-colors shrink-0">
                            <i class="ph-bold ph-user-plus text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm sm:text-base text-slate-800 dark:text-white group-hover:text-blue-500 transition-colors">Registrasi Akun</h4>
                            <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5">Tambah data profil</p>
                        </div>
                    </x-card>
                </a>

                <a href="{{ route('admin.jadwal.create') }}" class="block h-full outline-none">
                    <x-card class="flex items-center gap-4 cursor-pointer group h-full hover:border-purple-500/50 hover:shadow-lg transition-all p-4 sm:p-5">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400 flex items-center justify-center group-hover:bg-purple-500 group-hover:text-white transition-colors shrink-0">
                            <i class="ph-bold ph-calendar-plus text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm sm:text-base text-slate-800 dark:text-white group-hover:text-purple-500 transition-colors">Susun Jadwal</h4>
                            <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5">Plotting sesi KBM</p>
                        </div>
                    </x-card>
                </a>
                
                <a href="{{ route('admin.rombel.index') }}" class="block h-full outline-none">
                    <x-card class="flex items-center gap-4 cursor-pointer group h-full hover:border-orange-500/50 hover:shadow-lg transition-all p-4 sm:p-5">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400 flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition-colors shrink-0">
                            <i class="ph-bold ph-door text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm sm:text-base text-slate-800 dark:text-white group-hover:text-orange-500 transition-colors">Data Rombel</h4>
                            <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5">Manajemen anggota</p>
                        </div>
                    </x-card>
                </a>
            </div>
        </div>

        <!-- Pantauan Kelas Dinamis (12 Kolom) - INTERAKTIF -->
        <div class="md:col-span-12 mt-2">
            <div class="flex justify-between items-end mb-4 px-1">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Aktivitas Kelas Aktif</h3>
                <a href="{{ route('admin.rombel.index') }}" class="text-[10px] font-bold uppercase tracking-widest text-yumik-primary hover:text-emerald-700 transition-colors">
                    Lihat Semua
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($activeClasses as $kelas)
                    <a href="{{ route('admin.rombel.edit', $kelas->id) }}" class="block group outline-none">
                        <x-card class="flex flex-col justify-between h-full hover:border-yumik-primary/50 hover:shadow-lg transition-all cursor-pointer">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-500 flex items-center justify-center shrink-0 group-hover:bg-yumik-primary/10 group-hover:text-yumik-primary transition-colors">
                                    <i class="ph-bold ph-door text-2xl"></i>
                                </div>
                                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300">
                                    Tingkat {{ $kelas->grade_level }} {{ $kelas->major ? '· '.$kelas->major : '' }}
                                </span>
                            </div>
                            
                            <div>
                                <h4 class="text-xl font-bold text-slate-800 dark:text-white mb-1 group-hover:text-yumik-primary transition-colors">{{ $kelas->name }}</h4>
                                <p class="text-xs text-slate-500 flex items-center gap-1">
                                    Wali: {{ $kelas->waliKelas->user->name ?? 'Belum ditentukan' }}
                                </p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-white/5">
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="text-slate-500 font-medium">Kapasitas Terisi</span>
                                    @php
                                        $percentage = $kelas->capacity > 0 ? min(100, ($kelas->siswas_count / $kelas->capacity) * 100) : 0;
                                        $barColor = $percentage >= 100 ? 'bg-red-500' : ($percentage > 80 ? 'bg-amber-500' : 'bg-yumik-primary');
                                        $textColor = $percentage >= 100 ? 'text-red-500' : 'text-yumik-primary';
                                    @endphp
                                    <span class="font-bold {{ $textColor }}">{{ $kelas->siswas_count }} / {{ $kelas->capacity }}</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-black/30 h-1.5 rounded-full overflow-hidden">
                                    <div class="{{ $barColor }} h-full rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </x-card>
                    </a>
                @empty
                    <div class="col-span-full p-8 text-center bg-white/30 dark:bg-white/5 rounded-3xl border border-slate-200 dark:border-white/10">
                        <i class="ph-fill ph-door text-4xl text-slate-300 dark:text-slate-600 mb-2"></i>
                        <p class="text-sm text-slate-500">Belum ada kelas yang dibuat pada semester ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection