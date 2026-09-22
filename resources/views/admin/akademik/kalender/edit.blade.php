@extends('layouts.app')

@section('title', 'Tambah Agenda Kalender - LMS')

@section('content')
    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item href="{{ route('admin.kalender.index') }}">Kalender Akademik</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Tambah Agenda</x-breadcrumb-item>
        </x-breadcrumbs>
    </div>

    @if ($errors->any())
        <x-alert type="warning" title="Periksa Input Anda" message="Beberapa field belum diisi dengan benar." />
    @endif

    <form action="{{ route('admin.kalender.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf

        <div class="lg:col-span-8 flex flex-col gap-6">
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Informasi Kegiatan</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NAMA AGENDA / KEGIATAN</label>
                        <x-input type="text" icon="text-t" name="event_name" value="{{ old('event_name') }}" placeholder="Contoh: Ujian Tengah Semester Ganjil" required />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">KATEGORI</label>
                        <x-select icon="tag" name="type" required>
                            <option value="Kegiatan" {{ old('type') == 'Kegiatan' ? 'selected' : '' }}>Kegiatan / Acara Sekolah</option>
                            <option value="Ujian" {{ old('type') == 'Ujian' ? 'selected' : '' }}>Ujian Akademik</option>
                            <option value="Libur" {{ old('type') == 'Libur' ? 'selected' : '' }}>Hari Libur</option>
                            <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </x-select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">DESKRIPSI (OPSIONAL)</label>
                        <textarea name="description" class="w-full bg-slate-50 dark:bg-black/10 border border-slate-200 dark:border-white/10 rounded-2xl p-4 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-yumik-primary/50 focus:border-yumik-primary outline-none transition-all resize-none h-24" placeholder="Tuliskan detail kegiatan jika diperlukan...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-4 flex flex-col gap-6">
            <x-card class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-white/5 dark:to-transparent">
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-4">Pengaturan Waktu</h3>
                
                <div class="flex flex-col gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TAHUN AJARAN TERKAIT</label>
                        <x-select icon="calendar" name="academic_year_id" required>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TANGGAL MULAI</label>
                        <x-input type="date" icon="calendar-plus" name="start_date" value="{{ old('start_date') }}" required />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TANGGAL SELESAI</label>
                        <x-input type="date" icon="calendar-check" name="end_date" value="{{ old('end_date') }}" required />
                    </div>
                </div>
            </x-card>

            <div class="flex flex-col gap-3 mt-auto">
                <x-button variant="primary" type="submit" class="w-full py-4 shadow-lg">
                    <i class="ph-bold ph-floppy-disk text-lg"></i> Simpan Agenda
                </x-button>
                <a href="{{ route('admin.kalender.index') }}" class="w-full">
                    <x-button variant="secondary" type="button" class="w-full py-4 border-none bg-transparent shadow-none text-slate-500">Batal</x-button>
                </a>
            </div>
        </div>
    </form>
@endsection