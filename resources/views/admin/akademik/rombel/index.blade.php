@extends('layouts.app')

@section('title', 'Manajemen Rombongan Belajar - LMS')

@section('content')
    @if(session('success'))
        <x-alert type="success" title="Berhasil!" message="{{ session('success') }}" />
    @endif
    @if(session('error'))
        <x-alert type="error" title="Gagal!" message="{{ session('error') }}" />
    @endif

    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Rombongan Belajar</x-breadcrumb-item>
        </x-breadcrumbs>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mt-2">
            <div>
                <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white">
                    Direktori <span class="font-bold">Kelas.</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Kelola rombongan belajar, kapasitas, dan plotting siswa.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.rombel.create') }}">
                    <x-button variant="primary">
                        <i class="ph-bold ph-plus text-lg"></i> Tambah Kelas Baru
                    </x-button>
                </a>
            </div>
        </div>
    </div>

    <!-- Toolbar Filter -->
    <div class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-6 p-4 rounded-2xl bg-white/50 dark:bg-[#1A1D24]/50 border border-slate-100 dark:border-white/5 backdrop-blur-sm">
        <form action="{{ route('admin.rombel.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3">
            <div class="w-full sm:w-64">
                <x-select name="semester_id" onchange="this.form.submit()" icon="calendar">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester_id', $semesters->firstWhere('is_active', true)->id ?? '') == $sem->id ? 'selected' : '' }}>
                            {{ $sem->academicYear->name }} - Semester {{ $sem->type }} {{ $sem->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full lg:w-96">
                <x-search-bar name="search" value="{{ request('search') }}" placeholder="Cari nama kelas atau kode ruang..." />
            </div>
        </form>
    </div>

    <!-- Bento Grid Kelas -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($classrooms as $rombel)
            <x-card class="flex flex-col h-full group hover:border-yumik-primary/50 transition-colors duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="inline-block px-2 py-1 rounded text-[10px] font-bold tracking-widest uppercase bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300 mb-2">
                            Tingkat {{ $rombel->grade_level }} {{ $rombel->major ? '· '.$rombel->major : '' }}
                        </span>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $rombel->name }}</h3>
                        <p class="text-xs text-slate-500 flex items-center gap-1 mt-1">
                            <i class="ph-fill ph-door"></i> Ruang: {{ $rombel->room_code ?? 'Belum diset' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-yumik-primary/10 text-yumik-primary flex items-center justify-center shrink-0">
                        <i class="ph-fill ph-chalkboard-teacher text-2xl"></i>
                    </div>
                </div>

                <div class="mt-2 mb-6">
                    <p class="text-[10px] font-bold tracking-widest uppercase text-slate-400 mb-1">Wali Kelas</p>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <x-avatar size="sm" />
                        {{ $rombel->waliKelas->user->name ?? 'Belum Ditentukan' }}
                    </p>
                </div>

                <!-- Indikator Kapasitas -->
                <div class="mt-auto pt-4 border-t border-slate-100 dark:border-white/5">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-slate-500 font-medium">Kapasitas Kelas</span>
                        <span class="font-bold {{ $rombel->siswas_count >= $rombel->capacity ? 'text-red-500' : 'text-yumik-primary' }}">
                            {{ $rombel->siswas_count }} / {{ $rombel->capacity }} Siswa
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-black/30 h-1.5 rounded-full overflow-hidden">
                        @php
                            $percentage = $rombel->capacity > 0 ? min(100, ($rombel->siswas_count / $rombel->capacity) * 100) : 0;
                            $barColor = $percentage >= 100 ? 'bg-red-500' : ($percentage > 80 ? 'bg-amber-500' : 'bg-yumik-primary');
                        @endphp
                        <div class="{{ $barColor }} h-full rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex gap-2 mt-5">
                    <a href="{{ route('admin.rombel.show', $rombel->id) }}" class="flex-1">
                        <x-button variant="primary" class="w-full py-2 shadow-none bg-slate-800 hover:bg-slate-700 text-xs">
                            Kelola Anggota
                        </x-button>
                    </a>
                    <a href="{{ route('admin.rombel.edit', $rombel->id) }}">
                        <x-button variant="secondary" class="px-3 py-2 text-xs">
                            <i class="ph-bold ph-pencil-simple"></i>
                        </x-button>
                    </a>
                </div>
            </x-card>
        @empty
            <div class="col-span-full py-12 flex flex-col items-center justify-center text-slate-400">
                <i class="ph-fill ph-empty text-6xl mb-3 opacity-50"></i>
                <p>Belum ada data kelas pada semester ini.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">
        <x-pagination :paginator="$classrooms" />
    </div>
@endsection