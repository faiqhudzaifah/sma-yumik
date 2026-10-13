@extends('layouts.app')

@section('title', $siswa->user->name . ' - Detail Siswa')

@section('content')
<div class="space-y-6">

    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('guru.dashboard') }}" class="hover:text-yumik-primary transition-colors">Dashboard</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <a href="{{ route('guru.wali-kelas.dashboard') }}" class="hover:text-yumik-primary transition-colors">Panel Wali Kelas</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <a href="{{ route('guru.wali-kelas.siswa') }}" class="hover:text-yumik-primary transition-colors">Daftar Siswa</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold">{{ $siswa->user->name }}</span>
    </nav>

    <x-card class="p-6 lg:p-8">
        <div class="flex flex-col md:flex-row items-start gap-6">
            <x-avatar src="{{ $siswa->photo ? asset('storage/' . $siswa->photo) : '' }}" size="xl" />

            <div class="flex-1">
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $siswa->user->name }}</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $siswa->user->email }}</p>
                <div class="flex flex-wrap gap-2 mt-4">
                    <x-badge color="accent">NISN: {{ $siswa->nisn ?? '-' }}</x-badge>
                    <x-badge color="neutral">NIS: {{ $siswa->nis ?? '-' }}</x-badge>
                    <x-badge color="neutral">{{ $siswa->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</x-badge>
                </div>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-card class="p-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Kontak</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Telepon Siswa</p>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $siswa->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Telepon Orang Tua</p>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $siswa->parent_phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Alamat</p>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $siswa->address ?? '-' }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="p-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Data Orang Tua</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Nama Ayah</p>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $siswa->father_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Nama Ibu</p>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $siswa->mother_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Nama Wali</p>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $siswa->guardian_name ?? '-' }}</p>
                </div>
            </div>
        </x-card>
    </div>

</div>
@endsection