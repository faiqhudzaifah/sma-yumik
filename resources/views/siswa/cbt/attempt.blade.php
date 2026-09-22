@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row gap-6">
    <!-- Kolom Utama: Soal -->
    <div class="flex-grow space-y-6">
        <x-card>
            <div class="flex justify-between items-center pb-4 border-b">
                <h2 class="text-xl font-bold">{{ $cbt->judul }}</h2>
                <x-timer :end-time="$attempt->created_at->addMinutes($cbt->durasi)" />
            </div>

            <!-- Tambahkan ID dan atribut data-store-url di sini -->
            <div class="py-6" id="question-container" data-store-url="{{ route('siswa.cbt.store_answer', [$cbt->id, $attempt->id]) }}">
                @foreach($questions as $index => $question)
                    <div class="question-block hidden" id="question-{{ $question->id }}">
                        <div class="text-lg font-medium text-gray-800 mb-4">
                            <span class="font-bold mr-2">{{ $index + 1 }}.</span>
                            {!! nl2br(e($question->teks_soal)) !!}
                        </div>

                        <div class="space-y-3 mt-6">
                            @foreach($question->options as $option)
                                <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                    <input type="radio" 
                                           name="answer_{{ $question->id }}" 
                                           value="{{ $option->id }}" 
                                           class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500 answer-radio" 
                                           data-question-id="{{ $question->id }}">
                                    <span class="ml-3 text-gray-700">{{ $option->teks_opsi }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-between pt-6 border-t">
                <x-button type="button" color="secondary" id="btn-prev" disabled>Sebelumnya</x-button>
                <x-button type="button" color="primary" id="btn-next">Selanjutnya</x-button>
            </div>
        </x-card>
    </div>

    <!-- Sidebar Navigasi -->
    <div class="w-full md:w-80 space-y-6 shrink-0">
        <x-card title="Navigasi Soal">
            <div class="grid grid-cols-5 gap-2 mt-4" id="nav-grid">
                @foreach($questions as $index => $question)
                    <!-- Tombol Navigasi -->
                    <button type="button" 
                            class="w-10 h-10 rounded text-sm font-semibold border flex items-center justify-center transition-colors nav-btn bg-white text-gray-700 hover:bg-gray-100"
                            id="nav-btn-{{ $question->id }}">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>

            <div class="mt-8 border-t pt-6">
                <form action="{{ route('siswa.cbt.submit', ['cbt' => $cbt->id, 'attempt' => $attempt->id]) }}" method="POST">
                    @csrf
                    <x-button type="submit" color="red" class="w-full justify-center" onclick="return confirm('Apakah Anda yakin ingin mengumpulkan ujian ini? Jawaban tidak dapat diubah lagi.')">
                        Selesai & Kumpulkan
                    </x-button>
                </form>
            </div>
        </x-card>
    </div>
</div>

<!-- Muat file JS khusus CBT Engine -->
@vite(['resources/js/cbt-engine.js'])
@endsection