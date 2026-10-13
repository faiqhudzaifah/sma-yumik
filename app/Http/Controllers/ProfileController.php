<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load(['guru', 'siswa']);

        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validasi dasar (semua role)
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'photo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
        ];

        // Validasi tambahan untuk guru
        if ($user->role_id == 2 && $user->guru) {
            $rules = array_merge($rules, [
                'front_title'        => 'nullable|string|max:20',
                'back_title'         => 'nullable|string|max:50',
                'nip'                => 'nullable|string|max:30',
                'nuptk'              => 'nullable|string|max:30',
                'employment_status'  => 'nullable|in:PNS,PPPK,GTY,Honorer',
            ]);
        }

        // Validasi tambahan untuk siswa
        if ($user->role_id == 3 && $user->siswa) {
            $rules = array_merge($rules, [
                'nisn' => 'nullable|string|max:20',
                'nis'  => 'nullable|string|max:20',
            ]);
        }

        $validated = $request->validate($rules);

        // Update user
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Handle upload foto
        if ($request->hasFile('photo')) {
            // Hapus foto lama
            $oldPhoto = $user->guru?->photo ?? $user->siswa?->photo;
            if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                Storage::disk('public')->delete($oldPhoto);
            }

            // Simpan foto baru
            $photoPath = $request->file('photo')->store('photos', 'public');

            if ($user->role_id == 2 && $user->guru) {
                $user->guru->photo = $photoPath;
            } elseif ($user->role_id == 3 && $user->siswa) {
                $user->siswa->photo = $photoPath;
            } else {
                // Fallback: simpan di kolom user kalau ada, atau skip
                // (kalau tabel users tidak punya kolom photo, buat migration dulu)
            }
        }

        $user->save();

        // Update data guru
        if ($user->role_id == 2 && $user->guru) {
            $user->guru->front_title = $validated['front_title'] ?? null;
            $user->guru->back_title = $validated['back_title'] ?? null;
            $user->guru->nip = $validated['nip'] ?? null;
            $user->guru->nuptk = $validated['nuptk'] ?? null;
            $user->guru->phone = $validated['phone'] ?? null;
            $user->guru->address = $validated['address'] ?? null;

            if (isset($validated['employment_status'])) {
                $user->guru->employment_status = $validated['employment_status'];
            }

            $user->guru->save();
        }

        // Update data siswa
        if ($user->role_id == 3 && $user->siswa) {
            $user->siswa->nisn = $validated['nisn'] ?? $user->siswa->nisn;
            $user->siswa->nis = $validated['nis'] ?? $user->siswa->nis;
            $user->siswa->phone = $validated['phone'] ?? null;
            $user->siswa->address = $validated['address'] ?? null;
            $user->siswa->save();
        }

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui.');
    }
}
