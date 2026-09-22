@extends('layouts.app')
@section('title', 'Tambah Tahun Ajaran - LMS')

@section('content')
    <!-- Notifikasi Validasi & Error -->
    @if ($errors->any()) 
        <x-alert type="warning" title="Periksa Input" message="Beberapa form belum diisi dengan benar." /> 
    @endif
    @if(session('error')) 
        <x-alert type="error" title="Gagal Menyimpan!" message="{{ session('error') }}" /> 
    @endif

    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item href="{{ route('admin.tahun-ajaran.index') }}">Tahun Ajaran</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Tambah Baru</x-breadcrumb-item>
        </x-breadcrumbs>
    </div>

    <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-6">
        @csrf
        
        <div class="md:col-span-8 flex flex-col gap-6">
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Detail Periode</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TAHUN AJARAN</label>
                        <x-input type="text" icon="calendar" name="name" value="{{ old('name') }}" placeholder="Misal: 2026/2027" required />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">SEMESTER</label>
                        <x-select icon="books" name="semester" required>
                            <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TANGGAL MULAI</label>
                        <x-input type="date" icon="calendar-plus" name="start_date" value="{{ old('start_date') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TANGGAL BERAKHIR</label>
                        <x-input type="date" icon="calendar-check" name="end_date" value="{{ old('end_date') }}" />
                    </div>
                </div>
            </x-card>
        </div>

        <div class="md:col-span-4 flex flex-col gap-6">
            <x-card class="bg-emerald-50/50 dark:bg-emerald-900/10 border-emerald-100 dark:border-emerald-800/30">
                <h3 class="text-sm font-bold tracking-widest uppercase text-emerald-600 dark:text-emerald-400 mb-2">Status Sistem</h3>
                <p class="text-xs text-slate-500 mb-6">Jika diaktifkan, periode lama otomatis dialihkan menjadi arsip.</p>
                
                <div class="flex items-center justify-between p-4 rounded-2xl bg-white dark:bg-[#1A1D24] shadow-sm">
                    <span class="text-sm font-bold text-slate-800 dark:text-white">Jadikan Aktif</span>
                    <x-toggle name="is_active" value="1" :checked="old('is_active') == '1'" />
                </div>
            </x-card>

            <div class="flex flex-col gap-3 mt-auto">
                <x-button variant="primary" type="submit" class="w-full py-4">
                    <i class="ph-bold ph-floppy-disk text-lg"></i> Simpan Periode
                </x-button>
                <a href="{{ route('admin.tahun-ajaran.index') }}">
                    <x-button variant="secondary" type="button" class="w-full py-4">Batal</x-button>
                </a>
            </div>
        </div>
    </form>
@endsection