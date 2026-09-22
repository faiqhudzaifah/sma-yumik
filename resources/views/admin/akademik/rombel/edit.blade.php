@extends('layouts.app')

@section('title', 'Edit Kelas - LMS')

@section('content')
    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item href="{{ route('admin.rombel.index') }}">Rombongan Belajar</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Edit Kelas</x-breadcrumb-item>
        </x-breadcrumbs>
    </div>

    @if ($errors->any())
        <x-alert type="warning" title="Periksa Input Anda" message="Beberapa field belum diisi dengan benar." />
    @endif

    <form action="{{ route('admin.rombel.update', $rombel->id) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        <div class="lg:col-span-8 flex flex-col gap-6">
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Identitas Kelas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NAMA KELAS</label>
                        <x-input type="text" icon="text-t" name="name" value="{{ old('name', $rombel->name) }}" required />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">KODE RUANGAN / FISIK</label>
                        <x-input type="text" icon="door" name="room_code" value="{{ old('room_code', $rombel->room_code) }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TINGKAT / JENJANG</label>
                        <x-select icon="stairs" name="grade_level" required>
                            <option value="10" {{ old('grade_level', $rombel->grade_level) == '10' ? 'selected' : '' }}>Kelas 10 (X)</option>
                            <option value="11" {{ old('grade_level', $rombel->grade_level) == '11' ? 'selected' : '' }}>Kelas 11 (XI)</option>
                            <option value="12" {{ old('grade_level', $rombel->grade_level) == '12' ? 'selected' : '' }}>Kelas 12 (XII)</option>
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JURUSAN (OPSIONAL)</label>
                        <x-input type="text" icon="student" name="major" value="{{ old('major', $rombel->major) }}" />
                    </div>
                </div>
            </x-card>

            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Penugasan Wali Kelas</h3>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">PILIH GURU WALI KELAS</label>
                        <x-select icon="chalkboard-teacher" name="wali_kelas_id">
                        <option value="">-- Tanpa Wali Kelas --</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}" {{ old('wali_kelas_id', $rombel->wali_kelas_id) == $guru->id ? 'selected' : '' }}>
                                {{ $guru->user->name }} (NIP: {{ $guru->nip ?? '-' }})
                            </option>
                        @endforeach
                    </x-select>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-4 flex flex-col gap-6">
            <x-card class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-white/5 dark:to-transparent">
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-4">Pengaturan Logis</h3>
                
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">PERIODE SEMESTER</label>
                    <x-select icon="calendar" name="semester_id" required>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ old('semester_id', $rombel->semester_id) == $sem->id ? 'selected' : '' }}>
                                {{ $sem->academicYear->name }} - {{ $sem->type }}
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">KAPASITAS MAKSIMAL</label>
                    <x-input type="number" icon="users" name="capacity" value="{{ old('capacity', $rombel->capacity) }}" required />
                </div>
            </x-card>

            <div class="flex flex-col gap-3 mt-auto">
                <x-button variant="primary" type="submit" class="w-full py-4 shadow-lg">
                    <i class="ph-bold ph-check text-lg"></i> Simpan Perubahan
                </x-button>
                <a href="{{ route('admin.rombel.index') }}" class="w-full">
                    <x-button variant="secondary" type="button" class="w-full py-4 border-none bg-transparent shadow-none text-slate-500">Batal</x-button>
                </a>
            </div>
        </div>
    </form>
@endsection