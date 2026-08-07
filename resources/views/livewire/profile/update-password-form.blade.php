<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
    }
}; ?>

<section
    class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 sm:p-8">
    <header class="mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">
            Ubah Password
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Pastikan akun Anda aman dengan menggunakan password acak yang panjang.
        </p>
    </header>

    <form wire:submit="updatePassword" class="space-y-6">
        <div>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password Saat Ini</label>
            <input wire:model="current_password" type="password"
                class="w-full sm:w-2/3 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500">
            @error('current_password')
                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password Baru</label>
            <input wire:model="password" type="password"
                class="w-full sm:w-2/3 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500">
            @error('password')
                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password
                Baru</label>
            <input wire:model="password_confirmation" type="password"
                class="w-full sm:w-2/3 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500">
            @error('password_confirmation')
                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="pt-2 flex items-center gap-4">
            <button type="submit"
                class="bg-slate-800 hover:bg-slate-700 dark:bg-slate-100 dark:hover:bg-slate-200 dark:text-slate-800 text-white font-bold py-2.5 px-6 rounded-xl transition-colors shadow-sm">
                Perbarui Password
            </button>
            <x-action-message class="text-sm font-bold text-emerald-600 dark:text-emerald-400" on="password-updated">
                Password diubah.
            </x-action-message>
        </div>
    </form>
</section>
