<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // Mengarahkan user ke halaman login Google/GitHub
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // Menangani kembalian data dari Google/GitHub
    public function callback($provider)
    {
        try {
            $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan saat menghubungi ' . ucfirst($provider));
        }

        // Cari user berdasarkan email
        $user = \App\Models\User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            return redirect('/login')->withErrors([
                'email' => 'Akses ditolak. Akun Anda tidak terdaftar sebagai staf.'
            ]);
        }


        if ($user) {
            // INI KODE YANG HILANG: Update kolom provider dengan data dari Google/GitHub
            $user->update([
                'provider' => $provider,                 // Menyimpan 'google' atau 'github'
                'provider_id' => $socialUser->getId(),   // Menyimpan ID unik
                'provider_token' => $socialUser->token,  // Menyimpan token akses
            ]);

            // Login dan arahkan ke dashboard
            \Illuminate\Support\Facades\Auth::login($user);
            return redirect()->route('dashboard');
        }

        // Jika email tidak ada di database (bukan staf)
        return redirect('/login')->with('error', 'Akses ditolak. Email Anda belum terdaftar sebagai staf kafe.');
    }
}
