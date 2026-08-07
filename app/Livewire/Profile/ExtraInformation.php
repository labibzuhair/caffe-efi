<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ExtraInformation extends Component
{
    public function unlink()
    {
        $user = Auth::user();

        // Kosongkan semua kolom OAuth
        $user->update([
            'provider' => null,
            'provider_id' => null,
            'provider_token' => null
        ]);

        session()->flash('message', 'Tautan akun berhasil diputus.');
    }

    public function render()
    {
        $user = Auth::user();

        // Karena hanya ada 1 kolom 'provider', user hanya bisa menautkan SALAH SATU (Google ATAU Github)
        return view('livewire.profile.extra-information', [
            'currentProvider' => $user->provider, // 'google', 'github', atau null
            'isLinked' => !is_null($user->provider_id),
        ]);
    }
}
