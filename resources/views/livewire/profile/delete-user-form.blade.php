<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="bg-red-50/50 dark:bg-red-900/10 rounded-3xl border border-red-200 dark:border-red-900/50 p-6 sm:p-8">
    <header class="mb-6">
        <h2 class="text-xl font-bold text-red-600 dark:text-red-400">
            Hapus Akun Permanen
        </h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Setelah akun Anda dihapus, semua data akan hilang secara permanen. Pastikan Anda telah mengunduh data yang
            diperlukan.
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-500 text-white font-bold py-2.5 px-6 rounded-xl transition-colors shadow-sm">
        Hapus Akun Saya
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-8 bg-white dark:bg-slate-800 rounded-3xl">

            <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-2">
                Apakah Anda yakin?
            </h2>

            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                Tindakan ini tidak dapat dibatalkan. Masukkan password Anda untuk mengonfirmasi penghapusan permanen
                akun.
            </p>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password Anda</label>
                <input wire:model="password" type="password"
                    class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-red-500 focus:ring-red-500"
                    placeholder="Masukkan password untuk konfirmasi...">
                @error('password')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white rounded-xl font-bold transition-colors shadow-sm">
                    Hapus Permanen
                </button>
            </div>
        </form>
    </x-modal>
</section>
