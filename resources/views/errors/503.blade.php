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
    <title>Sedang Pemeliharaan - {{ $storeName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes bounce-slow {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .animate-bounce-slow {
            animation: bounce-slow 3s infinite;
        }
    </style>
</head>

<body
    class="bg-slate-50 dark:bg-slate-900 min-h-screen flex items-center justify-center p-4 transition-colors duration-500">

    <div class="fixed top-[20%] right-[20%] w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-lg w-full text-center z-10">

        <div class="animate-bounce-slow mb-8 inline-block">
            <div
                class="relative w-48 h-48 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-orange-500/10 border-4 border-orange-50 dark:border-slate-700 overflow-hidden">
                @if ($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $storeName }}"
                        class="w-32 h-32 object-contain drop-shadow-md">
                @else
                    <span class="text-7xl">🏪</span>
                @endif

                <div class="absolute -bottom-2 -right-4 text-5xl bg-slate-50 dark:bg-slate-900 rounded-full p-2 z-20">
                    🚧
                </div>
            </div>
        </div>

        <h1 class="text-7xl font-black text-slate-900 dark:text-white mb-2 tracking-tighter">503</h1>
        <h2 class="text-2xl font-extrabold text-slate-700 dark:text-slate-300 mb-4">Kafe Sedang Diperbarui</h2>
        <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed font-medium">
            Kami sedang melakukan sedikit bersih-bersih dan peningkatan sistem di <b>{{ $storeName }}</b>. Kami akan
            segera kembali melayani Anda dalam beberapa menit!
        </p>

        <button onclick="window.location.reload()"
            class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-300 bg-orange-500 rounded-full shadow-lg shadow-orange-500/30 hover:bg-orange-400 hover:-translate-y-1 focus:ring-4 focus:ring-offset-2 focus:ring-orange-500 dark:focus:ring-offset-slate-900">
            Cek Kembali Sekarang
        </button>
    </div>
</body>

</html>
