<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $userRole = auth()->user()->role;

        $redirectRoute = match ($userRole) {
            'kitchen' => route('dapur', absolute: false),
            'cashier' => route('kasir', absolute: false),
            default => route('dashboard', absolute: false),
        };

        $this->redirectIntended(default: $redirectRoute, navigate: true);
    }
}; ?>

<div>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Selamat Datang</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Silakan masuk ke akun Anda untuk melanjutkan.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->has('email') && !$errors->has('form.email'))
        <div
            class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 flex items-start gap-3 transition-all duration-300">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="text-sm font-medium text-red-600 dark:text-red-400 leading-relaxed">
                {{ $errors->first('email') }}
            </span>
        </div>
    @endif

    <form wire:submit.prevent="login">
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email
                Akses</label>
            <input wire:model="form.email" id="email" type="email" name="email" required autofocus
                autocomplete="username"
                class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors duration-300"
                placeholder="admin@caffe.com">
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div class="mt-5">
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kata
                Sandi</label>
            <input wire:model="form.password" id="password" type="password" name="password" required
                autocomplete="current-password"
                class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors duration-300"
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-5">
            <label for="remember" class="flex items-center cursor-pointer">
                <input wire:model="form.remember" id="remember" type="checkbox" name="remember"
                    class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 shadow-sm focus:ring-emerald-500 dark:bg-slate-900 cursor-pointer">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 transition-colors"
                    href="{{ route('password.request') }}" wire:navigate>
                    Lupa sandi?
                </a>
            @endif
        </div>

        <div class="mt-8">
            <button type="submit"
                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-slate-800 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/30">
                <span wire:loading.remove wire:target="login">Masuk ke Dashboard</span>
                <span wire:loading wire:target="login" class="flex items-center">
                    Memproses...
                    <svg class="animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </span>
            </button>
        </div>
    </form>

    <div class="mt-8">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200 dark:border-slate-700/70"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-3 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400">Atau masuk
                    dengan</span>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4">
            <a href="{{ route('social.redirect', 'google') }}"
                class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm bg-slate-50 dark:bg-slate-900/50 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-300 hover:-translate-y-0.5">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                        fill="#4285F4" />
                    <path
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                        fill="#34A853" />
                    <path
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                        fill="#FBBC05" />
                    <path
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                        fill="#EA4335" />
                </svg>
                Google
            </a>

            <a href="{{ route('social.redirect', 'github') }}"
                class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm bg-slate-50 dark:bg-slate-900/50 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-900 transition-all duration-300 hover:-translate-y-0.5">
                <svg class="w-5 h-5 text-slate-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                        clip-rule="evenodd" />
                </svg>
                GitHub
            </a>
        </div>
    </div>
</div>
