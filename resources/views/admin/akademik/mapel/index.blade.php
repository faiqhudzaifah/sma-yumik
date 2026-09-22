@extends('layouts.app')

@section('title', 'Mata Pelajaran - LMS')

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
            <x-breadcrumb-item active="true">Mata Pelajaran</x-breadcrumb-item>
        </x-breadcrumbs>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mt-2">
            <div>
                <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white">
                    Direktori <span class="font-bold">Mata Pelajaran.</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Kelola kurikulum, kode mapel, dan guru pengampu.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.mapel.create') }}">
                    <x-button variant="primary">
                        <i class="ph-bold ph-plus text-lg"></i> Tambah Mapel
                    </x-button>
                </a>
            </div>
        </div>
    </div>

    <!-- Toolbar Filter -->
    <div class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-6 p-4 rounded-2xl bg-white/50 dark:bg-[#1A1D24]/50 border border-slate-100 dark:border-white/5 backdrop-blur-sm">
        <form action="{{ route('admin.mapel.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3">
            <div class="w-full sm:w-64">
                <x-select name="semester_id" onchange="this.form.submit()" icon="calendar">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester_id', $semesters->firstWhere('is_active', true)->id ?? '') == $sem->id ? 'selected' : '' }}>
                            {{ $sem->academicYear->name }} - Semester {{ $sem->type }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div class="w-full lg:w-96">
                <x-search-bar name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode mapel..." />
            </div>
        </form>
    </div>

    <div class="transition-all duration-300 ease-in-out">
        <x-table>
            <x-slot name="head">
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest w-16">No</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Informasi Mata Pelajaran</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Guru Pengampu</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
            </x-slot>

            @forelse($subjects as $index => $mapel)
                <tr class="hover:bg-white/40 dark:hover:bg-white/5 transition-colors group border-b border-slate-100 dark:border-white/5">
                    <td class="p-5 text-slate-500 font-medium">{{ $subjects->firstItem() + $index }}</td>
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-yumik-primary/10 text-yumik-primary flex items-center justify-center shrink-0">
                                <i class="ph-fill ph-book-open text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white">{{ $mapel->name }}</h4>
                                <span class="text-xs font-mono text-slate-500 bg-slate-100 dark:bg-white/5 px-2 py-0.5 rounded">
                                    Kode: {{ $mapel->code ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="p-5">
                        <div class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            @if($mapel->guru)
                                <x-avatar size="sm" />
                                {{ $mapel->guru->user->name }}
                            @else
                                <span class="text-slate-400 italic font-normal">Belum diplot</span>
                            @endif
                        </div>
                    </td>
                    <td class="p-5 text-right flex justify-end gap-2">
                        <x-action-button icon="pencil-simple" color="amber" tooltip="Edit Mapel" href="{{ route('admin.mapel.edit', $mapel->id) }}" />
                        <form action="{{ route('admin.mapel.destroy', $mapel->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?');">
                            @csrf
                            @method('DELETE')
                            <x-action-button type="submit" icon="trash" color="red" tooltip="Hapus Mapel" />
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-10 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="ph-fill ph-books text-5xl mb-3 opacity-50"></i>
                            <p>Belum ada data mata pelajaran pada semester ini.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-6">
            <x-pagination :paginator="$subjects" />
        </div>
    </div>
@endsection