@php
    $setting = \App\Models\Setting::first();
    $storeName = $setting->store_name ?? 'CaffePOS';
    $socialMedia = $setting && $setting->social_media ? json_decode($setting->social_media, true) : [];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Katalog Menu - {{ $storeName }}</title>
    @if ($setting && $setting->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $setting->logo) }}">
    @else
        <link rel="icon"
            href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>☕</text></svg>">
    @endif
    <meta name="description"
        content="Katalog menu digital resmi dari {{ $storeName }}. {{ $setting->footer_text ?? 'Temukan sajian kopi dan hidangan terbaik kami.' }}">
    <meta name="keywords" content="cafe, resto, {{ $storeName }}, menu digital, point of sale, kopi, makanan">
    <meta name="author" content="{{ $storeName }}">

    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Katalog Menu - {{ $storeName }}">
    <meta property="og:description"
        content="Lihat menu lezat kami dan pesan langsung dari katalog digital {{ $storeName }}.">
    @if ($setting && $setting->seo_thumbnail)
        <meta property="og:image" content="{{ asset('storage/' . $setting->seo_thumbnail) }}">
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="antialiased font-sans bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 transition-colors duration-500 selection:bg-emerald-500 selection:text-white">

    <nav
        class="sticky top-0 z-50 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-800/50 transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:justify-between h-auto md:h-20 py-3 md:py-0 gap-3 md:gap-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 cursor-pointer"
                        onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                        @if ($setting && $setting->logo)
                            <img src="{{ asset('storage/' . $setting->logo) }}" alt="{{ $storeName }}"
                                class="h-8 sm:h-10 w-auto object-contain">
                            <span
                                class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white hidden sm:block">{{ $storeName }}</span>
                        @else
                            <div
                                class="p-2 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-2xl shadow-lg shadow-emerald-500/30 transform hover:rotate-12 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M20 8h-3V4H3v13a4 4 0 004 4h9a4 4 0 004-4v-4h2a2 2 0 002-2V10a2 2 0 00-2-2zM7 4v14m5-14v14">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $storeName }}</span>
                        @endif
                    </div>
                    <div class="md:hidden">
                        <button onclick="toggleTheme()"
                            class="p-2 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
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
                </div>
                <div
                    class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1 md:py-0 w-full md:w-auto md:justify-center">
                    @foreach ($categories as $category)
                        @if ($category->products->count() > 0)
                            <a href="#kategori-{{ $category->id }}"
                                class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100/50 dark:bg-slate-800/50 hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-white transition-all duration-300 border border-slate-200/50 dark:border-slate-700/50">
                                {{ $category->name }}
                            </a>
                        @endif
                    @endforeach
                </div>
                <div class="hidden md:flex items-center">
                    <button onclick="toggleTheme()"
                        class="p-2.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-300 hover:rotate-45 focus:outline-none focus:ring-2 focus:ring-emerald-500">
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
            </div>
        </div>
    </nav>

    <div class="relative overflow-hidden py-24 sm:py-32 bg-slate-50 dark:bg-slate-900 transition-colors duration-500">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] opacity-30 dark:opacity-10 pointer-events-none animate-pulse"
            style="animation-duration: 4s;">
            <div
                class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full blur-[100px] mix-blend-multiply dark:mix-blend-screen transform scale-y-50 -translate-y-1/2">
            </div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in-up">
            <span
                class="inline-block py-1.5 px-4 rounded-full bg-emerald-100/50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold tracking-widest uppercase mb-8 border border-emerald-200/50 dark:border-emerald-800/30 backdrop-blur-sm shadow-sm">
                Bahan Organik Pilihan
            </span>
            <h1
                class="text-5xl sm:text-7xl font-extrabold tracking-tight mb-6 text-slate-900 dark:text-white drop-shadow-sm">
                Selamat Datang di <br />
                <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-500">{{ $storeName }}</span>
            </h1>
            <p class="text-lg sm:text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-10 font-medium">
                Katalog digital resmi. Temukan keseimbangan sempurna dari hidangan artisan yang menggugah selera.
            </p>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 -mt-16 relative z-10">
        @forelse ($categories as $index => $category)
            @if ($category->products->count() > 0)
                <div id="kategori-{{ $category->id }}" class="mb-24 scroll-mt-28 animate-fade-in-up"
                    style="animation-delay: {{ $index * 150 }}ms;">
                    <div class="flex items-center mb-10 group">
                        <h2
                            class="text-3xl font-black text-slate-900 dark:text-white tracking-tight group-hover:text-emerald-500 transition-colors">
                            {{ $category->name }}</h2>
                        <div
                            class="ml-6 flex-grow h-px bg-gradient-to-r from-slate-300 dark:from-slate-700 to-transparent">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                        @foreach ($category->products as $product)
                            <div
                                class="group relative bg-white dark:bg-slate-800/80 rounded-3xl shadow-sm hover:shadow-2xl transition-all duration-500 border border-slate-200/60 dark:border-slate-700/50 flex flex-col overflow-hidden {{ $product->is_active ? 'hover:shadow-emerald-500/20 dark:hover:shadow-teal-900/30 hover:-translate-y-3' : 'opacity-80' }}">
                                <div
                                    class="absolute inset-0 bg-gradient-to-b from-white/40 to-transparent dark:from-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-20 pointer-events-none">
                                </div>
                                <div
                                    class="relative w-full aspect-[4/3] overflow-hidden bg-slate-100 dark:bg-slate-700/50">
                                    @if (!$product->is_active)
                                        <div
                                            class="absolute inset-0 bg-slate-900/40 z-10 flex items-center justify-center backdrop-blur-[2px]">
                                            <span
                                                class="px-4 py-2 bg-red-500 text-white font-black tracking-widest text-sm rounded-lg shadow-lg rotate-12 uppercase border-2 border-white/20">Habis</span>
                                        </div>
                                    @endif
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}"
                                            class="w-full h-full object-cover {{ $product->is_active ? 'group-hover:scale-125 group-hover:rotate-1' : 'grayscale opacity-70' }} transition-transform duration-1000 ease-out">
                                    @else
                                        <div
                                            class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-700 {{ $product->is_active ? 'group-hover:scale-110' : '' }} transition-transform duration-700 ease-in-out">
                                            <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-2 transform {{ $product->is_active ? 'group-hover:-translate-y-1' : '' }} transition-transform duration-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span
                                                class="text-xs font-semibold text-slate-400 dark:text-slate-500 tracking-widest uppercase">Tanpa
                                                Foto</span>
                                        </div>
                                    @endif
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-white via-white/10 to-transparent dark:from-slate-800 dark:via-slate-800/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                                    </div>
                                </div>
                                <div class="p-6 flex-grow flex flex-col justify-between relative z-10">
                                    <div>
                                        <h3
                                            class="text-lg font-bold leading-tight transition-colors mb-2 {{ $product->is_active ? 'text-slate-900 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400' : 'text-slate-500 dark:text-slate-400 line-through' }}">
                                            {{ $product->name }}</h3>
                                        <p
                                            class="text-sm text-slate-500 dark:text-slate-400 mb-6 line-clamp-2 leading-relaxed">
                                            {{ $product->description ?? 'Dibuat dengan presisi dan bahan berkualitas tinggi untuk memanjakan lidah Anda.' }}
                                        </p>
                                    </div>
                                    <div
                                        class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-700/50">
                                        @if ($product->is_active)
                                            <span
                                                class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse"></span>
                                                Tersedia
                                            </span>
                                        @else
                                            <span
                                                class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-red-500 dark:text-red-400 flex items-center gap-1.5">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)]"></span>
                                                Habis
                                            </span>
                                        @endif
                                        <span
                                            class="text-base sm:text-lg font-black {{ $product->is_active ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500' }}">Rp
                                            {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <div
                class="text-center py-32 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm rounded-3xl border border-slate-200 dark:border-slate-700/50 shadow-sm animate-fade-in-up">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-emerald-50 dark:bg-slate-900 mb-6 border border-emerald-100 dark:border-slate-800">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Belum ada menu</h3>
                <p class="mt-3 text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Katalog menu sedang disiapkan.
                    Silakan kembali lagi nanti.</p>
            </div>
        @endforelse
    </main>

    <footer
        class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 transition-colors duration-500 mt-10">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        @if ($setting && $setting->logo)
                            <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" class="h-8 w-auto">
                        @else
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M20 8h-3V4H3v13a4 4 0 004 4h9a4 4 0 004-4v-4h2a2 2 0 002-2V10a2 2 0 00-2-2zM7 4v14m5-14v14">
                                </path>
                            </svg>
                        @endif
                        <span
                            class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $storeName }}</span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                        {{ $setting->address ?? 'Alamat belum diatur. Silakan perbarui di dasbor pengaturan toko.' }}
                    </p>
                    <div class="space-y-2 text-sm text-slate-600 dark:text-slate-300 font-medium">
                        @if ($setting && $setting->contact_phone)
                            <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg> {{ $setting->contact_phone }}</div>
                        @endif
                        @if ($setting && $setting->contact_email)
                            <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg> {{ $setting->contact_email }}</div>
                        @endif
                    </div>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4">Kategori Menu</h3>
                    <ul class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                        @foreach ($categories->take(4) as $cat)
                            <li><a href="#kategori-{{ $cat->id }}"
                                    class="hover:text-emerald-500 transition-colors">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4">Ikuti Kami</h3>
                    <div class="flex gap-4">
                        @if (!empty($socialMedia['instagram']))
                            <a href="{{ Str::startsWith($socialMedia['instagram'], 'http') ? $socialMedia['instagram'] : 'https://instagram.com/' . $socialMedia['instagram'] }}"
                                target="_blank"
                                class="p-2 bg-slate-100 dark:bg-slate-800 rounded-full text-slate-500 hover:text-pink-500 hover:bg-pink-50 dark:hover:bg-pink-900/30 transition-colors"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 1.76-6.965 6.965-.058 1.28-.072 1.688-.072 4.947s.014 3.667.072 4.947c.184 5.206 2.603 6.765 6.965 6.965 1.28.058 1.688.072 4.947.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-1.76 6.966-6.965.058-1.28.073-1.687.073-4.947s-.015-3.667-.072-4.947c-.185-5.208-2.614-6.765-6.966-6.965-1.28-.058-1.689-.072-4.948-.072zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg>
                            </a>
                        @endif
                        @if (!empty($socialMedia['tiktok']))
                            <a href="{{ Str::startsWith($socialMedia['tiktok'], 'http') ? $socialMedia['tiktok'] : 'https://tiktok.com/@' . $socialMedia['tiktok'] }}"
                                target="_blank"
                                class="p-2 bg-slate-100 dark:bg-slate-800 rounded-full text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 2.78-1.15 5.54-3.33 7.65-2.22 2.15-5.25 3.32-8.32 3.12-3.15-.2-6.16-1.85-8.15-4.29-1.92-2.35-2.81-5.46-2.5-8.52.3-3.11 1.88-6.01 4.31-8.03 2.37-1.97 5.5-2.92 8.57-2.61v4.06c-1.74-.2-3.52-.02-5.11.75-1.52.74-2.73 2.04-3.37 3.58-.66 1.57-.76 3.35-.29 4.98.47 1.66 1.59 3.08 3.12 3.86 1.5.76 3.26 1 4.88.66 1.57-.31 2.96-1.25 3.88-2.58.89-1.28 1.34-2.82 1.34-4.38.01-6.19.01-12.38.01-18.57z" />
                                </svg></a>
                        @endif
                        @if (!empty($socialMedia['facebook']))
                            <a href="{{ Str::startsWith($socialMedia['facebook'], 'http') ? $socialMedia['facebook'] : 'https://facebook.com/' . $socialMedia['facebook'] }}"
                                target="_blank"
                                class="p-2 bg-slate-100 dark:bg-slate-800 rounded-full text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                                </svg></a>
                        @endif
                        @if (!empty($socialMedia['youtube']))
                            <a href="{{ Str::startsWith($socialMedia['youtube'], 'http') ? $socialMedia['youtube'] : 'https://youtube.com/' . $socialMedia['youtube'] }}"
                                target="_blank"
                                class="p-2 bg-slate-100 dark:bg-slate-800 rounded-full text-slate-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"><svg
                                    class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg></a>
                        @endif
                    </div>
                </div>
            </div>
            <div
                class="pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-center text-xs font-medium text-slate-400 mt-8">
                    {{ $setting->footer_text ?? 'copy right reserved' }} &copy; {{ date('Y') }}
                    {{ $storeName }}.
                </p>
                <a href="{{ route('login') }}"
                    class="text-xs text-slate-400 hover:text-emerald-500 transition-colors opacity-50 hover:opacity-100 flex items-center gap-1"><svg
                        class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg> Akses Staf Kafe</a>
            </div>
        </div>
    </footer>
</body>

</html>
