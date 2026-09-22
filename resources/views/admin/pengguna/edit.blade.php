@extends('layouts.app')

@section('title', 'Edit Pengguna - LMS')

@section('content')
    <div class="mb-8">
        <x-breadcrumbs>
            <x-breadcrumb-item href="{{ route('admin.dashboard') }}">Dashboard</x-breadcrumb-item>
            <x-breadcrumb-item href="{{ route('admin.pengguna.index') }}">Kelola Pengguna</x-breadcrumb-item>
            <x-breadcrumb-item active="true">Edit Data</x-breadcrumb-item>
        </x-breadcrumbs>
    </div>

    <!-- PENGATURAN ALERT (Tanpa margin bottom) -->
    @if ($errors->any())
        <x-alert type="warning" title="Periksa Input Anda" message="Ada field yang belum diisi dengan benar." />
    @endif
    @if(session('error'))
        <x-alert type="error" title="Gagal Memperbarui!" message="{{ session('error') }}" />
    @endif

    <form action="{{ route('admin.pengguna.update', $pengguna->id) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="role_id" value="{{ $pengguna->role_id }}">

        <div class="lg:col-span-8 flex flex-col gap-6">
            <!-- Info Kredensial -->
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Informasi Kredensial</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NAMA LENGKAP</label>
                        <x-input type="text" icon="user" name="name" value="{{ old('name', $pengguna->name) }}" required />
                        @error('name') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">USERNAME (UNIK)</label>
                        <x-input type="text" icon="identification-card" name="username" value="{{ old('username', $pengguna->username) }}" required />
                        @error('username') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">ALAMAT EMAIL</label>
                        <x-input type="email" icon="envelope-simple" name="email" value="{{ old('email', $pengguna->email) }}" />
                        @error('email') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">KATA SANDI BARU (OPSIONAL)</label>
                        <x-input type="password" icon="lock-key" name="password" placeholder="Kosongkan jika tidak diubah" />
                    </div>
                </div>
            </x-card>

            @if(in_array($pengguna->role_id, [2, 3]))
            @php 
                $profile = $pengguna->role_id == 2 ? $pengguna->guru : $pengguna->siswa;
            @endphp
            <!-- Demografi Umum -->
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Demografi & Kontak</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TEMPAT LAHIR</label>
                        <x-input type="text" icon="map-pin" name="place_of_birth" value="{{ old('place_of_birth', $profile?->place_of_birth) }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TANGGAL LAHIR</label>
                        <x-input type="date" icon="calendar" name="date_of_birth" value="{{ old('date_of_birth', $profile?->date_of_birth) }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">JENIS KELAMIN</label>
                        <x-select icon="gender-intersex" name="gender">
                            <option value="L" {{ old('gender', $profile?->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $profile?->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">AGAMA</label>
                        <x-select icon="hands-praying" name="religion">
                            <option value="">Pilih Agama</option>
                            <option value="Islam" {{ old('religion', $profile?->religion) == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('religion', $profile?->religion) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NOMOR TELEPON</label>
                        <x-input type="text" icon="phone" name="phone" value="{{ old('phone', $profile?->phone) }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">ALAMAT LENGKAP</label>
                        <x-input type="text" icon="house" name="address" value="{{ old('address', $profile?->address) }}" />
                    </div>
                </div>
            </x-card>

            <!-- Profil Spesifik -->
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">
                    {{ $pengguna->role_id == 2 ? 'Data Kepegawaian' : 'Data Akademik & Orang Tua' }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @if($pengguna->role_id == 2)
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NIP</label>
                            <x-input type="text" icon="cardholder" name="nip" value="{{ old('nip', $profile?->nip) }}" />
                            @error('nip') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NUPTK</label>
                            <x-input type="text" icon="identification-badge" name="nuptk" value="{{ old('nuptk', $profile?->nuptk) }}" />
                            @error('nuptk') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">GELAR DEPAN</label>
                            <x-input type="text" icon="text-t" name="front_title" value="{{ old('front_title', $profile?->front_title) }}" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">GELAR BELAKANG</label>
                            <x-input type="text" icon="text-t" name="back_title" value="{{ old('back_title', $profile?->back_title) }}" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">STATUS PEGAWAI</label>
                            <x-select icon="briefcase" name="employment_status">
                                <option value="Honorer" {{ old('employment_status', $profile?->employment_status) == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                                <option value="PNS" {{ old('employment_status', $profile?->employment_status) == 'PNS' ? 'selected' : '' }}>PNS</option>
                                <option value="PPPK" {{ old('employment_status', $profile?->employment_status) == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                            </x-select>
                        </div>
                    @elseif($pengguna->role_id == 3)
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NISN</label>
                            <x-input type="text" icon="student" name="nisn" value="{{ old('nisn', $profile?->nisn) }}" />
                            @error('nisn') <span class="text-xs text-red-500 mt-1 pl-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NIS SEKOLAH</label>
                            <x-input type="text" icon="hash" name="nis" value="{{ old('nis', $profile?->nis) }}" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NAMA AYAH</label>
                            <x-input type="text" icon="user" name="father_name" value="{{ old('father_name', $profile?->father_name) }}" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NAMA IBU</label>
                            <x-input type="text" icon="user" name="mother_name" value="{{ old('mother_name', $profile?->mother_name) }}" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">NO TELEPON ORTU/WALI</label>
                            <x-input type="text" icon="phone" name="parent_phone" value="{{ old('parent_phone', $profile?->parent_phone) }}" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">TAHUN MASUK</label>
                            <x-input type="number" icon="calendar-blank" name="enrollment_year" value="{{ old('enrollment_year', $profile?->enrollment_year) }}" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 pl-2">STATUS SISWA</label>
                            <x-select icon="activity" name="status">
                                <option value="Aktif" {{ old('status', $profile?->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Lulus" {{ old('status', $profile?->status) == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                <option value="Pindah" {{ old('status', $profile?->status) == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                            </x-select>
                        </div>
                    @endif
                </div>
            </x-card>
            @endif
        </div>

        <div class="lg:col-span-4 flex flex-col gap-6">
            <x-card>
                <h3 class="text-sm font-bold tracking-widest uppercase text-yumik-primary mb-6">Tingkat Akses</h3>
                <div class="p-4 rounded-2xl border border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-black/20 opacity-70">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Akses Saat Ini</span>
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white mt-1">{{ ucfirst($pengguna->role->name) }}</h4>
                </div>
            </x-card>

            <div class="flex flex-col gap-3 mt-auto">
                <x-button variant="primary" type="submit" class="w-full py-4 shadow-lg">
                    <i class="ph-bold ph-check text-lg"></i> Simpan Perubahan
                </x-button>
                <a href="{{ route('admin.pengguna.index') }}" class="w-full">
                    <x-button variant="secondary" type="button" class="w-full py-4 border-none bg-transparent shadow-none text-slate-500">
                        Batal
                    </x-button>
                </a>
            </div>
        </div>
    </form>
@endsection