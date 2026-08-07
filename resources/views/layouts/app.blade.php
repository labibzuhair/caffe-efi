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

    <title>{{ $title ?? 'Dashboard - ' . $storeName }}</title>

    @if ($setting && $setting->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $setting->logo) }}">
    @else
        <link rel="icon"
            href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>☕</text></svg>">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script>
        function applyTheme() {
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        // Fungsi toggle global untuk konsistensi
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }

        // 1. Jalankan saat pertama kali halaman dimuat (Hard Refresh)
        applyTheme();

        // 2. Jalankan ulang setiap kali Livewire selesai berpindah halaman (SPA Navigate)
        document.addEventListener('livewire:navigated', () => {
            applyTheme();
        });
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body
    class="font-sans antialiased text-slate-900 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 transition-colors duration-500 selection:bg-emerald-500 selection:text-white">
    <div class="min-h-screen">
        <livewire:layout.navigation />

        @if (isset($header))
            <header
                class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md shadow-sm border-b border-slate-200/50 dark:border-slate-800/50 sticky top-16 z-40 transition-colors duration-500">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>

    <div class="mt-8 text-sm text-slate-500 dark:text-slate-400 font-medium text-center">
        Sistem Kasir & Katalog Digital <br>
        <p class="text-center text-xs font-medium text-slate-400 mt-8 mb-8">
            {{ $setting->footer_text ?? 'copy right reserved' }} &copy; {{ date('Y') }} {{ $storeName }}.
        </p>
    </div>

</body>

</html>
