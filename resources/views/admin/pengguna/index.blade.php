@extends('layouts.app')

@section('title', 'Kelola Pengguna - LMS')

@section('content')
    <!-- Notifikasi Session (Tanpa mb-6 karena sudah floating) -->
    @if(session('success'))
        <x-alert type="success" title="Berhasil!" message="{{ session('success') }}" />
    @endif
    @if(session('error'))
        <x-alert type="error" title="Gagal!" message="{{ session('error') }}" />
    @endif

    <!-- Header & Breadcrumbs -->
    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Kelola Pengguna</x-breadcrumb-item>
        </x-breadcrumbs>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mt-2">
            <div>
                <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white">
                    Direktori <span class="font-bold">Pengguna.</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Kelola data Admin, Guru, Siswa, dan Operator.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.pengguna.create') }}">
                    <x-button variant="primary">
                        <i class="ph-bold ph-plus text-lg"></i> Tambah Pengguna
                    </x-button>
                </a>
            </div>
        </div>
    </div>

    <!-- Toolbar Pencarian & Tabs Filter -->
    <div class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-6">
        <div class="w-full lg:w-auto overflow-x-auto pb-2">
            <x-tabs>
                <x-tab-item class="filter-tab" href="{{ route('admin.pengguna.index') }}" name="Semua" :active="!request('role')" />
                @foreach($roles as $role)
                    <x-tab-item 
                        class="filter-tab"
                        href="{{ route('admin.pengguna.index', ['role' => $role->name]) }}" 
                        name="{{ ucfirst($role->name) }}" 
                        :active="request('role') == $role->name" 
                    />
                @endforeach
            </x-tabs>
        </div>

        <form id="search-form" action="{{ route('admin.pengguna.index') }}" method="GET" class="w-full lg:w-96">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <x-search-bar name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIP/NISN..." />
        </form>
    </div>

    <!-- AREA DATA -->
    <div id="data-container" class="transition-all duration-300 ease-in-out">
        <x-table>
            <x-slot name="head">
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest w-16">No</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Profil Pengguna</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Username / NIP</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Peran</th>
                <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
            </x-slot>

            @forelse($users as $index => $user)
                <tr class="hover:bg-white/40 dark:hover:bg-white/5 transition-colors group border-b border-slate-100 dark:border-white/5">
                    <td class="p-5 text-slate-500 font-medium">{{ $users->firstItem() + $index }}</td>
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            <x-avatar size="md" status="online" />
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white">{{ $user->name }}</h4>
                                <span class="text-xs text-slate-500">{{ $user->email ?? 'Tidak ada email' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="p-5 text-sm font-semibold text-slate-600 dark:text-slate-300">
                        {{ $user->username }}
                    </td>
                    <td class="p-5">
                        @php
                            $roleColors = [
                                'admin' => 'bg-purple-100 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400',
                                'guru' => 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400',
                                'siswa' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400',
                                'operator' => 'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400',
                            ];
                            $color = $roleColors[$user->role->name] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase {{ $color }}">
                            {{ $user->role->name }}
                        </span>
                    </td>
                    
                    <td class="p-5 text-right flex justify-end gap-3">
                        <x-action-button icon="eye" color="blue" tooltip="Lihat Detail" href="{{ route('admin.pengguna.show', $user->id) }}" />
                        <x-action-button icon="pencil-simple" color="amber" tooltip="Edit Pengguna" href="{{ route('admin.pengguna.edit', $user->id) }}" />
                        
                        <form action="{{ route('admin.pengguna.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                            @csrf
                            @method('DELETE')
                            <x-action-button type="submit" icon="trash" color="red" tooltip="Hapus Data" />
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-10 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="ph-fill ph-users-three text-5xl mb-3 opacity-50"></i>
                            <p>Belum ada data pengguna ditemukan.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-6">
            <x-pagination :paginator="$users" />
        </div>
    </div>
@endsection