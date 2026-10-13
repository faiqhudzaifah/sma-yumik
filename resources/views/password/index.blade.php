@extends('layouts.app')

@section('title', 'Ubah Password - SMA Yumik Kulsum')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
        <a href="{{ route('profile.index') }}" class="hover:text-yumik-primary transition-colors">Profil Saya</a>
        <i class="ph-bold ph-caret-right text-xs"></i>
        <span class="text-slate-800 dark:text-white font-bold">Ubah Password</span>
    </nav>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
            <i class="ph-fill ph-check-circle text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    <x-card class="p-6 lg:p-8">
        <div class="flex items-center gap-4 mb-7">
            <div class="w-12 h-12 rounded-[1rem] bg-amber-100 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center shrink-0">
                <i class="ph-fill ph-lock-key text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-slate-800 dark:text-white">Ubah Password</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Pastikan password baru minimal 8 karakter</p>
            </div>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Password Lama <span class="text-red-500">*</span></label>
                <x-input type="password" name="current_password" icon="lock" required />
                @error('current_password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Password Baru <span class="text-red-500">*</span></label>
                <x-input type="password" name="new_password" icon="lock-key" required minlength="8" />
                <p class="text-[11px] text-slate-400 mt-1">Minimal 8 karakter</p>
                @error('new_password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                <x-input type="password" name="new_password_confirmation" icon="lock-key" required />
                @error('new_password_confirmation')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-5 border-t border-slate-100 dark:border-white/10">
                <a href="{{ route('profile.index') }}">
                    <x-button type="button" variant="secondary">Batal</x-button>
                </a>
                <x-button type="submit" variant="primary" class="shadow-lg shadow-yumik-primary/30">
                    <i class="ph-bold ph-floppy-disk"></i> Ubah Password
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection