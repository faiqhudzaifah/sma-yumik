@extends('layouts.app')

@section('title', 'Susun Jadwal Pelajaran - LMS')

@section('content')
    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item href="{{ route('admin.jadwal.index') }}">Jadwal Pelajaran</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Susun Jadwal</x-breadcrumb-item>
        </x-breadcrumbs>
    </div>

    @if ($errors->any())
        <x-alert type="warning" title="Peringatan Validasi" message="Periksa kembali input Anda." />
    @endif
    @if(session('error'))
        <x-alert type="error" title="Jadwal Bentrok!" message="{{ session('error') }}" />
    @endif

    <form action="{{ route('admin.jadwal.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf

        <div class="lg:col-span-8 flex flex-col gap-6">
            <!-- Entitas Utama -->
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Target Entitas</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">ROMBONGAN BELAJAR</label>
                        <x-select icon="users-three" name="classroom_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $kelas)
                                <option value="{{ $kelas->id }}" {{ old('classroom_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">MATA PELAJARAN</label>
                        <x-select icon="book-bookmark" name="subject_id" required>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($subjects as $mapel)
                                <option value="{{ $mapel->id }}" {{ old('subject_id') == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">GURU PENGAMPU</label>
                        <x-select icon="chalkboard-teacher" name="guru_id" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->user->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                </div>
            </x-card>

            <!-- Dimensi Waktu & Ruang -->
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Penempatan Waktu & Lokasi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">HARI</label>
                        <x-select icon="calendar-blank" name="day_of_week" required>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                <option value="{{ $hari }}" {{ old('day_of_week') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JAM MULAI (KE-)</label>
                        <x-input type="number" icon="sort-ascending" name="jam_ke_mulai" value="{{ old('jam_ke_mulai') }}" required min="1" placeholder="Misal: 1" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JAM SELESAI (KE-)</label>
                        <x-input type="number" icon="sort-descending" name="jam_ke_selesai" value="{{ old('jam_ke_selesai') }}" required min="1" placeholder="Misal: 3" />
                    </div>
                    <div class="md:col-span-3 pt-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2 flex justify-between">
                            <span>RUANGAN SPESIFIK (OPSIONAL)</span>
                            <span class="font-normal text-[10px]">Kosongkan jika di kelas utama</span>
                        </label>
                        <x-input type="text" icon="door" name="ruangan" value="{{ old('ruangan') }}" placeholder="Contoh: Laboratorium Komputer 1" />
                    </div>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-4 flex flex-col gap-6">
            <x-card class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-white/5 dark:to-transparent">
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-4">Pengaturan Sistem</h3>
                
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
                
                <div class="mt-5 p-4 rounded-xl border border-blue-200 dark:border-blue-500/20 bg-blue-50/50 dark:bg-blue-500/10">
                    <div class="flex items-start gap-3 text-blue-600 dark:text-blue-400">
                        <i class="ph-fill ph-info text-xl shrink-0 mt-0.5"></i>
                        <p class="text-xs leading-relaxed">Sistem akan menolak penyimpanan jika mendeteksi adanya bentrok jadwal pada guru yang sama, atau penggunaan ruangan kelas pada rentang jam yang bersinggungan.</p>
                    </div>
                </div>
            </x-card>

            <div class="flex flex-col gap-3 mt-auto">
                <x-button variant="primary" type="submit" class="w-full py-4 shadow-lg">
                    <i class="ph-bold ph-floppy-disk text-lg"></i> Simpan Jadwal
                </x-button>
                <a href="{{ route('admin.jadwal.index') }}" class="w-full">
                    <x-button variant="secondary" type="button" class="w-full py-4 border-none bg-transparent shadow-none text-slate-500">Batal</x-button>
                </a>
            </div>
        </div>
    </form>
@endsection