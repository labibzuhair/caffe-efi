<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        Auth::user()->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Verifikasi Email</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
            Terima kasih telah bergabung! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik tautan
            yang baru saja kami kirimkan.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div
            class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">
                Tautan verifikasi baru telah dikirim ke alamat email Anda.
            </span>
        </div>
    @endif

    <div class="mt-8 flex flex-col gap-4">
        <button wire:click="sendVerification"
            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-slate-800 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/30">
            <span wire:loading.remove wire:target="sendVerification">Kirim Ulang Email</span>
            <span wire:loading wire:target="sendVerification">Mengirim...</span>
        </button>

        <button wire:click="logout" type="submit"
            class="text-sm font-medium text-slate-600 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors">
            Keluar (Log Out)
        </button>
    </div>
</div>
