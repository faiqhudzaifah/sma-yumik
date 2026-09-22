@extends('layouts.app')

@section('title', 'Tahun Ajaran & Semester - LMS')

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
            <x-breadcrumb-item active="true">Tahun Ajaran</x-breadcrumb-item>
        </x-breadcrumbs>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mt-2">
            <div>
                <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white">
                    Master Data <span class="font-bold">Periode.</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Kelola Tahun Ajaran dan Semester aktif sistem.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.tahun-ajaran.create') }}">
                    <x-button variant="primary">
                        <i class="ph-bold ph-plus text-lg"></i> Tambah Periode
                    </x-button>
                </a>
            </div>
        </div>
    </div>

    <div class="flex justify-end mb-6">
        <form action="{{ route('admin.tahun-ajaran.index') }}" method="GET" class="w-full lg:w-96">
            <x-search-bar name="search" value="{{ request('search') }}" placeholder="Cari tahun ajaran (contoh: 2026)..." />
        </form>
    </div>

    <div class="transition-all duration-300 ease-in-out">
        <x-table>
            <x-slot name="head">
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest w-16">No</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Tahun Ajaran & Semester</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Durasi Pelaksanaan</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
            </x-slot>

            @forelse($semesters as $index => $semesterData)
                <tr class="hover:bg-white/40 dark:hover:bg-white/5 transition-colors group border-b border-slate-100 dark:border-white/5">
                    <td class="p-5 text-slate-500 font-medium">{{ $semesters->firstItem() + $index }}</td>
                    <td class="p-5">
                        <h4 class="font-bold text-slate-800 dark:text-white text-base">{{ $semesterData->academicYear->name }}</h4>
                        <span class="text-sm font-medium text-slate-500">Semester {{ $semesterData->type }}</span>
                    </td>
                    <td class="p-5">
                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                            <i class="ph-fill ph-calendar-blank text-slate-400"></i>
                            @if($semesterData->start_date && $semesterData->end_date)
                                {{ \Carbon\Carbon::parse($semesterData->start_date)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($semesterData->end_date)->format('d M Y') }}
                            @else
                                <span class="italic opacity-50">Belum ditentukan</span>
                            @endif
                        </div>
                    </td>
                    <td class="p-5 text-center">
                        @if($semesterData->is_active)
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                Arsip
                            </span>
                        @endif
                    </td>
                    <td class="p-5 text-right flex justify-end gap-2">
                        <x-action-button icon="download-simple" color="emerald" tooltip="Unduh Arsip Akademik" href="{{ route('admin.tahun-ajaran.export', $semesterData->id) }}" />
                        <x-action-button icon="pencil-simple" color="amber" tooltip="Edit Periode" href="{{ route('admin.tahun-ajaran.edit', $semesterData->id) }}" />
                        
                        <form action="{{ route('admin.tahun-ajaran.destroy', $semesterData->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini? Data yang terkait mungkin akan hilang.');">
                            @csrf
                            @method('DELETE')
                            <x-action-button type="submit" icon="trash" color="{{ $semesterData->is_active ? 'slate' : 'red' }}" tooltip="{{ $semesterData->is_active ? 'Periode aktif tidak bisa dihapus' : 'Hapus Periode' }}" :disabled="$semesterData->is_active" />
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-10 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="ph-fill ph-calendar-x text-5xl mb-3 opacity-50"></i>
                            <p>Belum ada data periode akademik.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-6">
            <x-pagination :paginator="$semesters" />
        </div>
    </div>
@endsection