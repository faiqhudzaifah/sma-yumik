@extends('layouts.app')

@section('title', 'Konfigurasi Jam Pelajaran - LMS')

@section('content')
    @if(session('success'))
        <x-alert type="success" title="Berhasil!" message="{{ session('success') }}" />
    @endif
    @if(session('error'))
        <x-alert type="error" title="Gagal!" message="{{ session('error') }}" />
    @endif
    @if ($errors->any())
        <x-alert type="warning" title="Periksa Input Anda" message="Beberapa form belum diisi dengan benar." />
    @endif

    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Jam Pelajaran</x-breadcrumb-item>
        </x-breadcrumbs>

        <div class="mt-2">
            <h2 class="text-3xl font-light tracking-tight text-slate-800 dark:text-white">
                Konfigurasi <span class="font-bold">Waktu & Sesi.</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1">Atur rentang jam pelajaran, jadwal khusus hari Jumat, dan jam istirahat.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        
        <!-- KOLOM KIRI: Form Tambah Jam Pelajaran (4 Kolom) -->
        <div class="xl:col-span-4 flex flex-col gap-6">
            <x-card class="sticky top-6">
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Tambah Sesi Waktu</h3>
                
                <form action="{{ route('admin.jam-pelajaran.store') }}" method="POST" class="flex flex-col gap-5">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TIPE HARI</label>
                        <x-select icon="calendar" name="day_type" required>
                            <option value="Umum" {{ old('day_type') == 'Umum' ? 'selected' : '' }}>Hari Umum (Senin - Kamis, Sabtu)</option>
                            <option value="Jumat" {{ old('day_type') == 'Jumat' ? 'selected' : '' }}>Khusus Hari Jumat</option>
                        </x-select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JENIS KEGIATAN</label>
                        <x-select icon="activity" name="type" required>
                            <option value="Belajar" {{ old('type') == 'Belajar' ? 'selected' : '' }}>Sesi Belajar (KBM)</option>
                            <option value="Istirahat" {{ old('type') == 'Istirahat' ? 'selected' : '' }}>Jam Istirahat</option>
                            <option value="Upacara" {{ old('type') == 'Upacara' ? 'selected' : '' }}>Upacara / Apel</option>
                            <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Kegiatan Lainnya</option>
                        </x-select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2 flex justify-between">
                            <span>JAM PELAJARAN KE-</span>
                            <span class="font-normal text-[10px] italic">Kosongkan jika istirahat</span>
                        </label>
                        <x-input type="number" icon="list-numbers" name="jam_ke" value="{{ old('jam_ke') }}" placeholder="Contoh: 1" min="1" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JAM MULAI</label>
                            <x-input type="time" icon="clock" name="start_time" value="{{ old('start_time') }}" required />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JAM SELESAI</label>
                            <x-input type="time" icon="clock" name="end_time" value="{{ old('end_time') }}" required />
                        </div>
                    </div>

                    <div class="pt-2">
                        <x-button variant="primary" type="submit" class="w-full py-3 shadow-lg">
                            <i class="ph-bold ph-plus text-lg"></i> Tambahkan Sesi
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- KOLOM KANAN: Tabel Data Jam Pelajaran (8 Kolom) -->
        <div class="xl:col-span-8">
            <x-card class="h-full flex flex-col p-0 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-black/10">
                    <h3 class="text-sm font-bold tracking-widest uppercase text-slate-600 dark:text-slate-300">Daftar Waktu Operasional</h3>
                </div>
                
                <div class="flex-1 overflow-x-auto">
                    <x-table>
                        <x-slot name="head">
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-widest w-12">No</th>
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Tipe Hari</th>
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Jam Ke</th>
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Rentang Waktu</th>
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Kegiatan</th>
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                        </x-slot>

                        @forelse($lessonHours as $index => $jam)
                            <tr class="hover:bg-white/40 dark:hover:bg-white/5 transition-colors border-b border-slate-100 dark:border-white/5 {{ $jam->type == 'Istirahat' ? 'bg-amber-50/30 dark:bg-amber-900/10' : '' }}">
                                <td class="p-4 text-slate-500 font-medium">{{ $index + 1 }}</td>
                                
                                <td class="p-4">
                                    @if($jam->day_type == 'Jumat')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                                            <i class="ph-bold ph-calendar-star"></i> Jumat
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-slate-300">
                                            <i class="ph-bold ph-calendar"></i> Umum
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="p-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                    {{ $jam->jam_ke ?? '-' }}
                                </td>
                                
                                <td class="p-4">
                                    <div class="flex items-center gap-2 font-mono text-sm font-semibold text-slate-800 dark:text-white">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-black/30 border border-slate-200 dark:border-white/10">
                                            {{ \Carbon\Carbon::parse($jam->start_time)->format('H:i') }}
                                        </span>
                                        <span class="text-slate-400">-</span>
                                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-black/30 border border-slate-200 dark:border-white/10">
                                            {{ \Carbon\Carbon::parse($jam->end_time)->format('H:i') }}
                                        </span>
                                    </div>
                                </td>
                                
                                <td class="p-4">
                                    @php
                                        $typeColors = [
                                            'Belajar' => 'text-blue-600 dark:text-blue-400',
                                            'Istirahat' => 'text-amber-600 dark:text-amber-400',
                                            'Upacara' => 'text-purple-600 dark:text-purple-400',
                                            'Lainnya' => 'text-slate-600 dark:text-slate-400',
                                        ];
                                        $icons = [
                                            'Belajar' => 'ph-books',
                                            'Istirahat' => 'ph-coffee',
                                            'Upacara' => 'ph-flag-banner',
                                            'Lainnya' => 'ph-dots-three-circle',
                                        ];
                                    @endphp
                                    <span class="flex items-center gap-1.5 text-xs font-bold {{ $typeColors[$jam->type] ?? 'text-slate-500' }}">
                                        <i class="ph-bold {{ $icons[$jam->type] ?? 'ph-circle' }} text-base"></i>
                                        {{ $jam->type }}
                                    </span>
                                </td>
                                
                                <td class="p-4 text-right flex justify-end gap-2">
                                    <!-- Tombol Pemicu Edit -->
                                    <button type="button" onclick="document.getElementById('modal-edit-{{ $jam->id }}').classList.remove('hidden'); document.getElementById('modal-edit-{{ $jam->id }}').setAttribute('data-state', 'open');" class="w-9 h-9 rounded-full bg-amber-50 text-amber-500 dark:bg-amber-500/10 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-colors shadow-sm relative group">
                                        <i class="ph-bold ph-pencil-simple"></i>
                                    </button>

                                    <!-- Tombol Pemicu Hapus -->
                                    <button type="button" onclick="document.getElementById('modal-delete-{{ $jam->id }}').classList.remove('hidden'); document.getElementById('modal-delete-{{ $jam->id }}').setAttribute('data-state', 'open');" class="w-9 h-9 rounded-full bg-red-50 text-red-500 dark:bg-red-500/10 hover:bg-red-500 hover:text-white flex items-center justify-center transition-colors shadow-sm relative group">
                                        <i class="ph-bold ph-trash"></i>
                                    </button>

                                    <!-- Modal Konfirmasi Hapus (Menggunakan Komponen Anda) -->
                                    <x-modal id="modal-delete-{{ $jam->id }}" title="Hapus Sesi?">
                                        Menghapus rentang jam ini mungkin akan memengaruhi format jadwal yang sudah disusun. Anda yakin?
                                        <form action="{{ route('admin.jam-pelajaran.destroy', $jam->id) }}" method="POST" class="mt-6">
                                            @csrf
                                            @method('DELETE')
                                            <x-button variant="primary" type="submit" class="w-full bg-red-500 hover:bg-red-600 shadow-red-500/30">Ya, Hapus Sekarang</x-button>
                                        </form>
                                    </x-modal>

                                    <!-- Modal Form Edit (Kustom adaptasi gaya glassmorphism) -->
                                    <div id="modal-edit-{{ $jam->id }}" class="fixed inset-0 z-[100] hidden items-center justify-center p-6 bg-slate-900/30 dark:bg-black/60 backdrop-blur-md transition-opacity duration-300 opacity-0 data-[state=open]:opacity-100 data-[state=open]:flex text-left">
                                        <div class="w-full max-w-lg bg-white/95 dark:bg-yumik-surface-dark/95 backdrop-blur-2xl rounded-[2.5rem] p-8 shadow-2xl border border-white/60 dark:border-white/10 transform scale-95 data-[state=open]:scale-100 transition-transform duration-300 relative">
                                            
                                            <button type="button" onclick="document.getElementById('modal-edit-{{ $jam->id }}').setAttribute('data-state', 'closed'); setTimeout(() => document.getElementById('modal-edit-{{ $jam->id }}').classList.add('hidden'), 300)" class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-[#2A2A2A] text-slate-500 hover:text-red-500 transition-all">
                                                <i class="ph-bold ph-x"></i>
                                            </button>

                                            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6 tracking-tight">Edit Jam Pelajaran</h3>

                                            <form action="{{ route('admin.jam-pelajaran.update', $jam->id) }}" method="POST" class="flex flex-col gap-5">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TIPE HARI</label>
                                                    <x-select icon="calendar" name="day_type" required>
                                                        <option value="Umum" {{ $jam->day_type == 'Umum' ? 'selected' : '' }}>Hari Umum (Senin - Kamis, Sabtu)</option>
                                                        <option value="Jumat" {{ $jam->day_type == 'Jumat' ? 'selected' : '' }}>Khusus Hari Jumat</option>
                                                    </x-select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JENIS KEGIATAN</label>
                                                    <x-select icon="activity" name="type" required>
                                                        <option value="Belajar" {{ $jam->type == 'Belajar' ? 'selected' : '' }}>Sesi Belajar (KBM)</option>
                                                        <option value="Istirahat" {{ $jam->type == 'Istirahat' ? 'selected' : '' }}>Jam Istirahat</option>
                                                        <option value="Upacara" {{ $jam->type == 'Upacara' ? 'selected' : '' }}>Upacara / Apel</option>
                                                        <option value="Lainnya" {{ $jam->type == 'Lainnya' ? 'selected' : '' }}>Kegiatan Lainnya</option>
                                                    </x-select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-500 mb-2 pl-2 flex justify-between">
                                                        <span>JAM PELAJARAN KE-</span>
                                                        <span class="font-normal text-[10px] italic">Kosongkan jika istirahat</span>
                                                    </label>
                                                    <x-input type="number" icon="list-numbers" name="jam_ke" value="{{ $jam->jam_ke }}" min="1" />
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JAM MULAI</label>
                                                        <x-input type="time" icon="clock" name="start_time" value="{{ \Carbon\Carbon::parse($jam->start_time)->format('H:i') }}" required />
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JAM SELESAI</label>
                                                        <x-input type="time" icon="clock" name="end_time" value="{{ \Carbon\Carbon::parse($jam->end_time)->format('H:i') }}" required />
                                                    </div>
                                                </div>

                                                <div class="flex gap-3 mt-4">
                                                    <x-button variant="secondary" type="button" class="flex-1 py-3" onclick="document.getElementById('modal-edit-{{ $jam->id }}').setAttribute('data-state', 'closed'); setTimeout(() => document.getElementById('modal-edit-{{ $jam->id }}').classList.add('hidden'), 300)">Batal</x-button>
                                                    <x-button variant="primary" type="submit" class="flex-1 py-3">Simpan Perubahan</x-button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <i class="ph-fill ph-clock-dashed text-5xl mb-3 opacity-50"></i>
                                        <p>Konfigurasi jam pelajaran belum diatur.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </div>
            </x-card>
        </div>
    </div>
@endsection