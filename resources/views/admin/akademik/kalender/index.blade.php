@extends('layouts.app')

@section('title', 'Kalender Akademik - LMS')

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
            <x-breadcrumb-item active="true">Kalender Akademik</x-breadcrumb-item>
        </x-breadcrumbs>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mt-2">
            <div>
                <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white">
                    Agenda <span class="font-bold">Akademik.</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Jadwal kegiatan, ujian, dan hari libur operasional sekolah.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.kalender.create') }}">
                    <x-button variant="primary">
                        <i class="ph-bold ph-plus text-lg"></i> Tambah Agenda
                    </x-button>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Tahun Ajaran -->
    <div class="flex justify-end mb-6">
        <form action="{{ route('admin.kalender.index') }}" method="GET" class="w-full lg:w-96">
            <x-select name="academic_year_id" onchange="this.form.submit()" icon="calendar">
                <option value="">-- Semua Tahun Ajaran --</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                        Tahun Ajaran {{ $year->name }}
                    </option>
                @endforeach
            </x-select>
        </form>
    </div>

    <div class="transition-all duration-300 ease-in-out">
        <x-table>
            <x-slot name="head">
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest w-16">No</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Informasi Kegiatan</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Tanggal Pelaksanaan</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Kategori</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
            </x-slot>

            @forelse($calendars as $index => $agenda)
                <tr class="hover:bg-white/40 dark:hover:bg-white/5 transition-colors border-b border-slate-100 dark:border-white/5">
                    <td class="p-5 text-slate-500 font-medium">{{ $calendars->firstItem() + $index }}</td>
                    
                    <td class="p-5">
                        <h4 class="font-bold text-slate-800 dark:text-white">{{ $agenda->event_name }}</h4>
                        <span class="text-xs text-slate-500 line-clamp-1 mt-1">{{ $agenda->description ?? 'Tidak ada deskripsi' }}</span>
                    </td>
                    
                    <td class="p-5">
                        <div class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300 font-mono">
                            <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-black/30 border border-slate-200 dark:border-white/10">
                                {{ \Carbon\Carbon::parse($agenda->start_date)->format('d M Y') }}
                            </span>
                            @if($agenda->start_date != $agenda->end_date)
                                <span class="text-slate-400">-</span>
                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-black/30 border border-slate-200 dark:border-white/10">
                                    {{ \Carbon\Carbon::parse($agenda->end_date)->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </td>
                    
                    <td class="p-5 text-center">
                        @php
                            $typeStyles = [
                                'Libur' => 'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400',
                                'Ujian' => 'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400',
                                'Kegiatan' => 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400',
                                'Lainnya' => 'bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300',
                            ];
                        @endphp
                        <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase {{ $typeStyles[$agenda->type] ?? $typeStyles['Lainnya'] }}">
                            {{ $agenda->type }}
                        </span>
                    </td>
                    
                    <td class="p-5 text-right flex justify-end gap-2">
                        <x-action-button icon="pencil-simple" color="amber" tooltip="Edit Agenda" href="{{ route('admin.kalender.edit', $agenda->id) }}" />
                        <form action="{{ route('admin.kalender.destroy', $agenda->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda ini?');">
                            @csrf
                            @method('DELETE')
                            <x-action-button type="submit" icon="trash" color="red" tooltip="Hapus Agenda" />
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="ph-fill ph-calendar-blank text-5xl mb-3 opacity-50"></i>
                            <p>Belum ada agenda pada tahun ajaran ini.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>
        <div class="mt-6">
            <x-pagination :paginator="$calendars" />
        </div>
    </div>
@endsection