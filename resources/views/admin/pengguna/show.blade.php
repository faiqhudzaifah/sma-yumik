@extends('layouts.app')

@section('title', 'Detail Pengguna - LMS')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <x-breadcrumbs>
                <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
                <x-breadcrumb-item href="{{ route('admin.pengguna.index') }}">Kelola Pengguna</x-breadcrumb-item>
                <x-breadcrumb-item active="true">Profil Pengguna</x-breadcrumb-item>
            </x-breadcrumbs>
            <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white mt-2">
                Detail <span class="font-bold">Entitas.</span>
            </h2>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.pengguna.edit', $pengguna->id) }}">
                <x-button variant="secondary">
                    <i class="ph-bold ph-pencil-simple text-lg"></i> Edit Data
                </x-button>
            </a>
            <a href="{{ route('admin.pengguna.index') }}">
                <x-button variant="primary" class="bg-slate-800 hover:bg-slate-700 shadow-slate-800/30">
                    <i class="ph-bold ph-arrow-left text-lg"></i> Kembali
                </x-button>
            </a>
        </div>
    </div>

    <!-- Bento Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        
        <!-- Identitas Utama (8 Kolom) -->
        <x-card class="md:col-span-8 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8 mb-8">
                <div class="shrink-0 relative">
                    <x-avatar size="xl" status="online" />
                    @php
                        $roleColors = [
                            'admin' => 'bg-purple-500',
                            'guru' => 'bg-blue-500',
                            'siswa' => 'bg-emerald-500',
                            'operator' => 'bg-amber-500',
                        ];
                        $badgeBg = $roleColors[$pengguna->role->name] ?? 'bg-slate-500';
                    @endphp
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-[9px] font-bold tracking-widest uppercase text-white {{ $badgeBg }} shadow-lg shadow-black/10">
                        {{ $pengguna->role->name }}
                    </div>
                </div>
                
                <div class="text-center sm:text-left flex-1 mt-4 sm:mt-0">
                    @php
                        $fullName = $pengguna->name;
                        if ($pengguna->role_id == 2 && $pengguna->guru) {
                            $front = $pengguna->guru->front_title ? $pengguna->guru->front_title . ' ' : '';
                            $back = $pengguna->guru->back_title ? ', ' . $pengguna->guru->back_title : '';
                            $fullName = $front . $pengguna->name . $back;
                        }
                    @endphp
                    <h3 class="text-3xl font-bold text-slate-800 dark:text-white mb-1">{{ $fullName }}</h3>
                    <p class="text-slate-500 font-medium font-mono text-sm">{{ $pengguna->username }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-slate-200 dark:border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-400 shrink-0">
                        <i class="ph-fill ph-envelope-simple text-xl"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Email Utama</span>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $pengguna->email ?? '-' }}</span>
                    </div>
                </div>
                @php
                    $phone = $pengguna->role_id == 2 ? $pengguna->guru?->phone : ($pengguna->role_id == 3 ? $pengguna->siswa?->phone : '-');
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-400 shrink-0">
                        <i class="ph-fill ph-phone text-xl"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Nomor Telepon</span>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $phone ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Metadata Sistem (4 Kolom) -->
        <x-card class="md:col-span-4 flex flex-col justify-between bg-gradient-to-br from-white/60 to-white/30 dark:from-[#1A1D24]/60 dark:to-[#1A1D24]/30">
            <div>
                <h4 class="text-xs font-bold tracking-widest uppercase text-slate-400 mb-6">Metadata Sistem</h4>
                <div class="flex flex-col gap-4">
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-white/5">
                        <span class="text-sm text-slate-500">ID Pengguna</span>
                        <span class="text-sm font-bold font-mono text-slate-700 dark:text-slate-300">#{{ str_pad($pengguna->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-white/5">
                        <span class="text-sm text-slate-500">Registrasi</span>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $pengguna->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Pembaruan</span>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $pengguna->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Data Demografi (Semua Peran, 4 Kolom) -->
        @php
            $profile = $pengguna->role_id == 2 ? $pengguna->guru : ($pengguna->role_id == 3 ? $pengguna->siswa : null);
        @endphp
        @if(in_array($pengguna->role_id, [2, 3]))
        <x-card class="md:col-span-4">
            <h4 class="text-xs font-bold tracking-widest uppercase text-yumik-primary mb-6">Demografi & Alamat</h4>
            <div class="flex flex-col gap-4">
                <div>
                    <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Tempat, Tanggal Lahir</span>
                    <span class="text-sm font-semibold text-slate-800 dark:text-white">
                        {{ $profile?->place_of_birth ?? '-' }}, {{ $profile?->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->format('d F Y') : '-' }}
                    </span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Jenis Kelamin</span>
                    <span class="text-sm font-semibold text-slate-800 dark:text-white">{{ $profile?->gender == 'L' ? 'Laki-laki' : ($profile?->gender == 'P' ? 'Perempuan' : '-') }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Agama</span>
                    <span class="text-sm font-semibold text-slate-800 dark:text-white">{{ $profile?->religion ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Alamat Tempat Tinggal</span>
                    <span class="text-sm font-semibold text-slate-800 dark:text-white">{{ $profile?->address ?? '-' }}</span>
                </div>
            </div>
        </x-card>

        <!-- Data Spesifik Guru/Siswa (8 Kolom) -->
        <x-card class="md:col-span-8">
            <h4 class="text-xs font-bold tracking-widest uppercase text-yumik-primary mb-6">
                {{ $pengguna->role_id == 2 ? 'Administrasi Kepegawaian' : 'Administrasi Akademik' }}
            </h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @if($pengguna->role_id == 2)
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">NIP (Nomor Induk Pegawai)</span>
                        <span class="text-lg font-bold text-slate-800 dark:text-white">{{ $pengguna->guru?->nip ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">NUPTK</span>
                        <span class="text-lg font-bold text-slate-800 dark:text-white">{{ $pengguna->guru?->nuptk ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Status Kepegawaian</span>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            {{ $pengguna->guru?->employment_status ?? '-' }}
                        </span>
                    </div>
                @elseif($pengguna->role_id == 3)
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">NISN / NIS</span>
                        <span class="text-lg font-bold text-slate-800 dark:text-white">{{ $pengguna->siswa?->nisn ?? '-' }} <span class="text-slate-400 text-sm font-normal">/ {{ $pengguna->siswa?->nis ?? '-' }}</span></span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Status Kesiswaan</span>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $pengguna->siswa?->status ?? 'Aktif' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Tahun Masuk</span>
                        <span class="text-lg font-bold text-slate-800 dark:text-white">{{ $pengguna->siswa?->enrollment_year ?? '-' }}</span>
                    </div>
                    <div class="sm:col-span-2 pt-4 mt-2 border-t border-slate-100 dark:border-white/5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Nama Ayah / Ibu</span>
                            <span class="text-sm font-semibold text-slate-800 dark:text-white">{{ $pengguna->siswa?->father_name ?? '-' }} / {{ $pengguna->siswa?->mother_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">Nomor Kontak Wali</span>
                            <span class="text-sm font-semibold text-slate-800 dark:text-white">{{ $pengguna->siswa?->parent_phone ?? '-' }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </x-card>
        @endif
    </div>
@endsection