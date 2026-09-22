@extends('layouts.app')

@section('title', 'Proses Kenaikan Kelas - LMS')

@section('content')
    @if(session('success'))
        <x-alert type="success" title="Berhasil!" message="{{ session('success') }}" />
    @endif
    
    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Kenaikan Kelas</x-breadcrumb-item>
        </x-breadcrumbs>

        <div class="mt-2">
            <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white">
                Proses <span class="font-bold">Kenaikan Kelas.</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1">Pilih kelas sumber untuk memproses status akhir siswa (Naik/Tinggal/Lulus).</p>
        </div>
    </div>

    <!-- Toolbar Filter -->
    <div class="flex justify-end mb-6">
        <form action="{{ route('admin.promotion.index') }}" method="GET" class="w-full lg:w-96">
            <x-select name="semester_id" onchange="this.form.submit()" icon="calendar">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" {{ request('semester_id', $semesters->firstWhere('is_active', true)->id ?? '') == $sem->id ? 'selected' : '' }}>
                        {{ $sem->academicYear->name }} - Semester {{ $sem->type }} {{ $sem->is_active ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </x-select>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($classrooms as $rombel)
            <x-card class="flex flex-col h-full hover:border-yumik-primary/50 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="inline-block px-2 py-1 rounded text-[10px] font-bold tracking-widest uppercase bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-300 mb-2">
                            Tingkat {{ $rombel->grade_level }}
                        </span>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $rombel->name }}</h3>
                        <p class="text-xs text-slate-500 mt-1">Total: {{ $rombel->siswas_count }} Siswa</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center shrink-0">
                        <i class="ph-fill ph-graduation-cap text-2xl"></i>
                    </div>
                </div>

                <div class="mt-auto pt-6">
                    <a href="{{ route('admin.promotion.review', $rombel->id) }}" class="block">
                        <x-button variant="primary" class="w-full py-3 bg-purple-600 hover:bg-purple-700 shadow-purple-500/30">
                            <i class="ph-bold ph-arrow-circle-right text-lg"></i> Proses Siswa
                        </x-button>
                    </a>
                </div>
            </x-card>
        @empty
            <div class="col-span-full py-12 text-center text-slate-400">
                <i class="ph-fill ph-empty text-6xl mb-3 opacity-50"></i>
                <p>Belum ada data kelas pada semester ini.</p>
            </div>
        @endforelse
    </div>
@endsection