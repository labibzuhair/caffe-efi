@php
    $setting = \App\Models\Setting::first();
    $storeName = $setting->store_name ?? 'CaffePOS';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? $storeName . ' - Sistem Pemesanan Mandiri' }}</title>

    <meta property="og:title" content="{{ $storeName }} - Menu Digital">
    <meta property="og:description"
        content="Pesan makanan dan minuman lezat tanpa antri langsung dari meja Anda di {{ $storeName }}.">
    @if ($setting && $setting->seo_thumbnail)
        <meta property="og:image" content="{{ asset('storage/' . $setting->seo_thumbnail) }}">
    @endif

    @if ($setting && $setting->logo)
        <link rel="icon" href="{{ asset('storage/' . $setting->logo) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        function applyTheme() {
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        // Jalankan saat load pertama
        applyTheme();

        // Jalankan ulang setiap kali pindah halaman via wire:navigate
        document.addEventListener('livewire:navigated', () => {
            applyTheme();
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body
    class="antialiased font-sans bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 transition-colors duration-500 selection:bg-emerald-500 selection:text-white">

    {{ $slot }}

    @php
        $customerId = session('customer_id');
        $tableSessionId = null;
        if ($customerId) {
            $customer = \App\Models\SessionCustomer::find($customerId);
            $tableSessionId = $customer ? $customer->table_session_id : null;
        }
    @endphp

    @if ($tableSessionId)
        <audio id="customer-bell-audio" src="{{ asset('audio/customer-bell.mp3') }}" loop preload="auto"></audio>

        <div x-data="{
            showCallAlert: false,
            callMessage: '',
            vibrateInterval: null,
            audioUnlocked: false,

            // Ambil referensi audio langsung dari elemen HTML di atas
            get bellAudio() {
                return document.getElementById('customer-bell-audio');
            },

            init() {
                // 2. Trik Bypass Audio iOS
                const unlockAudio = () => {
                    if (this.audioUnlocked) return;
                    let audioEl = this.bellAudio;
                    if (audioEl) {
                        audioEl.play().then(() => {
                            audioEl.pause();
                            audioEl.currentTime = 0;
                            this.audioUnlocked = true;
                            document.removeEventListener('click', unlockAudio);
                            document.removeEventListener('touchstart', unlockAudio);
                        }).catch(e => {});
                    }
                };

                document.addEventListener('click', unlockAudio);
                document.addEventListener('touchstart', unlockAudio);

                // 3. PASANG TELINGA PUSHER
                if (window.Echo) {
                    // Pastikan keluar dari channel lama dulu jika ada sisa-sisa
                    window.Echo.leave('customer-table-{{ $tableSessionId }}');

                    window.Echo.channel('customer-table-{{ $tableSessionId }}')
                        .listen('.App\\Events\\CallCustomer', (e) => {

                            this.startAlert(e.message);

                            // Tembak Web Push Notification jika HP sedang diminimize
                            if ('Notification' in window && Notification.permission === 'granted' && document.hidden) {
                                new Notification('🔔 Pesanan Anda Siap!', {
                                    body: e.message,
                                    icon: '{{ asset('storage/' . ($setting->logo ?? '')) }}',
                                    vibrate: [200, 100, 200, 100, 500],
                                    tag: 'caffepos-pickup'
                                });
                            }
                        });
                }
            },

            // 4. FUNGSI PEMBERSIH (GARBAGE COLLECTOR) SAAT PINDAH HALAMAN
            destroy() {
                if (window.Echo) {
                    // Copot telinga Echo agar tidak dobel saat pindah halaman
                    window.Echo.leave('customer-table-{{ $tableSessionId }}');
                }
                this.stopAlert();
            },

            startAlert(msg) {
                this.callMessage = msg;
                this.showCallAlert = true;

                let audioEl = this.bellAudio;
                if (audioEl) {
                    audioEl.currentTime = 0;
                    audioEl.play().catch(e => console.warn('Audio diproteksi'));
                }

                if (navigator.vibrate) {
                    navigator.vibrate([200, 100, 200, 100, 500]);
                    this.vibrateInterval = setInterval(() => {
                        navigator.vibrate([200, 100, 200, 100, 500]);
                    }, 2500);
                }
            },

            stopAlert() {
                this.showCallAlert = false;

                let audioEl = this.bellAudio;
                if (audioEl) {
                    audioEl.pause();
                    audioEl.currentTime = 0;
                }

                if (this.vibrateInterval) {
                    clearInterval(this.vibrateInterval);
                    this.vibrateInterval = null;
                }
                if (navigator.vibrate) { navigator.vibrate(0); }
            }
        }">

            <div x-show="showCallAlert" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/90 backdrop-blur-sm">

                <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                    class="bg-white dark:bg-slate-800 rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl flex flex-col items-center text-center border-4 border-emerald-500">

                    <div
                        class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/50 rounded-full flex items-center justify-center mb-4 animate-bounce">
                        <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2 animate-pulse">Pesanan Siap!</h2>
                    <p class="text-slate-600 dark:text-slate-300 font-medium mb-6" x-text="callMessage"></p>
                    <button @click="stopAlert()"
                        class="w-full py-4 bg-emerald-600 text-white font-black text-lg rounded-xl hover:bg-emerald-500 transition-colors shadow-lg shadow-emerald-500/30 flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Oke, Saya Ambil
                    </button>
                </div>
            </div>
        </div>
    @endif
</body>

</html>
