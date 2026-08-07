<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }
}; ?>

<div>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Lupa Sandi?</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
            Tidak masalah. Beri tahu kami alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata
            sandi.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email Akses</label>
            <input wire:model="email" id="email" type="email" name="email" required autofocus
                class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors duration-300"
                placeholder="admin@caffe.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-8">
            <button type="submit"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-slate-800 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/30">
                <span wire:loading.remove wire:target="sendPasswordResetLink">Kirim Tautan Reset</span>
                <span wire:loading wire:target="sendPasswordResetLink">Mengirim...</span>
            </button>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" wire:navigate
                class="text-sm font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 transition-colors">
                &larr; Kembali ke Login
            </a>
        </div>
    </form>
</div>
