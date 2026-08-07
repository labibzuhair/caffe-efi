<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset($this->only('email', 'password', 'password_confirmation', 'token'), function ($user) {
            $user
                ->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])
                ->save();

            event(new PasswordReset($user));
        });

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status));
        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Buat Sandi Baru</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
            Silakan masukkan kata sandi baru untuk akun staf Anda.
        </p>
    </div>

    <form wire:submit="resetPassword">
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email Akses</label>
            <input wire:model="email" id="email" type="email" name="email" required autofocus
                autocomplete="username"
                class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors duration-300">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Sandi
                Baru</label>
            <input wire:model="password" id="password" type="password" name="password" required
                autocomplete="new-password"
                class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors duration-300">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="password_confirmation"
                class="block text-sm font-medium text-slate-700 dark:text-slate-300">Konfirmasi Sandi</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password"
                name="password_confirmation" required autocomplete="new-password"
                class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors duration-300">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-8">
            <button type="submit"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-slate-800 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/30">
                <span wire:loading.remove wire:target="resetPassword">Simpan & Login</span>
                <span wire:loading wire:target="resetPassword">Memproses...</span>
            </button>
        </div>
    </form>
</div>
