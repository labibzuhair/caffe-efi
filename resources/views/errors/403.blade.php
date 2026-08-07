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
    <title>Akses Ditolak - {{ $storeName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>

<body
    class="bg-slate-50 dark:bg-slate-900 min-h-screen flex items-center justify-center p-4 transition-colors duration-500">

    <div class="fixed top-[-10%] right-[-10%] w-96 h-96 bg-red-500/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-lg w-full text-center z-10">

        <div class="animate-float mb-8 inline-block">
            <div
                class="relative w-48 h-48 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-red-500/10 border-4 border-red-50 dark:border-slate-700 overflow-hidden">
                @if ($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $storeName }}"
                        class="w-32 h-32 object-contain drop-shadow-md grayscale opacity-80">
                @else
                    <span class="text-7xl">🛑</span>
                @endif

                <div class="absolute -bottom-2 -right-4 text-5xl bg-slate-50 dark:bg-slate-900 rounded-full p-2 z-20">
                    👮
                </div>
            </div>
        </div>

        <h1 class="text-7xl font-black text-slate-900 dark:text-white mb-2 tracking-tighter">403</h1>
        <h2 class="text-2xl font-extrabold text-slate-700 dark:text-slate-300 mb-4">Akses Masuk Ditolak!</h2>
        <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed font-medium">
            Maaf, area <b>{{ $storeName }}</b> ini khusus untuk staf internal. Silakan kembali ke meja Anda untuk
            melanjutkan pemesanan.
        </p>

        <a href="{{ url('/') }}"
            class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-300 bg-slate-800 dark:bg-slate-700 rounded-full shadow-lg shadow-slate-800/30 hover:bg-slate-700 dark:hover:bg-slate-600 hover:-translate-y-1 focus:ring-4 focus:ring-offset-2 focus:ring-slate-800">
            Kembali ke Halaman Depan
        </a>
    </div>
</body>

</html>
