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

    <title>{{ $storeName }} - Login Staf</title>

    @if ($setting && $setting->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $setting->logo) }}">
    @else
        <link rel="icon"
            href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>☕</text></svg>">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <script>
        function applyTheme() {
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }

        applyTheme();

        document.addEventListener('livewire:navigated', () => {
            applyTheme();
        });
    </script>
    <style>
        html.dark .theme-icon-light {
            display: none;
        }

        html.dark .theme-icon-dark {
            display: block;
        }

        html:not(.dark) .theme-icon-light {
            display: block;
        }

        html:not(.dark) .theme-icon-dark {
            display: none;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans text-slate-900 dark:text-slate-100 antialiased bg-slate-50 dark:bg-slate-900 transition-colors duration-500 selection:bg-emerald-500 selection:text-white">

    <div
        class="fixed top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] opacity-20 dark:opacity-10 pointer-events-none z-0">
        <div
            class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full blur-[100px] mix-blend-multiply dark:mix-blend-screen transform scale-y-50 -translate-y-1/2">
        </div>
    </div>

    <div class="absolute top-6 right-6 z-50">
        <button onclick="toggleTheme()"
            class="p-3 rounded-full bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/50 shadow-lg text-slate-500 dark:text-slate-400 hover:scale-110 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <svg class="theme-icon-dark w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                    clip-rule="evenodd"></path>
            </svg>
            <svg class="theme-icon-light w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
        </button>
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
        <div>
            <a href="/" wire:navigate class="flex flex-col items-center gap-3 group">
                @if ($setting && $setting->logo)
                    <img src="{{ asset('storage/' . $setting->logo) }}" alt="{{ $storeName }}"
                        class="h-16 w-auto object-contain drop-shadow-md group-hover:scale-105 transition-transform duration-300">
                    <span
                        class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white mt-2">{{ $storeName }}</span>
                @else
                    <div class="flex items-center gap-3">
                        <div
                            class="p-3 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-2xl shadow-lg shadow-emerald-500/30 group-hover:rotate-12 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M20 8h-3V4H3v13a4 4 0 004 4h9a4 4 0 004-4v-4h2a2 2 0 002-2V10a2 2 0 00-2-2zM7 4v14m5-14v14">
                                </path>
                            </svg>
                        </div>
                        <span
                            class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $storeName }}</span>
                    </div>
                @endif
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-8 px-8 py-10 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl shadow-2xl overflow-hidden sm:rounded-3xl border border-slate-200/50 dark:border-slate-700/50">
            {{ $slot }}
        </div>

        <div class="mt-8 text-sm text-slate-500 dark:text-slate-400 font-medium text-center">
            Sistem Kasir & Katalog Digital <br>
            <p class="text-center text-xs font-medium text-slate-400 mt-8">
                {{ $setting->footer_text ?? 'copy right reserved' }} &copy; {{ date('Y') }} {{ $storeName }}.
            </p>
        </div>
    </div>
</body>

</html>
