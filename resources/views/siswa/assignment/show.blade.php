@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Instruksi Tugas -->
    <div class="lg:col-span-2 space-y-6">
        <x-card>
            <div class="border-b pb-4 mb-5">
                <h1 class="text-2xl font-bold text-gray-800">{{ $assignment->judul ?? 'Judul Tugas' }}</h1>
                <p class="text-sm text-gray-500 mt-1">Batas Waktu: {{ \Carbon\Carbon::parse($assignment->deadline)->format('d F Y, H:i') }}</p>
            </div>
            
            <div class="prose max-w-none text-gray-700">
                {!! nl2br(e($assignment->instruksi ?? 'Tidak ada instruksi khusus.')) !!}
            </div>

            @if(!empty($assignment->lampiran))
                <div class="mt-6 p-4 bg-gray-50 border rounded-lg">
                    <p class="font-semibold text-gray-700 text-sm mb-2">File Lampiran:</p>
                    <a href="{{ asset('storage/' . $assignment->lampiran) }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download Lampiran
                    </a>
                </div>
            @endif
        </x-card>
    </div>

    <!-- Panel Pengumpulan -->
    <div class="space-y-6">
        <x-card title="Pengumpulan Tugas">
            <!-- Menampilkan Nilai (Jika sudah dinilai) -->
            @if(isset($submission) && $submission->nilai !== null)
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-center">
                    <p class="text-sm text-green-800 mb-1">Nilai Anda</p>
                    <p class="text-3xl font-bold text-green-600">{{ $submission->nilai }}</p>
                </div>
            @endif

            <!-- Form Upload (Jika belum dikumpulkan atau diperbolehkan resubmit) -->
            <!-- Sesuaikan pengecekan variabel $submission dengan controller Anda -->
            @if(!isset($submission) || (isset($submission) && empty($submission->file_jawaban)))
                <form action="{{ route('siswa.assignment.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <x-file-upload 
                        name="file_jawaban" 
                        label="Unggah File Jawaban" 
                        help="Format: PDF, DOCX, JPG (Maks 10MB)" 
                        required 
                    />
                    
                    <x-input 
                        type="textarea" 
                        name="catatan" 
                        label="Catatan Tambahan (Opsional)" 
                        rows="3" 
                        placeholder="Ketik catatan untuk guru di sini..." 
                    />

                    <!-- Validasi Terlambat Tampilan Saja -->
                    @if(now()->greaterThan($assignment->deadline))
                        <div class="p-3 bg-red-50 text-red-700 text-xs rounded border border-red-200">
                            <strong>Perhatian:</strong> Batas waktu telah lewat. Tugas akan ditandai terlambat.
                        </div>
                    @endif

                    <x-button type="submit" color="primary" class="w-full justify-center mt-2">
                        Kirim Tugas
                    </x-button>
                </form>
            @else
                <!-- Tampilan jika sudah mengumpulkan -->
                <div class="text-center py-4">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 text-green-500 rounded-full mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Tugas Terkumpul</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Dikirim pada: {{ \Carbon\Carbon::parse($submission->created_at)->format('d M Y, H:i') }}
                    </p>
                    
                    @if(!empty($submission->file_jawaban))
                        <a href="{{ asset('storage/' . $submission->file_jawaban) }}" target="_blank" class="mt-4 inline-block text-sm text-blue-600 hover:underline">
                            Lihat File Terkirim
                        </a>
                    @endif
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection