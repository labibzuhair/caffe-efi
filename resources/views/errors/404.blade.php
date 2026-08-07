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
        // Abaikan jika koneksi database terputus
    }
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - {{ $storeName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        body {
            overflow: hidden;
        }
    </style>
</head>

<body
    class="bg-slate-50 dark:bg-slate-900 min-h-screen flex items-center justify-center p-4 transition-colors duration-500">

    <div class="fixed top-[-10%] left-[-10%] w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-96 h-96 bg-teal-500/20 rounded-full blur-3xl"></div>

    <div class="relative max-w-lg w-full text-center z-10">

        <div class="animate-float mb-8 inline-block">
            <div
                class="relative w-48 h-48 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-emerald-500/10 border-4 border-emerald-50 dark:border-slate-700 overflow-hidden">
                @if ($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $storeName }}"
                        class="w-32 h-32 object-contain drop-shadow-md">
                @else
                    <span class="text-7xl">☕</span>
                @endif

                <div class="absolute -bottom-2 -right-4 text-5xl bg-slate-50 dark:bg-slate-900 rounded-full p-2 z-20">
                    ❓
                </div>
            </div>
        </div>

        <h1 class="text-7xl font-black text-slate-900 dark:text-white mb-2 tracking-tighter drop-shadow-sm">404</h1>
        <h2 class="text-2xl font-extrabold text-slate-700 dark:text-slate-300 mb-4">Waduh! Kopinya Tumpah...</h2>
        <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed font-medium">
            Halaman atau menu di <b>{{ $storeName }}</b> yang Anda cari sepertinya sedang istirahat. Mari kembali ke
            area yang aman.
        </p>

        <a href="{{ url('/') }}"
            class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-300 bg-emerald-600 rounded-full shadow-lg shadow-emerald-500/30 hover:bg-emerald-500 hover:-translate-y-1 hover:shadow-emerald-500/50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-emerald-600 dark:focus:ring-offset-slate-900 group">
            <svg class="w-5 h-5 mr-2 -ml-1 group-hover:-translate-x-1 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Kembali ke Beranda
        </a>
    </div>
</body>

</html>
