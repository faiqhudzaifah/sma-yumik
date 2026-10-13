@extends('layouts.app')

@section('title', 'Profil Saya - SMA Yumik Kulsum')

@section('content')
@php
    /** @var \App\Models\User $user */
    $guru = $user->guru;
    $siswa = $user->siswa;
    $currentPhoto = $guru?->photo ?? $siswa?->photo;

    // Tentukan route dashboard sesuai role
    $dashboardRoute = match((int) $user->role_id) {
        1 => 'admin.dashboard',
        2 => 'guru.dashboard',
        3 => 'siswa.dashboard',
        4 => 'operator.dashboard',
        default => 'login',
    };
@endphp

<div class="max-w-3xl mx-auto space-y-6">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route($dashboardRoute) }}" class="hover:text-yumik-primary transition-colors">Dashboard</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold">Profil Saya</span>
    </nav>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
            <i class="ph-fill ph-check-circle text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-sm font-bold text-red-600 dark:text-red-400">
            <div class="flex items-start gap-2">
                <i class="ph-fill ph-warning-circle text-lg shrink-0 mt-0.5"></i>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- FORM PROFIL --}}
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- HEADER + FOTO --}}
        <x-card class="p-6 lg:p-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <div class="relative shrink-0">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-yumik-primary to-yumik-accent p-1">
                        <div class="w-full h-full rounded-full bg-white dark:bg-[#1A1F29] flex items-center justify-center overflow-hidden">
                            @if($currentPhoto)
                                {{-- Cache buster: pakai waktu file sebagai query string --}}
                                <img id="photo-preview" src="{{ asset('storage/' . $currentPhoto) . '?v=' . time() }}" alt="Foto Profil" class="w-full h-full object-cover">
                            @else
                                <img id="photo-preview" src="" alt="" class="w-full h-full object-cover hidden">
                                <div id="photo-placeholder" class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="ph-fill ph-user text-4xl"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <label for="photo" class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-yumik-primary text-white flex items-center justify-center shadow-lg hover:bg-green-600 active:scale-95 transition-all cursor-pointer">
                        <i class="ph-bold ph-camera text-sm"></i>
                        <input type="file" id="photo" name="photo" accept="image/*" class="hidden" onchange="previewPhoto(event)">
                    </label>
                </div>

                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ $user->email }}</p>
                    <p class="text-xs text-slate-400 mt-2">
                        <i class="ph-bold ph-identification-card"></i>
                        {{ ucfirst($user->role->name ?? 'Pengguna') }}
                    </p>
                    <p class="text-[11px] text-slate-400 mt-1">
                        <i class="ph-bold ph-info"></i>
                        Klik ikon kamera untuk ganti foto (max 2MB)
                    </p>
                </div>
            </div>
        </x-card>

        {{-- DATA DASAR --}}
        <x-card class="p-6 lg:p-8">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                <i class="ph-fill ph-user-circle text-yumik-primary text-xl"></i>
                Data Dasar
            </h2>

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <x-input name="name" value="{{ old('name', $user->name) }}" icon="user" required maxlength="255" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Email <span class="text-red-500">*</span></label>
                    <x-input type="email" name="email" value="{{ old('email', $user->email) }}" icon="envelope" required maxlength="255" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">No. Telepon</label>
                    <x-input name="phone" value="{{ old('phone', $guru?->phone ?? $siswa?->phone) }}" icon="phone" maxlength="20" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Alamat</label>
                    <textarea name="address" rows="3" maxlength="500"
                        class="w-full p-4 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all resize-none"
                        placeholder="Alamat lengkap...">{{ old('address', $guru?->address ?? $siswa?->address) }}</textarea>
                </div>
            </div>
        </x-card>

        {{-- DATA GURU --}}
        @if($user->role_id == 2 && $guru)
            <x-card class="p-6 lg:p-8">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                    <i class="ph-fill ph-chalkboard-teacher text-yumik-primary text-xl"></i>
                    Data Guru
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Gelar Depan</label>
                        <x-input name="front_title" value="{{ old('front_title', $guru->front_title) }}" placeholder="Drs., Ir., dll" icon="text-t" maxlength="20" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Gelar Belakang</label>
                        <x-input name="back_title" value="{{ old('back_title', $guru->back_title) }}" placeholder="S.Pd., M.Kom., dll" icon="text-t" maxlength="50" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">NIP</label>
                        <x-input name="nip" value="{{ old('nip', $guru->nip) }}" placeholder="Nomor Induk Pegawai" icon="identification-card" maxlength="30" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">NUPTK</label>
                        <x-input name="nuptk" value="{{ old('nuptk', $guru->nuptk) }}" placeholder="Nomor Unik Pendidik" icon="identification-card" maxlength="30" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Status Kepegawaian</label>
                        <x-select name="employment_status" icon="briefcase">
                            <option value="PNS" {{ old('employment_status', $guru->employment_status) == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="PPPK" {{ old('employment_status', $guru->employment_status) == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                            <option value="GTY" {{ old('employment_status', $guru->employment_status) == 'GTY' ? 'selected' : '' }}>GTY (Guru Tetap Yayasan)</option>
                            <option value="Honorer" {{ old('employment_status', $guru->employment_status) == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                        </x-select>
                    </div>
                </div>
            </x-card>
        @endif

        {{-- DATA SISWA --}}
        @if($user->role_id == 3 && $siswa)
            <x-card class="p-6 lg:p-8">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                    <i class="ph-fill ph-student text-yumik-primary text-xl"></i>
                    Data Siswa
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">NISN</label>
                        <x-input name="nisn" value="{{ old('nisn', $siswa->nisn) }}" placeholder="Nomor Induk Siswa Nasional" icon="identification-card" maxlength="20" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">NIS</label>
                        <x-input name="nis" value="{{ old('nis', $siswa->nis) }}" placeholder="Nomor Induk Sekolah" icon="identification-card" maxlength="20" />
                    </div>
                </div>
            </x-card>
        @endif

        {{-- TOMBOL --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route($dashboardRoute) }}">
                <x-button type="button" variant="secondary">Batal</x-button>
            </a>
            <x-button type="submit" variant="primary" class="shadow-lg shadow-yumik-primary/30">
                <i class="ph-bold ph-floppy-disk"></i> Simpan Perubahan
            </x-button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-placeholder');

            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush
@endsection