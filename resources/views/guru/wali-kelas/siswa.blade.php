@extends('layouts.app')

@section('title', 'Daftar Siswa - ' . $classroom->name)

@section('content')
<div class="space-y-6">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('guru.dashboard') }}" class="hover:text-yumik-primary transition-colors">Dashboard</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <a href="{{ route('guru.wali-kelas.dashboard') }}" class="hover:text-yumik-primary transition-colors">Panel Wali Kelas</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold">Daftar Siswa</span>
    </nav>

    {{-- HEADER --}}
    <x-card class="p-6 lg:p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-500/20 text-blue-500 flex items-center justify-center shrink-0">
                    <i class="ph-fill ph-users text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white">Daftar Siswa</h1>
                    <p class="text-sm text-slate-500 mt-1">
                        <span class="font-bold text-yumik-primary">{{ $siswas->count() }}</span> siswa · Kelas {{ $classroom->name }}
                    </p>
                </div>
            </div>

            <form action="{{ route('guru.wali-kelas.siswa') }}" method="GET" class="w-full md:w-64">
                <div class="relative">
                    <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari nama / NISN..."
                        class="w-full pl-11 pr-4 py-3 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all">
                </div>
            </form>
        </div>
    </x-card>

    {{-- LIST --}}
    @if($siswas->isEmpty())
        <x-card class="!p-0">
            <x-empty-state icon="users" title="Tidak ada siswa" description="Belum ada siswa terdaftar di kelas ini atau pencarian tidak ditemukan." />
        </x-card>
    @else
        <x-card class="!p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-black/20 border-b border-slate-100 dark:border-white/10">
                            <th class="p-4 pl-6 text-[10px] font-bold uppercase tracking-widest text-slate-400 w-12">#</th>
                            <th class="p-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Nama Siswa</th>
                            <th class="p-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">NISN / NIS</th>
                            <th class="p-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Gender</th>
                            <th class="p-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Kontak</th>
                            <th class="p-4 pr-6 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach($siswas as $index => $siswa)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
                                <td class="p-4 pl-6 text-sm text-slate-400 font-medium">{{ $index + 1 }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <x-avatar src="{{ $siswa->photo ? asset('storage/' . $siswa->photo) : '' }}" size="sm" />
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $siswa->user->name ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $siswa->user->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $siswa->nisn ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400">NIS: {{ $siswa->nis ?? '-' }}</p>
                                </td>
                                <td class="p-4 text-center">
                                    <x-badge color="neutral">
                                        {{ $siswa->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </x-badge>
                                </td>
                                <td class="p-4">
                                    <p class="text-xs text-slate-600 dark:text-slate-300">{{ $siswa->phone ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $siswa->parent_phone ?? '' }}</p>
                                </td>
                                <td class="p-4 pr-6 text-right">
                                    <a href="{{ route('guru.wali-kelas.siswa.detail', $siswa->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-yumik-primary/10 text-yumik-primary text-xs font-bold hover:bg-yumik-primary/20 transition-colors">
                                        Detail <i class="ph-bold ph-arrow-right text-xs"></i>
                                    </a>
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