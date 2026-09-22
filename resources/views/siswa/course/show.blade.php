@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('siswa.course.index') }}">Mata Pelajaran</x-breadcrumb-item>
        <x-breadcrumb-item active>{{ $course->subject->nama ?? 'Detail Mapel' }}</x-breadcrumb-item>
    </x-breadcrumbs>

    <div class="bg-indigo-600 rounded-xl p-8 text-white shadow-md">
        <h1 class="text-3xl font-bold mb-2">{{ $course->subject->nama ?? 'Nama Mata Pelajaran' }}</h1>
        <p class="text-indigo-100 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Guru Pengampu: {{ $course->guru->nama ?? '-' }}
        </p>
    </div>

    <x-card>
        <x-tabs>
            <!-- Tab Materi -->
            <x-tab-item name="Materi" active>
                <div class="space-y-3 mt-4">
                    @forelse($materi as $m)
                        <div class="flex items-center p-4 border rounded-lg hover:shadow-sm transition">
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </div>
                            <div class="flex-grow">
                                <a href="{{ route('siswa.material.show', $m->id) }}" class="text-lg font-semibold text-gray-800 hover:text-blue-600">
                                    {{ $m->judul }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">Dibagikan: {{ \Carbon\Carbon::parse($m->created_at)->format('d M Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <x-empty-state message="Belum ada materi untuk mata pelajaran ini." />
                    @endforelse
                </div>
            </x-tab-item>

            <!-- Tab Tugas -->
            <x-tab-item name="Tugas">
                <div class="mt-4">
                    <x-table>
                        <thead>
                            <tr>
                                <th>Judul Tugas</th>
                                <th>Batas Pengumpulan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tugas as $t)
                                <tr>
                                    <td class="font-medium text-gray-900">{{ $t->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($t->deadline)->format('d M Y H:i') }}</td>
                                    <td>
                                        <!-- Sesuaikan variabel pengecekan status tugas dengan backend Anda -->
                                        @if(isset($t->is_submitted) && $t->is_submitted)
                                            <x-badge color="green">Selesai</x-badge>
                                        @else
                                            <x-badge color="gray">Belum Dikerjakan</x-badge>
                                        @endif
                                    </td>
                                    <td>
                                        <x-action-button href="{{ route('siswa.assignment.show', $t->id) }}">
                                            Buka
                                        </x-action-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada tugas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </x-table>
                </div>
            </x-tab-item>
        </x-tabs>
    </x-card>
</div>
@endsection