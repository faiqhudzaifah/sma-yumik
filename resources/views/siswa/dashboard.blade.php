@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->siswa->nama ?? 'Siswa' }}</h1>
            <p class="text-gray-600 mt-1">Pantau aktivitas akademik Anda hari ini.</p>
        </div>
        <x-badge color="primary">Siswa Aktif</x-badge>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Jadwal & Tugas -->
        <div class="md:col-span-2 space-y-6">
            <x-card title="Jadwal Pelajaran Hari Ini">
                @if(isset($jadwalHariIni) && count($jadwalHariIni) > 0)
                    <div class="space-y-3">
                        @foreach($jadwalHariIni as $jadwal)
                            <div class="flex justify-between items-center p-4 border rounded-lg hover:bg-gray-50 transition">
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $jadwal->subject->nama ?? 'Nama Mapel' }}</h3>
                                    <p class="text-sm text-gray-500">Guru: {{ $jadwal->guru->nama ?? '-' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                        {{ $jadwal->lessonHour->waktu_mulai ?? '' }} - {{ $jadwal->lessonHour->waktu_selesai ?? '' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                   <x-empty-state title="Tidak Ada Jadwal" description="Hore! Tidak ada jadwal kelas hari ini." />
                @endif
            </x-card>

            <x-card title="Tugas Belum Dikerjakan">
                @if(isset($tugasMendesak) && count($tugasMendesak) > 0)
                    <div class="space-y-3">
                        @foreach($tugasMendesak as $tugas)
                            <div class="flex justify-between items-center p-3 border-l-4 border-yellow-400 bg-yellow-50 rounded-r-lg">
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $tugas->judul }}</h4>
                                    <p class="text-xs text-gray-600">{{ $tugas->subject->nama ?? 'Mata Pelajaran' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold text-red-600">Batas: {{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y, H:i') }}</span>
                                    <br>
                                    <a href="{{ route('siswa.assignment.show', $tugas->id) }}" class="text-sm text-blue-600 hover:underline">Kerjakan &rarr;</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Semua tugas sudah dikerjakan. Hebat!</p>
                @endif
            </x-card>
        </div>

        <!-- Kolom Kanan: Kuis -->
        <div class="space-y-6">
            <x-card title="Ujian / Kuis Tersedia">
                @if(isset($kuisTersedia) && count($kuisTersedia) > 0)
                    <div class="space-y-3">
                        @foreach($kuisTersedia as $kuis)
                            <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
                                <h4 class="font-bold text-blue-800">{{ $kuis->judul }}</h4>
                                <p class="text-xs text-gray-600 mb-3 mt-1">Ditutup: {{ \Carbon\Carbon::parse($kuis->waktu_selesai)->format('d M Y, H:i') }}</p>
                                <x-button color="primary" size="sm" class="w-full justify-center" href="{{ route('siswa.cbt.show', $kuis->id) }}">
                                    Mulai Ujian
                                </x-button>
                            </div>
                        @endforeach
                    </div>
                @else
                   <x-empty-state title="Bebas Tugas" description="Belum ada tugas mendesak untuk saat ini." />
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection