@extends('layouts.app')

@section('title', 'Detail Ujian - ' . $assessment->title)

@section('content')
<div class="space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.schedule.meeting.index', $schedule->id) }}">{{ $schedule->subject->name }}</x-breadcrumb-item>
        <x-breadcrumb-item href="{{ route('guru.schedule.assessment.index', $schedule->id) }}">CBT & Ujian</x-breadcrumb-item>
        <x-breadcrumb-item active="true">{{ $assessment->title }}</x-breadcrumb-item>
    </x-breadcrumbs>

    <!-- STATUS BANNER -->
    <x-card class="!p-0 overflow-hidden border-2 {{ $assessment->status == 'PUBLISHED' ? 'border-yumik-primary/50' : 'border-amber-500/50' }}">
        <div class="bg-slate-50 dark:bg-black/20 p-6 lg:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <x-badge color="{{ $assessment->status == 'PUBLISHED' ? 'primary' : 'neutral' }}" class="{{ $assessment->status == 'DRAFT' ? '!bg-amber-100 !text-amber-700' : '' }}">
                        STATUS: {{ $assessment->status }}
                    </x-badge>
                    <x-badge color="neutral">{{ $assessment->type }}</x-badge>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">{{ $assessment->title }}</h1>
                <p class="text-sm text-slate-500 font-medium mt-2">
                    <i class="ph-fill ph-timer"></i> Durasi: {{ $assessment->duration }} Menit • Batas Percobaan: {{ $assessment->attempt_limit }}x
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('guru.schedule.assessment.edit', [$schedule->id, $assessment->id]) }}">
                    <x-button variant="secondary" class="!px-4"><i class="ph-bold ph-gear"></i> Pengaturan</x-button>
                </a>
                @if($assessment->status == 'DRAFT')
                    <x-button variant="primary" class="!px-4"><i class="ph-bold ph-rocket-launch"></i> Publikasikan</x-button>
                @else
                    <a href="{{ route('guru.schedule.assessment.grading.index', [$schedule->id, $assessment->id]) }}">
                        <x-button variant="primary" class="!px-4 bg-amber-500 hover:bg-amber-600 !border-amber-500 shadow-amber-500/30 text-white"><i class="ph-bold ph-check-square-offset"></i> Koreksi Ujian</x-button>
                    </a>
                @endif
            </div>
        </div>
    </x-card>

    <!-- BANK SOAL (QUESTIONS) -->
    <div class="space-y-4">
        <div class="flex justify-between items-end px-2">
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Bank Soal</h2>
                <p class="text-sm text-slate-500">Total Soal: {{ $assessment->questions->count() }} • Total Poin: {{ $assessment->questions->sum('points') }}</p>
            </div>
            
            <div class="flex gap-2">
                <x-button variant="secondary" class="!px-4 !py-2 !text-sm shadow-sm">
                    <i class="ph-bold ph-file-csv"></i> Import Excel
                </x-button>
                @if($assessment->status == 'DRAFT')
                    <x-button variant="primary" class="!px-4 !py-2 !text-sm shadow-md" onclick="document.getElementById('modal-tambah-soal').setAttribute('data-state', 'open'); document.getElementById('modal-tambah-soal').classList.remove('hidden')">
                        <i class="ph-bold ph-plus"></i> Tambah Soal
                    </x-button>
                @endif
            </div>
        </div>

        @if($assessment->questions->isEmpty())
            <x-card class="p-12 text-center">
                <x-empty-state icon="files" title="Kuis Masih Kosong" description="Klik tombol tambah soal di atas untuk mulai membuat pertanyaan secara manual atau import dari Excel." />
            </x-card>
        @else
            @foreach($assessment->questions as $index => $question)
                <x-card class="p-6 relative group overflow-hidden border-l-4 border-l-yumik-primary">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="w-8 h-8 rounded-lg bg-yumik-primary/10 text-yumik-primary font-black flex items-center justify-center">{{ $index + 1 }}</span>
                                <x-badge color="neutral" class="!text-[10px]">{{ $question->type }}</x-badge>
                                <span class="text-xs font-bold text-slate-400">{{ number_format($question->points, 0) }} Poin</span>
                            </div>
                            
                            <!-- LOGIKA PENAMPILAN GAMBAR SOAL -->
                            @if($question->image_path)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $question->image_path) }}" alt="Gambar Pendukung Soal" class="rounded-xl border border-slate-200 dark:border-white/10 max-h-64 object-contain bg-slate-50 dark:bg-black/20 p-2 shadow-sm">
                                </div>
                            @endif

                            <div class="text-slate-800 dark:text-white font-medium text-lg mb-5">
                                {!! nl2br(e($question->question_text)) !!}
                            </div>

                            @if($question->type == 'MULTIPLE_CHOICE')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                                    @foreach($question->options as $optIndex => $option)
                                        <div class="p-3 rounded-xl border-2 {{ $option->is_correct ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-500/10' : 'border-slate-100 dark:border-white/5 bg-slate-50 dark:bg-black/20' }} flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-full {{ $option->is_correct ? 'bg-emerald-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }} flex items-center justify-center text-xs font-bold">{{ chr(65 + $optIndex) }}</span>
                                            <span class="text-sm font-medium {{ $option->is_correct ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300' }}">{{ $option->option_text }}</span>
                                            
                                            @if($option->is_correct)
                                                <i class="ph-fill ph-check-circle text-emerald-500 ml-auto text-lg"></i>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if($assessment->status == 'DRAFT')
                            <form action="{{ route('guru.schedule.assessment.question.destroy', [$schedule->id, $assessment->id, $question->id]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-[1rem] bg-red-50 dark:bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors" onclick="return confirm('Hapus soal ini?')">
                                    <i class="ph-bold ph-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </x-card>
            @endforeach
        @endif
    </div>
</div>

<!-- MODAL TAMBAH SOAL FULL UI -->
<x-modal id="modal-tambah-soal" title="Buat Pertanyaan">
    <form action="{{ route('guru.schedule.assessment.question.store', [$schedule->id, $assessment->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Tipe Soal</label>
                <x-select name="type" icon="tag" class="!py-3 !text-sm">
                    <option value="MULTIPLE_CHOICE">Pilihan Ganda</option>
                    <option value="ESSAY">Esai (Uraian)</option>
                </x-select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Bobot Nilai (Poin)</label>
                <x-input type="number" name="points" value="10" min="1" icon="star" class="!py-3 !text-sm" required />
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Teks Pertanyaan <span class="text-red-500">*</span></label>
            <textarea name="question_text" rows="4" class="w-full p-4 rounded-xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all text-slate-800 dark:text-white" placeholder="Tuliskan pertanyaan di sini..." required></textarea>
        </div>

        <!-- INPUT UNGGAH GAMBAR (OPSIONAL) -->
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Gambar Pendukung (Opsional)</label>
            <div class="relative group">
                <input type="file" name="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                <div class="w-full px-4 py-3 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-black/20 flex items-center gap-3 text-sm text-slate-500 group-hover:border-yumik-primary/50 group-hover:bg-yumik-primary/5 transition-all">
                    <i class="ph-bold ph-image text-lg"></i>
                    <span>Klik atau tarik file gambar ke sini (JPG/PNG, Maks 2MB)</span>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-white/10">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-3">Opsi Jawaban (Khusus Pilihan Ganda)</label>
            <div class="space-y-3">
                <!-- Looping opsi A sampai E (Sederhana menggunakan HTML biasa) -->
                @php $letters = ['A', 'B', 'C', 'D', 'E']; @endphp
                @foreach($letters as $index => $letter)
                    <div class="flex items-center gap-3">
                        <label class="cursor-pointer" title="Jadikan Kunci Jawaban">
                            <input type="radio" name="is_correct" value="{{ $index }}" class="peer sr-only" {{ $index === 0 ? 'checked' : '' }}>
                            <div class="w-8 h-8 rounded-full border-2 border-slate-300 dark:border-slate-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-500 text-slate-500 peer-checked:text-white flex items-center justify-center text-xs font-bold transition-all">
                                {{ $letter }}
                            </div>
                        </label>
                        <input type="text" name="options[]" placeholder="Teks opsi jawaban {{ $letter }}..." class="flex-1 py-2 px-4 rounded-xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all text-slate-800 dark:text-white">
                    </div>
                @endforeach
            </div>
            <p class="text-[10px] text-slate-400 mt-2">* Kosongkan opsi jawaban jika Anda membuat soal tipe Esai. Centang huruf bulat untuk menentukan Kunci Jawaban.</p>
        </div>

        <x-button type="submit" variant="primary" class="w-full mt-4 !py-3 shadow-md shadow-yumik-primary/30">Simpan Soal ke Bank</x-button>
    </form>
</x-modal>
@endsection