@extends('layouts.app')

@section('title', 'Jadwal Pelajaran - LMS')

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
            <x-breadcrumb-item active="true">Jadwal Pelajaran</x-breadcrumb-item>
        </x-breadcrumbs>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mt-2">
            <div>
                <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white">
                    Matriks <span class="font-bold">Penjadwalan.</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Plotting KBM, pengampu mapel, dan penggunaan ruangan.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.jadwal.create') }}">
                    <x-button variant="primary">
                        <i class="ph-bold ph-plus text-lg"></i> Susun Jadwal
                    </x-button>
                </a>
            </div>
        </div>
    </div>

    <!-- Toolbar Filter Kompleks -->
    <div class="mb-6 p-4 rounded-2xl bg-white/50 dark:bg-[#1A1D24]/50 border border-slate-100 dark:border-white/5 backdrop-blur-sm">
        <form action="{{ route('admin.jadwal.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <x-select name="semester_id" onchange="this.form.submit()" icon="calendar">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester_id', $semesters->firstWhere('is_active', true)->id ?? '') == $sem->id ? 'selected' : '' }}>
                            {{ $sem->academicYear->name }} - Semester {{ $sem->type }} {{ $sem->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div class="flex-1">
                <x-select name="classroom_id" onchange="this.form.submit()" icon="door">
                    <option value="">Semua Rombongan Belajar</option>
                    @foreach($classrooms as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('classroom_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->name }} (Tingkat {{ $kelas->grade_level }})
                        </option>
                    @endforeach
                </x-select>
            </div>
        </form>
    </div>

    <div class="transition-all duration-300 ease-in-out">
        <x-table>
            <x-slot name="head">
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest w-16">No</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Waktu & Tempat</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Kelas</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Mata Pelajaran & Guru</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
            </x-slot>

            @forelse($schedules as $index => $jadwal)
                <tr class="hover:bg-white/40 dark:hover:bg-white/5 transition-colors group border-b border-slate-100 dark:border-white/5">
                    <td class="p-5 text-slate-500 font-medium">{{ $index + 1 }}</td>
                    
                    <td class="p-5">
                        <div class="flex flex-col gap-1">
                            <span class="inline-flex w-fit items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold tracking-widest uppercase bg-yumik-primary/10 text-yumik-primary">
                                {{ $jadwal->day_of_week }}
                            </span>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                Jam ke-{{ $jadwal->jam_ke_mulai }} s.d {{ $jadwal->jam_ke_selesai }}
                            </span>
                            <span class="text-xs text-slate-500 flex items-center gap-1">
                                <i class="ph-fill ph-map-pin text-slate-400"></i>
                                {{ $jadwal->ruangan ?? 'Ruang Utama Kelas' }}
                            </span>
                        </div>
                    </td>
                    
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center shrink-0">
                                <i class="ph-fill ph-users-three text-xl text-slate-500"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white">{{ $jadwal->classroom->name }}</h4>
                                <span class="text-xs text-slate-500">{{ $jadwal->classroom->major ?? 'Umum' }}</span>
                            </div>
                        </div>
                    </td>
                    
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            <x-avatar size="sm" />
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white">{{ $jadwal->subject->name }}</h4>
                                <span class="text-xs text-slate-500">Oleh: {{ $jadwal->guru->user->name }}</span>
                            </div>
                        </div>
                    </td>
                    
                    <td class="p-5 text-right flex justify-end gap-2">
                        <x-action-button icon="pencil-simple" color="amber" tooltip="Ubah Jadwal" href="{{ route('admin.jadwal.edit', $jadwal->id) }}" />
                        
                        <!-- Pemicu Modal Hapus -->
                        <button type="button" onclick="document.getElementById('modal-delete-{{ $jadwal->id }}').setAttribute('data-state', 'open'); document.getElementById('modal-delete-{{ $jadwal->id }}').classList.remove('hidden')" class="w-9 h-9 rounded-full bg-red-50 text-red-500 dark:bg-red-500/10 hover:bg-red-500 hover:text-white flex items-center justify-center transition-colors shadow-sm relative group">
                            <i class="ph-bold ph-trash"></i>
                        </button>

                        <!-- Komponen Modal Hapus Spesifik -->
                        <x-modal id="modal-delete-{{ $jadwal->id }}" title="Hapus Jadwal?">
                            Anda yakin ingin menghapus jadwal <strong>{{ $jadwal->subject->name }}</strong> untuk kelas <strong>{{ $jadwal->classroom->name }}</strong> pada hari <strong>{{ $jadwal->day_of_week }}</strong>?
                            
                            <form action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST" class="mt-6 flex justify-end">
                                @csrf
                                @method('DELETE')
                                <x-button variant="primary" type="submit" class="w-full bg-red-500 hover:bg-red-600 shadow-red-500/30">Ya, Hapus Jadwal</x-button>
                            </form>
                        </x-modal>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="ph-fill ph-calendar-blank text-5xl mb-3 opacity-50"></i>
                            <p>Belum ada data jadwal untuk filter yang dipilih.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>
@endsection