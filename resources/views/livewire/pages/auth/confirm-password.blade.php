<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $password = '';

    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (
            !Auth::guard('web')->validate([
                'email' => Auth::user()->email,
                'password' => $this->password,
            ])
        ) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-6 text-center">
        <div
            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 mb-4">
            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Area Keamanan</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
            Ini adalah area aman aplikasi. Harap konfirmasi kata sandi Anda sebelum melanjutkan.
        </p>
    </div>

    <form wire:submit="confirmPassword">
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kata
                Sandi</label>
            <input wire:model="password" id="password" type="password" name="password" required
                autocomplete="current-password"
                class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors duration-300"
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-slate-800 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/30">
                <span wire:loading.remove wire:target="confirmPassword">Konfirmasi</span>
                <span wire:loading wire:target="confirmPassword">Memproses...</span>
            </button>
        </div>
    </form>
</div>
