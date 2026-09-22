@extends('layouts.app')

@section('title', 'Review Kenaikan Kelas - LMS')

@section('content')
    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item href="{{ route('admin.promotion.index') }}">Kenaikan Kelas</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Proses {{ $rombel->name }}</x-breadcrumb-item>
        </x-breadcrumbs>
    </div>

    @if(!$nextSemester)
        <x-alert type="warning" title="Peringatan Kritis Sistem" message="Data Semester berikutnya belum tersedia. Anda tidak bisa memproses kenaikan kelas jika Tahun Ajaran / Semester tujuan belum dibuat." />
    @endif

    <form action="{{ route('admin.promotion.process', $rombel->id) }}" method="POST">
        @csrf
        
        <!-- Hidden input untuk target Tahun Ajaran Baru sesuai requirement Controller -->
        <input type="hidden" name="academic_year_id" value="{{ $nextSemester ? $nextSemester->academic_year_id : $rombel->semester->academic_year_id }}">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Daftar Siswa -->
            <div class="lg:col-span-9 flex flex-col gap-6">
                <x-card class="p-0 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-black/10 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-bold tracking-widest uppercase text-slate-600 dark:text-slate-300">Daftar Siswa Kelas {{ $rombel->name }}</h3>
                            <p class="text-xs text-slate-500 mt-1">Tentukan status akhir siswa dan kelas tujuan di tahun ajaran baru.</p>
                        </div>
                        <span class="px-3 py-1 bg-white dark:bg-[#1A1D24] rounded-full text-xs font-bold shadow-sm border border-slate-200 dark:border-white/10">
                            Total: {{ $rombel->siswas->count() }}
                        </span>
                    </div>

                    <div class="overflow-x-auto p-2">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Siswa</th>
                                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5 text-center">Status Keputusan</th>
                                    <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Kelas Tujuan (Jika Naik/Tinggal)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rombel->siswas as $siswa)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border-b border-slate-50 dark:border-white/5">
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <x-avatar size="sm" />
                                                <div>
                                                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ $siswa->user->name }}</h4>
                                                    <span class="text-[10px] text-slate-500 font-mono">{{ $siswa->nisn }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <!-- Logic JS Sederhana: Jika memilih LULUS, disable dropdown kelas tujuan -->
                                            <select name="decisions[{{ $siswa->id }}][status]" 
                                                    onchange="document.getElementById('target_{{ $siswa->id }}').disabled = (this.value === 'LULUS')"
                                                    class="w-full bg-white dark:bg-[#1A1D24] border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm font-bold text-slate-700 dark:text-slate-300 focus:border-yumik-primary focus:ring-1 focus:ring-yumik-primary outline-none transition-all">
                                                <option value="NAIK" {{ $recommendations[$siswa->id] == 'NAIK' ? 'selected' : '' }}>NAIK KELAS</option>
                                                <option value="TINGGAL" {{ $recommendations[$siswa->id] == 'TINGGAL' ? 'selected' : '' }}>TINGGAL KELAS</option>
                                                <option value="LULUS" {{ $recommendations[$siswa->id] == 'LULUS' ? 'selected' : '' }}>LULUS ALUMNI</option>
                                            </select>
                                        </td>
                                        <td class="p-4">
                                            <select id="target_{{ $siswa->id }}" name="decisions[{{ $siswa->id }}][to_classroom_id]" 
                                                    {{ $recommendations[$siswa->id] == 'LULUS' ? 'disabled' : '' }}
                                                    class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-sm text-slate-700 dark:text-slate-300 focus:border-yumik-primary focus:ring-1 focus:ring-yumik-primary outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                                <option value="">-- Pilih Kelas Tujuan --</option>
                                                @foreach($targetClassrooms as $target)
                                                    <option value="{{ $target->id }}">{{ $target->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-8 text-center text-slate-400">Tidak ada siswa di kelas ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            <!-- Panel Eksekusi -->
            <div class="lg:col-span-3 flex flex-col gap-6">
                <x-card class="bg-gradient-to-br from-purple-50 to-white dark:from-purple-900/10 dark:to-transparent border-purple-100 dark:border-purple-500/20">
                    <h3 class="text-sm font-bold tracking-widest uppercase text-purple-600 dark:text-purple-400 mb-4">Informasi Transisi</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dari</span>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $rombel->semester->academicYear->name }}</p>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ke Target</span>
                            <p class="text-sm font-bold {{ $nextSemester ? 'text-yumik-primary' : 'text-red-500' }}">
                                {{ $nextSemester ? $nextSemester->academicYear->name : 'SEMESTER BARU BELUM DIBUAT' }}
                            </p>
                        </div>
                    </div>
                </x-card>

                <div class="flex flex-col gap-3 mt-auto">
                    <x-button variant="primary" type="submit" class="w-full py-4 shadow-lg bg-purple-600 hover:bg-purple-700 shadow-purple-500/30" :disabled="!$nextSemester || $rombel->siswas->isEmpty()">
                        <i class="ph-bold ph-check-circle text-lg"></i> Eksekusi Kenaikan
                    </x-button>
                    <a href="{{ route('admin.promotion.index') }}" class="w-full">
                        <x-button variant="secondary" type="button" class="w-full py-4 border-none bg-transparent shadow-none text-slate-500">Batalkan</x-button>
                    </a>
                </div>
            </div>

        </div>
    </form>
@endsection