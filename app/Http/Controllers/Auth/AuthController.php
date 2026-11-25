<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Proses login pengguna.
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string',
        ], [
            'identifier.required' => 'Nomor WhatsApp atau email wajib diisi.',
            'password.required'   => 'Password wajib diisi.',
        ]);

        $identifier = $request->input('identifier');
        $password   = $request->input('password');
        $remember   = $request->boolean('remember');

        // Coba cari user berdasarkan email atau phone
        $user = User::where('email', $identifier)
                    ->orWhere('phone', $identifier)
                    ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()
                ->withInput($request->only('identifier', 'remember'))
                ->with('error', 'Email/nomor WhatsApp atau password salah. Coba lagi.')
                ->with('tab', 'login');
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard')
                         ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Proses registrasi pengguna baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'password' => ['required', 'string', Password::min(8)],
            'role'     => 'nullable|in:penyuruh,disuruh',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'name.max'          => 'Nama maksimal 100 karakter.',
            'phone.required'    => 'Nomor WhatsApp aktif wajib diisi.',
            'phone.unique'      => 'Nomor WhatsApp ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
        ]);

        $role = $request->input('role', 'penyuruh');

        $user = User::create([
            'name'     => $request->input('name'),
            'phone'    => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'role'     => $role,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard')
                   ->with('success', 'Akun berhasil dibuat! Selamat datang di PenSuruh.');
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Kamu berhasil keluar.');
    }
}
