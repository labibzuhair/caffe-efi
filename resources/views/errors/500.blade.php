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
    <title>Sistem Gangguan - {{ $storeName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .8;
                transform: scale(0.95);
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }
    </style>
</head>

<body
    class="bg-slate-50 dark:bg-slate-900 min-h-screen flex items-center justify-center p-4 transition-colors duration-500">

    <div class="fixed top-[20%] left-[20%] w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-lg w-full text-center z-10">

        <div class="animate-pulse-slow mb-8 inline-block">
            <div
                class="relative w-48 h-48 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-amber-500/10 border-4 border-amber-50 dark:border-slate-700 overflow-hidden">
                @if ($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $storeName }}"
                        class="w-32 h-32 object-contain drop-shadow-md sepia opacity-80">
                @else
                    <span class="text-7xl">⚙️</span>
                @endif

                <div class="absolute -bottom-2 -right-4 text-5xl bg-slate-50 dark:bg-slate-900 rounded-full p-2 z-20">
                    🔥
                </div>
            </div>
        </div>

        <h1 class="text-7xl font-black text-slate-900 dark:text-white mb-2 tracking-tighter">500</h1>
        <h2 class="text-2xl font-extrabold text-slate-700 dark:text-slate-300 mb-4">Mesin Sedang Diperbaiki...</h2>
        <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed font-medium">
            Terjadi sedikit gangguan pada sistem dapur <b>{{ $storeName }}</b>. Teknisi kami sedang berusaha
            memperbaikinya secepat mungkin!
        </p>

        <button onclick="window.location.reload()"
            class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-300 bg-amber-500 rounded-full shadow-lg shadow-amber-500/30 hover:bg-amber-400 hover:-translate-y-1 focus:ring-4 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-slate-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                </path>
            </svg>
            Coba Muat Ulang
        </button>
    </div>
</body>

</html>
