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

        if ($user->is_suspended) {
            return back()
                ->withInput($request->only('identifier', 'remember'))
                ->with('error', 'Akun Anda telah ditangguhkan (suspend) oleh admin. Silakan hubungi tim support.')
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
            'email'    => 'nullable|email|max:150|unique:users,email',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'password' => ['required', 'string', Password::min(8)],
            'role'     => 'nullable|in:penyuruh,disuruh',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'name.max'          => 'Nama maksimal 100 karakter.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah terdaftar.',
            'phone.required'    => 'Nomor WhatsApp aktif wajib diisi.',
            'phone.unique'      => 'Nomor WhatsApp ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
        ]);

        $role = $request->input('role', 'penyuruh');
        $phoneClean = preg_replace('/[^0-9]/', '', $request->input('phone'));
        $email = $request->input('email');
        if (empty($email)) {
            $email = $phoneClean . '@pensuruh.id';
        }

        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $email,
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
