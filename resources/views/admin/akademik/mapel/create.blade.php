@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran - LMS')

@section('content')
    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item href="{{ route('admin.mapel.index') }}">Mata Pelajaran</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Tambah Baru</x-breadcrumb-item>
        </x-breadcrumbs>
    </div>

    @if ($errors->any())
        <x-alert type="warning" title="Periksa Input Anda" message="Beberapa field belum diisi dengan benar." />
    @endif

    <form action="{{ route('admin.mapel.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf

        <div class="lg:col-span-8 flex flex-col gap-6">
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Informasi Kurikulum</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NAMA MATA PELAJARAN</label>
                        <x-input type="text" icon="book-bookmark" name="name" value="{{ old('name') }}" placeholder="Contoh: Pendidikan Agama Islam" required />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">KODE MAPEL</label>
                        <x-input type="text" icon="hash" name="code" value="{{ old('code') }}" placeholder="Contoh: PAI-10" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">GURU PENGAMPU UTAMA</label>
                        <x-select icon="chalkboard-teacher" name="guru_id">
                            <option value="">-- Pilih Guru Pengampu --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->user->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-4 flex flex-col gap-6">
            <x-card class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-white/5 dark:to-transparent">
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-4">Target Pelaksanaan</h3>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">PERIODE SEMESTER</label>
                    <x-select icon="calendar" name="semester_id" required>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ (old('semester_id') ?? $semesters->firstWhere('is_active', true)->id ?? '') == $sem->id ? 'selected' : '' }}>
                                {{ $sem->academicYear->name }} - {{ $sem->type }}
                            </option>
                        @endforeach
                    </x-select>
                </div>
            </x-card>

            <div class="flex flex-col gap-3 mt-auto">
                <x-button variant="primary" type="submit" class="w-full py-4 shadow-lg">
                    <i class="ph-bold ph-floppy-disk text-lg"></i> Simpan Mapel
                </x-button>
                <a href="{{ route('admin.mapel.index') }}" class="w-full">
                    <x-button variant="secondary" type="button" class="w-full py-4 border-none bg-transparent shadow-none text-slate-500">Batal</x-button>
                </a>
            </div>
        </div>
    </form>
@endsection