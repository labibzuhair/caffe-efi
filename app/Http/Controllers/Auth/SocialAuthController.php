<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan saat menghubungi ' . ucfirst($provider));
        }

        $user = \App\Models\User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            return redirect('/login')->withErrors([
                'email' => 'Akses ditolak. Akun Anda tidak terdaftar sebagai staf.'
            ]);
        }


        if ($user) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
            ]);

            \Illuminate\Support\Facades\Auth::login($user);
            return redirect()->route('dashboard');
        }

        return redirect('/login')->with('error', 'Akses ditolak. Email Anda belum terdaftar sebagai staf kafe.');
    }
}
