<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'guru', 'siswa']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.pengguna.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.pengguna.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email'    => 'nullable|email|unique:users',
            'password' => 'required|string|min:8',
            'role_id'  => 'required|exists:roles,id',

            // Validasi Umum Profil
            'gender'         => 'nullable|in:L,P',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth'  => 'nullable|date',
            'religion'       => 'nullable|string|max:50',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string',

            // Validasi Guru
            'nip'               => 'nullable|required_if:role_id,2|string|unique:gurus',
            'nuptk'             => 'nullable|string|unique:gurus',
            'front_title'       => 'nullable|string|max:50',
            'back_title'        => 'nullable|string|max:50',
            'employment_status' => 'nullable|in:PNS,PPPK,GTY,Honorer',

            // Validasi Siswa
            'nisn'            => 'nullable|required_if:role_id,3|string|unique:siswas',
            'nis'             => 'nullable|string|unique:siswas',
            'father_name'     => 'nullable|string|max:255',
            'mother_name'     => 'nullable|string|max:255',
            'parent_phone'    => 'nullable|string|max:20',
            'enrollment_year' => 'nullable|required_if:role_id,3|numeric|digits:4',
            'status'          => 'nullable|in:Aktif,Lulus,Pindah,Keluar,Cuti',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $request->role_id,
            ]);

            $commonData = $request->only(['gender', 'place_of_birth', 'date_of_birth', 'religion', 'phone', 'address']);

            if ($request->role_id == 2) {
                $guruData = array_merge($commonData, $request->only(['nip', 'nuptk', 'front_title', 'back_title', 'employment_status']));
                $guruData['user_id'] = $user->id;
                Guru::create($guruData);
            } elseif ($request->role_id == 3) {
                $siswaData = array_merge($commonData, $request->only(['nisn', 'nis', 'father_name', 'mother_name', 'guardian_name', 'parent_phone', 'enrollment_year', 'status']));
                $siswaData['user_id'] = $user->id;
                $siswaData['status'] = $siswaData['status'] ?? 'Aktif';
                $siswaData['enrollment_year'] = $siswaData['enrollment_year'] ?? date('Y');
                Siswa::create($siswaData);
            }

            DB::commit();
            return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(User $pengguna)
    {
        $pengguna->load(['role', 'guru', 'siswa']);
        return view('admin.pengguna.show', compact('pengguna'));
    }

    public function edit(User $pengguna)
    {
        $pengguna->load(['guru', 'siswa']);
        $roles = Role::all();
        return view('admin.pengguna.edit', compact('pengguna', 'roles'));
    }

    public function update(Request $request, User $pengguna)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($pengguna->id)],
            'email'    => ['nullable', 'email', Rule::unique('users')->ignore($pengguna->id)],
            'password' => 'nullable|string|min:8',

            'gender'         => 'nullable|in:L,P',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth'  => 'nullable|date',
            'religion'       => 'nullable|string|max:50',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string',

            'nip'               => ['nullable', 'required_if:role_id,2', Rule::unique('gurus')->ignore($pengguna->guru?->id)],
            'nuptk'             => ['nullable', Rule::unique('gurus')->ignore($pengguna->guru?->id)],
            'front_title'       => 'nullable|string|max:50',
            'back_title'        => 'nullable|string|max:50',
            'employment_status' => 'nullable|in:PNS,PPPK,GTY,Honorer',

            'nisn'            => ['nullable', 'required_if:role_id,3', Rule::unique('siswas')->ignore($pengguna->siswa?->id)],
            'nis'             => ['nullable', Rule::unique('siswas')->ignore($pengguna->siswa?->id)],
            'father_name'     => 'nullable|string|max:255',
            'mother_name'     => 'nullable|string|max:255',
            'parent_phone'    => 'nullable|string|max:20',
            'enrollment_year' => 'nullable|required_if:role_id,3|numeric|digits:4',
            'status'          => 'nullable|in:Aktif,Lulus,Pindah,Keluar,Cuti',
        ]);

        try {
            DB::beginTransaction();

            $userData = $request->only(['name', 'username', 'email']);
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $pengguna->update($userData);

            $commonData = $request->only(['gender', 'place_of_birth', 'date_of_birth', 'religion', 'phone', 'address']);

            if ($pengguna->role_id == 2) {
                $guruData = array_merge($commonData, $request->only(['nip', 'nuptk', 'front_title', 'back_title', 'employment_status']));
                Guru::updateOrCreate(['user_id' => $pengguna->id], $guruData);
            } elseif ($pengguna->role_id == 3) {
                $siswaData = array_merge($commonData, $request->only(['nisn', 'nis', 'father_name', 'mother_name', 'guardian_name', 'parent_phone', 'enrollment_year', 'status']));
                Siswa::updateOrCreate(['user_id' => $pengguna->id], $siswaData);
            }

            DB::commit();
            return redirect()->route('admin.pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(User $pengguna)
    {
        if (Auth::id() === $pengguna->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        $pengguna->delete();
        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
