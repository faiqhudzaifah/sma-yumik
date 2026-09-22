@extends('layouts.app')

@section('title', 'Kelola Anggota Kelas - LMS')

@section('content')
    @if(session('success'))
        <x-alert type="success" title="Berhasil!" message="{{ session('success') }}" />
    @endif
    @if(session('error'))
        <x-alert type="error" title="Gagal!" message="{{ session('error') }}" />
    @endif

    <div class="mb-8 flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <x-breadcrumbs>
                <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
                <x-breadcrumb-item href="{{ route('admin.rombel.index') }}">Rombongan Belajar</x-breadcrumb-item>
                <x-breadcrumb-item active="true">{{ $rombel->name }}</x-breadcrumb-item>
            </x-breadcrumbs>
            <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white mt-2">
                Plotting <span class="font-bold">{{ $rombel->name }}</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1">Wali Kelas: {{ $rombel->waliKelas->user->name ?? 'Belum ditentukan' }}</p>
        </div>
        
        <div class="flex gap-3">
            <div class="px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-[#1A1D24] text-sm font-bold">
                Kapasitas: <span class="{{ $rombel->siswas->count() >= $rombel->capacity ? 'text-red-500' : 'text-yumik-primary' }}">{{ $rombel->siswas->count() }}</span> / {{ $rombel->capacity }}
            </div>
            <a href="{{ route('admin.rombel.index') }}">
                <x-button variant="secondary" class="shadow-none">Kembali</x-button>
            </a>
        </div>
    </div>

    <!-- UI Transfer List (Bento Split) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- KOLOM KIRI: Siswa yang Belum Memiliki Kelas -->
        <div class="lg:col-span-5 flex flex-col h-[700px]">
            <x-card class="flex-1 flex flex-col overflow-hidden bg-slate-50 dark:bg-black/10 border-dashed border-slate-300 dark:border-white/10">
                <div class="pb-4 border-b border-slate-200 dark:border-white/5">
                    <h3 class="text-sm font-bold tracking-widest uppercase text-slate-500">Siswa Tersedia (Belum Masuk Kelas)</h3>
                    <p class="text-xs text-slate-400 mt-1">Tahun Ajaran Aktif</p>
                </div>
                
                <form action="{{ route('admin.rombel.assign', $rombel->id) }}" method="POST" class="flex-1 flex flex-col overflow-hidden">
                    @csrf
                    <div class="flex-1 overflow-y-auto py-4 pr-2 custom-scrollbar space-y-2">
                        @forelse($availableSiswas as $siswa)
                            <label class="flex items-center gap-4 p-3 rounded-xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#1A1D24] cursor-pointer hover:border-yumik-primary/50 transition-colors">
                                <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" class="w-4 h-4 rounded border-slate-300 text-yumik-primary focus:ring-yumik-primary bg-slate-100 dark:bg-black/20 dark:border-white/10">
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ $siswa->user->name }}</h4>
                                    <span class="text-[10px] text-slate-500 font-mono">{{ $siswa->nisn }}</span>
                                </div>
                            </label>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center text-slate-400 text-center">
                                <i class="ph-fill ph-check-circle text-4xl mb-2 opacity-50"></i>
                                <p class="text-sm">Semua siswa aktif sudah mendapatkan kelas.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-white/5 mt-auto">
                        <x-button variant="primary" type="submit" class="w-full" :disabled="$availableSiswas->isEmpty() || $rombel->siswas->count() >= $rombel->capacity">
                            <i class="ph-bold ph-arrow-right text-lg"></i> Pindahkan ke Kelas Ini
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- KOLOM KANAN: Siswa di dalam Kelas -->
        <div class="lg:col-span-7 flex flex-col h-[700px]">
            <x-card class="flex-1 flex flex-col overflow-hidden border-yumik-primary/20">
                <div class="pb-4 border-b border-slate-200 dark:border-white/5 flex justify-between items-end">
                    <div>
                        <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary">Anggota Kelas ({{ $rombel->name }})</h3>
                        <p class="text-xs text-slate-400 mt-1">Siswa yang terdaftar pada semester ini</p>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto py-4 pr-2 custom-scrollbar space-y-2">
                    @forelse($rombel->siswas as $index => $anggota)
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-black/10 group hover:bg-white dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-4">
                                <span class="text-xs font-bold text-slate-400 w-5">{{ $index + 1 }}</span>
                                <x-avatar size="sm" />
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ $anggota->user->name }}</h4>
                                    <span class="text-[10px] text-slate-500 font-mono">NISN: {{ $anggota->nisn }} | L/P: {{ $anggota->gender }}</span>
                                </div>
                            </div>
                            
                            <!-- Tombol Keluarkan -->
                            <form action="{{ route('admin.rombel.remove', ['rombel' => $rombel->id, 'siswa' => $anggota->id]) }}" method="POST" onsubmit="return confirm('Keluarkan {{ $anggota->user->name }} dari kelas ini?');">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-full bg-red-50 text-red-500 dark:bg-red-500/10 hover:bg-red-500 hover:text-white flex items-center justify-center transition-colors shadow-sm opacity-0 group-hover:opacity-100 focus:opacity-100">
                                    <i class="ph-bold ph-minus"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-slate-400 text-center">
                            <i class="ph-fill ph-users-three text-5xl mb-3 opacity-50"></i>
                            <p class="text-sm">Belum ada siswa yang dimasukkan ke kelas ini.</p>
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

    </div>
@endsection