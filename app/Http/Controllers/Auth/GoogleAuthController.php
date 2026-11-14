<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect pengguna ke halaman autentikasi Google.
     */
    public function redirectToGoogle()
    {
        // Cek apakah credential Google sudah diisi di .env
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect('/login')->with('error', 'Google Client ID & Secret belum diisi di file .env. Silakan lengkapi konfigurasinya.');
        }

        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Tangani callback dari Google setelah user menyetujui.
     */
    public function handleGoogleCallback()
    {
        // Jika user sudah dalam keadaan login, langsung arahkan ke dashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cari user berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Update google_id & avatar jika belum ada
                $user->update([
                    'google_id' => $user->google_id ?: $googleUser->getId(),
                    'avatar'    => $user->avatar ?: $googleUser->getAvatar(),
                ]);
            } else {
                // Buat user baru
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'password'          => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);

            return redirect('/dashboard')->with('success', 'Berhasil masuk dengan akun Google.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Login Error: ' . $e->getMessage());

            if (str_contains($e->getMessage(), 'invalid_grant')) {
                $errorMsg = 'Sesi otorisasi Google telah digunakan atau kedaluwarsa. Silakan klik tombol "Masuk dengan Google" kembali.';
            } else {
                $errorMsg = $e->getMessage() ?: 'Terjadi kendala saat memvalidasi akun Google. Silakan coba kembali.';
            }

            return redirect('/login')->with('error', 'Gagal masuk dengan Google: ' . $errorMsg);
        }
    }
}

