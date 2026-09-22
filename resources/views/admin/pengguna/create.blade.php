@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru - LMS')

@section('content')
    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item href="{{ route('admin.pengguna.index') }}">Kelola Pengguna</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Tambah Baru</x-breadcrumb-item>
        </x-breadcrumbs>
    </div>

    <!-- PENGATURAN ALERT (Tanpa margin bottom) -->
    @if ($errors->any())
        <x-alert type="warning" title="Periksa Input Anda" message="Ada beberapa field yang belum diisi dengan benar." />
    @endif
    @if(session('error'))
        <x-alert type="error" title="Gagal Menyimpan!" message="{{ session('error') }}" />
    @endif

    <form action="{{ route('admin.pengguna.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf

        <div class="lg:col-span-8 flex flex-col gap-6">
            <!-- Info Utama -->
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Informasi Kredensial</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NAMA LENGKAP</label>
                        <x-input type="text" icon="user" name="name" value="{{ old('name') }}" required />
                        @error('name') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">USERNAME (UNIK)</label>
                        <x-input type="text" icon="identification-card" name="username" value="{{ old('username') }}" required />
                        @error('username') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">ALAMAT EMAIL (OPSIONAL)</label>
                        <x-input type="email" icon="envelope-simple" name="email" value="{{ old('email') }}" />
                        @error('email') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">KATA SANDI</label>
                        <x-input type="password" icon="lock-key" name="password" required />
                        @error('password') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-card>

            <!-- Demografi Umum (Guru & Siswa) -->
            <x-card id="panel-demografi" class="hidden">
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Demografi & Kontak</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TEMPAT LAHIR</label>
                        <x-input type="text" icon="map-pin" name="place_of_birth" value="{{ old('place_of_birth') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TANGGAL LAHIR</label>
                        <x-input type="date" icon="calendar" name="date_of_birth" value="{{ old('date_of_birth') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JENIS KELAMIN</label>
                        <x-select icon="gender-intersex" name="gender">
                            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">AGAMA</label>
                        <x-select icon="hands-praying" name="religion">
                            <option value="">Pilih Agama</option>
                            <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('religion') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('religion') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('religion') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('religion') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NOMOR TELEPON</label>
                        <x-input type="text" icon="phone" name="phone" value="{{ old('phone') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">ALAMAT LENGKAP</label>
                        <x-input type="text" icon="house" name="address" value="{{ old('address') }}" />
                    </div>
                </div>
            </x-card>

            <!-- Profil Spesifik -->
            <x-card id="panel-spesifik" class="hidden">
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6" id="judul-spesifik">Profil Akademik</h3>
                
                <!-- Spesifik Guru -->
                <div id="wrapper-guru" class="hidden grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NIP</label>
                        <x-input type="text" icon="cardholder" name="nip" value="{{ old('nip') }}" />
                        @error('nip') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NUPTK</label>
                        <x-input type="text" icon="identification-badge" name="nuptk" value="{{ old('nuptk') }}" />
                        @error('nuptk') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">GELAR DEPAN</label>
                        <x-input type="text" icon="text-t" name="front_title" value="{{ old('front_title') }}" placeholder="Contoh: Drs." />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">GELAR BELAKANG</label>
                        <x-input type="text" icon="text-t" name="back_title" value="{{ old('back_title') }}" placeholder="Contoh: S.Pd." />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">STATUS PEGAWAI</label>
                        <x-select icon="briefcase" name="employment_status">
                            <option value="Honorer" {{ old('employment_status') == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                            <option value="PNS" {{ old('employment_status') == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="PPPK" {{ old('employment_status') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                            <option value="GTY" {{ old('employment_status') == 'GTY' ? 'selected' : '' }}>GTY</option>
                        </x-select>
                    </div>
                </div>
                
                <!-- Spesifik Siswa -->
                <div id="wrapper-siswa" class="hidden grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NISN</label>
                        <x-input type="text" icon="student" name="nisn" value="{{ old('nisn') }}" />
                        @error('nisn') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NIS SEKOLAH</label>
                        <x-input type="text" icon="hash" name="nis" value="{{ old('nis') }}" />
                        @error('nis') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NAMA AYAH</label>
                        <x-input type="text" icon="user" name="father_name" value="{{ old('father_name') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NAMA IBU</label>
                        <x-input type="text" icon="user" name="mother_name" value="{{ old('mother_name') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NO TELEPON ORTU/WALI</label>
                        <x-input type="text" icon="phone" name="parent_phone" value="{{ old('parent_phone') }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TAHUN MASUK</label>
                        <x-input type="number" icon="calendar-blank" name="enrollment_year" value="{{ old('enrollment_year', date('Y')) }}" />
                    </div>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-4 flex flex-col gap-6">
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Tingkat Akses</h3>
                <div class="flex flex-col gap-3">
                    @foreach($roles as $role)
                        <div class="p-3 rounded-2xl border border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-black/10 hover:border-yumik-primary/50 transition-colors">
                            <x-radio id="role_{{ $role->id }}" name="role_id" value="{{ $role->id }}" label="{{ ucfirst($role->name) }}" :checked="old('role_id', 2) == $role->id" />                        
                        </div>
                    @endforeach
                </div>
            </x-card>

            <div class="flex flex-col gap-3 mt-auto">
                <x-button variant="primary" type="submit" class="w-full py-4 shadow-lg">
                    <i class="ph-bold ph-floppy-disk text-lg"></i> Simpan Pengguna
                </x-button>
            </div>
        </div>
    </form>

   @vite('resources/js/pengguna-form.js')
@endsection