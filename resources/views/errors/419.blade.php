@php
    $storeName = 'CaffePOS';
    $logo = null;
    try {
        $setting = \App\Models\Setting::first();
        if ($setting) {
            $storeName = $setting->store_name ?: 'CaffePOS';
            $logo = $setting->logo;
        }
    } catch (\Exception $e) {
    }
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir - {{ $storeName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes swing {

            0%,
            100% {
                transform: rotate(-10deg);
            }

            50% {
                transform: rotate(10deg);
            }
        }

        .animate-swing {
            animation: swing 3s ease-in-out infinite;
            transform-origin: top center;
        }
    </style>
</head>

<body
    class="bg-slate-50 dark:bg-slate-900 min-h-screen flex items-center justify-center p-4 transition-colors duration-500">

    <div class="fixed top-[10%] left-[30%] w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-lg w-full text-center z-10">

        <div class="animate-swing mb-8 inline-block">
            <div
                class="relative w-48 h-48 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-blue-500/10 border-4 border-blue-50 dark:border-slate-700 overflow-hidden">
                @if ($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $storeName }}"
                        class="w-32 h-32 object-contain drop-shadow-md opacity-70">
                @else
                    <span class="text-7xl">🕰️</span>
                @endif

                <div class="absolute -bottom-2 -right-4 text-5xl bg-slate-50 dark:bg-slate-900 rounded-full p-2 z-20">
                    ⌛
                </div>
            </div>
        </div>

        <h1 class="text-7xl font-black text-slate-900 dark:text-white mb-2 tracking-tighter">419</h1>
        <h2 class="text-2xl font-extrabold text-slate-700 dark:text-slate-300 mb-4">Sesi Anda Telah Berakhir</h2>
        <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed font-medium">
            Anda membiarkan halaman ini terbuka terlalu lama sehingga token keamanan kedaluwarsa. Jangan khawatir, cukup
            muat ulang halaman untuk melanjutkan.
        </p>

        <button onclick="window.location.reload()"
            class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-300 bg-blue-600 rounded-full shadow-lg shadow-blue-600/30 hover:bg-blue-500 hover:-translate-y-1 focus:ring-4 focus:ring-offset-2 focus:ring-blue-600 dark:focus:ring-offset-slate-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                </path>
            </svg>
            Muat Ulang Halaman
        </button>
    </div>
</body>

</html>
