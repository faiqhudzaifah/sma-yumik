@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gradient-to-r from-yumik-primary/10 to-transparent p-6 rounded-3xl border border-yumik-primary/20">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="#" class="hover:text-yumik-primary"><i class="ph-fill ph-house"></i> Dashboard</a>
                <i class="ph-bold ph-caret-right text-xs"></i>
                <span>Course</span>
            </div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                Matematika Wajib <span class="text-yumik-primary">• X MIPA 1</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-4">
                <span><i class="ph-bold ph-users text-slate-400"></i> 32 Siswa</span>
                <span><i class="ph-bold ph-clock text-slate-400"></i> Setiap Senin, 07:00</span>
            </p>
        </div>
        <a href="#" class="px-5 py-2.5 bg-white dark:bg-yumik-surface-dark text-slate-700 dark:text-white border border-slate-200 dark:border-white/10 rounded-full font-semibold hover:border-yumik-primary transition-all flex items-center gap-2 shadow-sm">
            <i class="ph-bold ph-gear"></i> Pengaturan
        </a>
    </div>

    <!-- TABS Navigation (Menggunakan x-tabs jika ada) -->
    <div class="flex gap-2 overflow-x-auto pb-2 hide-scrollbar border-b border-slate-200 dark:border-white/10">
        <button class="px-6 py-3 border-b-2 border-yumik-primary text-yumik-primary font-bold whitespace-nowrap">Alur Pertemuan</button>
        <button class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap">Buku Nilai (Gradebook)</button>
        <button class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap">Bank Soal & Kuis</button>
        <button class="px-6 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-white font-medium whitespace-nowrap">Siswa</button>
    </div>

    <!-- Timeline Pertemuan -->
    <div class="bg-white dark:bg-yumik-surface-dark p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-white/5">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Alur Pembelajaran</h2>
            <button class="text-yumik-primary bg-yumik-primary/10 hover:bg-yumik-primary/20 px-4 py-2 rounded-xl font-semibold transition-colors flex items-center gap-2">
                <i class="ph-bold ph-plus"></i> Tambah Pertemuan
            </button>
        </div>

        <div class="space-y-4">
            <!-- Session Card (Sudah Selesai) -->
            <div class="flex flex-col lg:flex-row gap-6 p-5 rounded-2xl border border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-[#1A1F29]/50 opacity-70">
                <div class="w-24 text-center shrink-0">
                    <div class="text-sm text-slate-500 font-bold">PERT</div>
                    <div class="text-3xl font-black text-slate-300">01</div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Pengantar Aljabar</h3>
                    <p class="text-sm text-slate-500 mb-4"><i class="ph-fill ph-calendar text-slate-400"></i> Senin, 5 Okt 2026</p>
                    
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-bold flex items-center gap-1"><i class="ph-bold ph-check"></i> Absensi Selesai</span>
                        <span class="px-3 py-1 bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 rounded-full text-xs font-semibold flex items-center gap-1"><i class="ph-fill ph-file-pdf"></i> Modul PDF</span>
                    </div>
                </div>
            </div>

            <!-- Session Card (Aktif/Mendatang) - Lebih Highlighted -->
            <div class="flex flex-col lg:flex-row gap-6 p-5 rounded-2xl border-2 border-yumik-primary/30 bg-white dark:bg-yumik-surface-dark shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 px-4 py-1 bg-yumik-primary text-white text-xs font-bold rounded-bl-xl">AKTIF</div>
                
                <div class="w-24 text-center shrink-0">
                    <div class="text-sm text-yumik-primary font-bold">PERT</div>
                    <div class="text-3xl font-black text-slate-800 dark:text-white">02</div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Persamaan Linear Satu Variabel</h3>
                    <p class="text-sm text-slate-500 mb-4"><i class="ph-fill ph-calendar text-slate-400"></i> Senin, 12 Okt 2026</p>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded-full text-xs font-bold flex items-center gap-1"><i class="ph-bold ph-warning-circle"></i> Absensi (12/32)</span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full text-xs font-bold flex items-center gap-1"><i class="ph-fill ph-exam"></i> Kuis Terjadwal</span>
                    </div>

                    <!-- Tombol Masuk Session -->
                    <a href="#" class="inline-flex items-center gap-2 px-5 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold hover:bg-yumik-primary dark:hover:bg-yumik-primary dark:hover:text-white transition-colors">
                        Buka Workspace Pertemuan <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection