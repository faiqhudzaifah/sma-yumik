@extends('layouts.app')

@section('title', 'Buat Ujian Baru - ' . $schedule->subject->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <x-breadcrumbs>
        <x-breadcrumb-item href="{{ route('guru.schedule.assessment.index', $schedule->id) }}">CBT & Ujian</x-breadcrumb-item>
        <x-breadcrumb-item active="true">Buat Ujian Baru</x-breadcrumb-item>
    </x-breadcrumbs>

    <x-card class="p-8 border-t-4 border-t-yumik-primary shadow-xl">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 rounded-[1rem] bg-yumik-primary/10 text-yumik-primary flex items-center justify-center shrink-0">
                <i class="ph-fill ph-exam text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Pengaturan Ujian / Kuis</h2>
                <p class="text-sm text-slate-500">Konfigurasi jadwal dan aturan CBT untuk kelas {{ $schedule->classroom->name }}</p>
            </div>
        </div>

        <form action="{{ route('guru.schedule.assessment.store', $schedule->id) }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- BAGIAN 1: IDENTITAS UJIAN -->
            <div class="space-y-6">
                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 dark:border-white/10 pb-2">1. Identitas Ujian</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Evaluasi <span class="text-red-500">*</span></label>
                        <x-input name="title" value="{{ old('title') }}" placeholder="Contoh: Ujian Tengah Semester (UTS) Ganjil" icon="text-t" required />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jenis Evaluasi <span class="text-red-500">*</span></label>
                        <x-select name="type" icon="tag">
                            <option value="QUIZ">Kuis (Kuis Harian/Mingguan)</option>
                            <option value="EXAM">Ujian (UTS/UAS)</option>
                        </x-select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Status Publikasi <span class="text-red-500">*</span></label>
                        <x-select name="status" icon="broadcast">
                            <option value="DRAFT">DRAFT (Disembunyikan dari siswa)</option>
                            <option value="PUBLISHED">PUBLISHED (Bisa diakses sesuai jadwal)</option>
                        </x-select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi / Instruksi Pengerjaan</label>
                    <textarea name="description" rows="3" class="w-full p-4 rounded-2xl bg-slate-100 dark:bg-black/20 border-none text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all" placeholder="Tuliskan petunjuk teknis ujian..."></textarea>
                </div>
            </div>

            <!-- BAGIAN 2: PENGATURAN WAKTU & AKSES -->
            <div class="space-y-6">
                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 dark:border-white/10 pb-2">2. Pengaturan Waktu & Akses</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jadwal Mulai</label>
                        <x-input type="datetime-local" name="start_at" value="{{ old('start_at') }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jadwal Berakhir (Tenggat)</label>
                        <x-input type="datetime-local" name="end_at" value="{{ old('end_at') }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Durasi Pengerjaan (Menit) <span class="text-red-500">*</span></label>
                        <x-input type="number" min="1" name="duration" value="{{ old('duration', 60) }}" icon="timer" required />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Batas Percobaan (Attempt) <span class="text-red-500">*</span></label>
                        <x-input type="number" min="1" name="attempt_limit" value="{{ old('attempt_limit', 1) }}" icon="arrows-clockwise" required />
                    </div>
                </div>

                <!-- Pengaturan Toggle -->
                <div class="flex flex-col sm:flex-row gap-6 p-5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10">
                    <div class="flex-1 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-slate-700 dark:text-slate-300">Acak Urutan Soal</p>
                            <p class="text-xs text-slate-500">Soal akan ditampilkan berbeda untuk setiap siswa</p>
                        </div>
                        <input type="hidden" name="shuffle_questions" value="0">
                        <x-toggle name="shuffle_questions" value="1" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-white/10">
                <a href="{{ route('guru.schedule.assessment.index', $schedule->id) }}">
                    <x-button variant="secondary" type="button">Batal</x-button>
                </a>
                <x-button variant="primary" type="submit" class="shadow-lg shadow-yumik-primary/30">
                    <i class="ph-bold ph-floppy-disk"></i> Simpan & Lanjut Buat Soal
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection