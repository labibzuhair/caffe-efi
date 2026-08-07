<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section
    class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 sm:p-8">
    <header class="mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">
            Informasi Dasar
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Perbarui nama tampilan dan alamat email login Anda.
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="sm:col-span-2">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                <input wire:model="name" type="text"
                    class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                    required>
                @error('name')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alamat Email</label>
                <input wire:model="email" type="email"
                    class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                    required>
                @error('email')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <div
                        class="mt-3 p-4 bg-orange-50 dark:bg-orange-900/30 rounded-xl border border-orange-200 dark:border-orange-800/50">
                        <p class="text-sm text-orange-800 dark:text-orange-300">
                            Email Anda belum diverifikasi.
                            <button wire:click.prevent="sendVerification"
                                class="font-bold underline hover:text-orange-900 dark:hover:text-orange-200 focus:outline-none">
                                Klik di sini untuk mengirim ulang.
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                Tautan baru telah dikirim!
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="pt-2 flex items-center gap-4">
            <button type="submit"
                class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-6 rounded-xl transition-colors shadow-sm">
                Simpan Perubahan
            </button>
            <x-action-message class="text-sm font-bold text-emerald-600 dark:text-emerald-400" on="profile-updated">
                Berhasil disimpan.
            </x-action-message>
        </div>
    </form>
</section>
