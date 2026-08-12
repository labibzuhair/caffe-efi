<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ExtraInformation extends Component
{
    public function unlink()
    {
        $user = Auth::user();

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

        return view('livewire.profile.extra-information', [
            'currentProvider' => $user->provider, 
            'isLinked' => !is_null($user->provider_id),
        ]);
    }
}
