@extends('layouts.app')

@section('title', 'Detail Pertemuan - ' . $meeting->topic)

@section('content')
<div class="space-y-6">
    <!-- BREADCRUMB -->
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.dashboard') }}">Dashboard</x-breadcrumb-item>
        <x-breadcrumb-item href="{{ route('guru.schedule.meeting.index', $schedule->id) }}">{{ $schedule->subject->name }}</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Pertemuan {{ $meeting->meeting_number }}</x-breadcrumb-item>
    </x-breadcrumbs>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: Informasi Jurnal (Porsi 1/3) -->
        <div class="space-y-6">
            <x-card class="p-6">
                <x-badge color="primary" class="mb-4">Pertemuan {{ str_pad($meeting->meeting_number, 2, '0', STR_PAD_LEFT) }}</x-badge>
                
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2 leading-tight">
                    {{ $meeting->topic }}
                </h1>
                
                <div class="flex items-center gap-2 text-sm text-slate-500 font-medium mb-6 pb-6 border-b border-slate-100 dark:border-white/10">
                    <i class="ph-fill ph-calendar text-slate-400"></i>
                    {{ \Carbon\Carbon::parse($meeting->date)->translatedFormat('l, d F Y') }}
                </div>

                <div class="space-y-4">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Kelas</h4>
                        <p class="font-medium text-slate-800 dark:text-white">{{ $schedule->classroom->name }} ({{ $schedule->classroom->major }})</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Mata Pelajaran</h4>
                        <p class="font-medium text-slate-800 dark:text-white">{{ $schedule->subject->name }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Jurnal / Catatan</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-black/20 p-4 rounded-xl border border-slate-100 dark:border-white/5">
                            {{ $meeting->description ?? 'Tidak ada catatan jurnal untuk pertemuan ini.' }}
                        </p>
                    </div>
                </div>

                <div class="mt-8">
                    <x-button variant="secondary" class="w-full">
                        <i class="ph-bold ph-pencil"></i> Edit Jurnal
                    </x-button>
                </div>
            </x-card>
        </div>

        <!-- KOLOM KANAN: Daftar Presensi Siswa (Porsi 2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <x-card class="p-0 overflow-hidden flex flex-col h-full">
                <div class="p-6 lg:p-8 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-black/20 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="ph-fill ph-users-three text-yumik-primary"></i> Presensi Kehadiran
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">Tandai kehadiran siswa untuk sesi ini.</p>
                    </div>
                    
                    <!-- Indikator Total -->
                    <div class="flex gap-2">
                        <div class="px-3 py-1.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 rounded-lg text-xs font-bold">
                            {{ $meeting->attendances->where('status', 'Hadir')->count() }} Hadir
                        </div>
                        <div class="px-3 py-1.5 bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 rounded-lg text-xs font-bold">
                            {{ $meeting->attendances->where('status', '!=', 'Hadir')->count() }} Absen
                        </div>
                    </div>
                </div>

                <!-- Form Bulk Update Presensi -->
                <form action="{{ route('guru.schedule.meeting.attendances.update', [$schedule->id, $meeting->id]) }}" method="POST" class="flex-1 flex flex-col">
                    @csrf
                    
                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[600px]">
                            <thead>
                                <tr class="bg-white dark:bg-transparent border-b border-slate-100 dark:border-white/5 text-xs font-bold tracking-widest text-slate-400 uppercase">
                                    <th class="p-4 pl-8 w-16">No</th>
                                    <th class="p-4">Nama Siswa</th>
                                    <th class="p-4 w-48 text-center">Status</th>
                                    <th class="p-4 pr-8">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                                @foreach($meeting->attendances as $index => $attendance)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
                                        <td class="p-4 pl-8 text-sm text-slate-400 font-medium">{{ $index + 1 }}</td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <x-avatar src="{{ $attendance->siswa->photo ?? '' }}" size="sm" />
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $attendance->siswa->user->name }}</span>
                                                    <span class="text-[10px] font-medium text-slate-400">{{ $attendance->siswa->nisn }}</span>
                                                </div>
                                            </div>
                                            <!-- Hidden input ID untuk bulk update -->
                                            <input type="hidden" name="attendances[{{ $index }}][id]" value="{{ $attendance->id }}">
                                        </td>
                                        <td class="p-4">
                                            <!-- Select Status Kustom -->
                                            <div class="relative w-full">
                                                <select name="attendances[{{ $index }}][status]" class="appearance-none w-full py-2 pl-4 pr-8 rounded-xl bg-slate-100 dark:bg-black/40 border-none text-sm font-semibold {{ $attendance->status == 'Hadir' ? 'text-emerald-600 dark:text-emerald-400' : ($attendance->status == 'Sakit' ? 'text-blue-500' : ($attendance->status == 'Izin' ? 'text-amber-500' : 'text-red-500')) }} focus:ring-2 focus:ring-yumik-primary/50 transition-all cursor-pointer">
                                                    <option value="Hadir" {{ $attendance->status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                                    <option value="Sakit" {{ $attendance->status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                                    <option value="Izin" {{ $attendance->status == 'Izin' ? 'selected' : '' }}>Izin</option>
                                                    <option value="Alpa" {{ $attendance->status == 'Alpa' ? 'selected' : '' }}>Alpa</option>
                                                </select>
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                                    <i class="ph-bold ph-caret-down text-xs"></i>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 pr-8">
                                            <input type="text" name="attendances[{{ $index }}][notes]" value="{{ $attendance->notes }}" placeholder="Catatan (Opsional)" class="w-full py-2 px-4 rounded-xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm focus:outline-none focus:ring-2 focus:ring-yumik-primary/30 transition-all text-slate-700 dark:text-slate-300">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 lg:p-8 bg-slate-50/80 dark:bg-black/40 border-t border-slate-100 dark:border-white/5 flex justify-end">
                        <x-button type="submit" variant="primary" class="shadow-lg shadow-yumik-primary/30">
                            <i class="ph-bold ph-floppy-disk"></i> Simpan Perubahan Presensi
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection