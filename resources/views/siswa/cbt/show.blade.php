@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('siswa.cbt.index') }}">Ujian & Kuis</x-breadcrumb-item>
        <x-breadcrumb-item active>{{ $cbt->judul }}</x-breadcrumb-item>
    </x-breadcrumbs>

    <x-card>
        <div class="text-center pb-6 border-b">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $cbt->judul }}</h1>
            <p class="text-gray-600">Mata Pelajaran: {{ $cbt->schedule->subject->nama }}</p>
        </div>

        <div class="py-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-800">Informasi Pelaksanaan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg border">
                    <p class="text-sm text-gray-500">Waktu Mulai</p>
                    <p class="font-bold text-gray-800">{{ $cbt->waktu_mulai->format('d M Y, H:i') }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg border">
                    <p class="text-sm text-gray-500">Waktu Selesai</p>
                    <p class="font-bold text-gray-800">{{ $cbt->waktu_selesai->format('d M Y, H:i') }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg border">
                    <p class="text-sm text-gray-500">Durasi Pengerjaan</p>
                    <p class="font-bold text-gray-800">{{ $cbt->durasi }} Menit</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg border">
                    <p class="text-sm text-gray-500">Jumlah Soal</p>
                    <p class="font-bold text-gray-800">{{ $cbt->questions->count() }} Soal</p>
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800">
                <h4 class="font-bold">Peraturan:</h4>
                <ul class="list-disc ml-5 text-sm mt-2">
                    <li>Waktu akan terus berjalan sejak Anda menekan tombol "Mulai".</li>
                    <li>Pastikan koneksi internet stabil.</li>
                    <li>Jawaban akan tersimpan secara otomatis setiap kali Anda berpindah soal.</li>
                </ul>
            </div>
        </div>

        <div class="pt-6 border-t text-center">
            @if(now()->between($cbt->waktu_mulai, $cbt->waktu_selesai))
                <form action="{{ route('siswa.cbt.start', $cbt->id) }}" method="POST">
                    @csrf
                    <x-button type="submit" color="primary" size="lg" class="w-full md:w-auto px-12">
                        Mulai Kerjakan Sekarang
                    </x-button>
                </form>
            @elseif(now()->lessThan($cbt->waktu_mulai))
                <x-alert type="warning" message="Ujian belum dimulai." />
            @else
                <x-alert type="danger" message="Waktu pelaksanaan ujian telah berakhir." />
            @endif
        </div>
    </x-card>
</div>
@endsection