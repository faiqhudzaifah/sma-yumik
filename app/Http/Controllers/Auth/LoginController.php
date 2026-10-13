<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm()
    {
        // Kalau sudah login, redirect ke dashboard sesuai role
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role_id);
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('username', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return $this->redirectBasedOnRole(Auth::user()->role_id);
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors(['username' => 'Username atau password salah.']);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect sesuai role.
     */
    private function redirectBasedOnRole($roleId)
    {
        return match ((int) $roleId) {
            1 => redirect()->route('admin.dashboard'),
            2 => redirect()->route('guru.dashboard'),
            3 => redirect()->route('siswa.dashboard'),
            4 => redirect()->route('operator.dashboard'),
            default => redirect('/'),
        };
    }
}
