<div class="min-h-screen bg-slate-50 dark:bg-slate-900 pb-24 transition-colors duration-500">

    <div id="ios-pwa-banner"
        class="hidden bg-emerald-50 dark:bg-emerald-900/40 border-b border-emerald-200 dark:border-emerald-800 p-4 transition-all">
        <div class="max-w-3xl mx-auto flex items-start gap-3">
            <div class="bg-emerald-100 dark:bg-emerald-800 p-2 rounded-full shrink-0 mt-0.5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-emerald-800 dark:text-emerald-300">Pengguna iPhone/iPad?</p>
                <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-1">Agar notifikasi pesanan bisa muncul,
                    tekan ikon <span
                        class="font-black bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded shadow-sm border border-emerald-200 mx-0.5">Share</span>
                    di bawah, lalu pilih <span
                        class="font-black bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded shadow-sm border border-emerald-200 mx-0.5">Add
                        to Home Screen</span>.</p>
            </div>
            <button onclick="document.getElementById('ios-pwa-banner').style.display='none'"
                class="text-emerald-500 hover:text-emerald-700 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    <div
        class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-800/50 pt-4 pb-4 shadow-sm">
        <div class="max-w-3xl mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('customer.menu') }}"
                    class="p-2 -ml-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </a>
                <div>
                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-0.5">
                        {{ $table->table_number }}</p>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white line-clamp-1">Tagihan Meja 🧾</h1>
                </div>
            </div>

            <button x-data="{
                isDark: false,
                init() {
                    this.isDark = localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    this.applyTheme();
                    document.addEventListener('livewire:navigated', () => { this.applyTheme(); });
                },
                applyTheme() {
                    if (this.isDark) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); }
                },
                toggleTheme() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                    this.applyTheme();
                }
            }" @click="toggleTheme()"
                class="p-2 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors focus:outline-none">
                <svg x-show="!isDark" x-cloak class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg x-show="isDark" x-cloak class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 pt-6 space-y-6">

        @if (!$orders->isEmpty() && $orders->where('payment_status', 'unpaid')->count() > 0)
            <div
                class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-2xl p-4 flex gap-3 shadow-sm animate-fade-in-up">
                <div class="bg-amber-100 dark:bg-amber-800 p-2 rounded-full shrink-0 h-fit mt-0.5">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-amber-800 dark:text-amber-300">Pastikan Volume Anda Menyala!</p>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mt-1 leading-relaxed">Matikan mode senyap
                        (Silent/Mute) dan perbesar volume HP Anda agar Anda dapat mendengar panggilan saat pesanan telah
                        siap diambil.</p>
                </div>
            </div>
        @endif

        @if ($orders->isEmpty())
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl p-8 text-center border border-dashed border-slate-300 dark:border-slate-700">
                <div
                    class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Belum Ada Pesanan</h3>
                <p class="text-sm text-slate-500 mt-2 mb-6">Kamu belum memesan apapun. Yuk, lihat menu kami!</p>
                <a href="{{ route('customer.menu') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-500/30">
                    Lihat Menu Sekarang
                </a>
            </div>
        @else
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 dark:bg-emerald-900/20 rounded-full -mr-10 -mt-10 blur-2xl">
                </div>

                <div class="flex items-center justify-between mb-4">
                    <p class="text-slate-500 dark:text-slate-400 font-bold text-sm">Total Keseluruhan</p>

                    @if ($orders->where('payment_status', 'unpaid')->count() > 0)
                        <span
                            class="text-[10px] font-bold px-2 py-1 bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400 rounded-md uppercase">Belum
                            Lunas</span>
                    @else
                        <span
                            class="text-[10px] font-bold px-2 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400 rounded-md uppercase">Semua
                            Lunas</span>
                    @endif
                </div>

                <h2 class="text-4xl font-black text-slate-900 dark:text-white mb-6">Rp
                    {{ number_format($totalBill, 0, ',', '.') }}</h2>

                <div
                    class="flex flex-col gap-2 pt-4 border-t border-slate-100 dark:border-slate-700/50 text-sm font-medium">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Total Harga Menu</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($taxPercentage > 0)
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Pajak Restoran ({{ $taxPercentage }}%)</span>
                            <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if (count($personDetails) > 0)
                <div
                    class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Patungan (Split Bill)
                        </h3>
                    </div>

                    <ul class="space-y-4">
                        @foreach ($personDetails as $name => $details)
                            <li class="border-b border-slate-100 dark:border-slate-700/50 pb-4 last:border-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase mt-0.5">
                                            {{ substr($name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span
                                                class="font-bold text-sm text-slate-700 dark:text-slate-300">{{ $name }}</span>
                                            <div
                                                class="flex flex-col mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                                <div class="flex gap-4">
                                                    <span>Harga Menu:</span>
                                                    <span>Rp
                                                        {{ number_format($details['subtotal'], 0, ',', '.') }}</span>
                                                </div>
                                                @if ($taxPercentage > 0)
                                                    <div class="flex gap-4">
                                                        <span>Pajak ({{ $taxPercentage }}%):</span>
                                                        <span>Rp
                                                            {{ number_format($details['tax'], 0, ',', '.') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-slate-900 dark:text-white">Rp
                                            {{ number_format($details['total'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center justify-between pt-2">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Riwayat Pemesanan</h2>
                <span
                    class="text-xs font-semibold px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live
                </span>
            </div>

            <div class="space-y-5">
                @foreach ($orders as $order)
                    <div
                        class="bg-white dark:bg-slate-800 rounded-3xl border {{ $order->payment_status === 'paid' ? 'border-emerald-200 dark:border-emerald-800/50' : 'border-slate-200 dark:border-slate-700' }} overflow-hidden shadow-sm">

                        <div
                            class="px-5 py-3 border-b {{ $order->payment_status === 'paid' ? 'border-emerald-100 dark:border-emerald-800/30 bg-emerald-50/30 dark:bg-emerald-900/10' : 'border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/30' }} flex justify-between items-center">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ $order->order_number }}
                                    </p>
                                    @if ($order->payment_status === 'paid')
                                        <span
                                            class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400 text-[8px] font-black uppercase tracking-wide">LUNAS</span>
                                    @else
                                        <span
                                            class="px-1.5 py-0.5 rounded bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400 text-[8px] font-black uppercase tracking-wide">BELUM
                                            LUNAS</span>
                                    @endif
                                </div>
                                <p class="text-xs font-bold text-slate-600 dark:text-slate-300">
                                    {{ $order->created_at->format('H:i') }} WIB</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Nota
                                </p>
                                <span class="text-sm font-black text-slate-900 dark:text-white">Rp
                                    {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="p-5 space-y-4">
                            @foreach ($order->items as $item)
                                @php
                                    $itemPriceAcc = $item->price_at_order;
                                    foreach ($item->selectedAddons as $addon) {
                                        $itemPriceAcc += $addon->addon_price;
                                    }
                                    $rowTotal = $itemPriceAcc * $item->qty;
                                @endphp

                                <div class="flex items-start gap-4">
                                    <div
                                        class="mt-2.5 w-2 h-2 rounded-full shrink-0
                                        @if ($item->status == 'pending') bg-slate-300
                                        @elseif($item->status == 'cooking') bg-amber-400 animate-pulse
                                        @else bg-emerald-500 @endif
                                    ">
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start mb-1">
                                            <div class="flex-1 pr-2">
                                                <h4
                                                    class="font-bold text-slate-900 dark:text-white text-sm leading-tight">
                                                    <span
                                                        class="text-emerald-600 dark:text-emerald-400 mr-1">{{ $item->qty }}x</span>
                                                    {{ $item->product->name }}
                                                </h4>
                                                <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                                    @ Rp {{ number_format($itemPriceAcc, 0, ',', '.') }}
                                                </div>
                                            </div>

                                            <div class="flex flex-col items-end gap-1.5 shrink-0">
                                                <span class="text-sm font-black text-slate-900 dark:text-white">
                                                    Rp {{ number_format($rowTotal, 0, ',', '.') }}
                                                </span>
                                                <span
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-md whitespace-nowrap
                                                    @if ($item->status == 'pending') bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400
                                                    @elseif($item->status == 'cooking') bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400
                                                    @else bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 @endif
                                                ">
                                                    @if ($item->status == 'pending')
                                                        Menunggu
                                                    @elseif($item->status == 'cooking')
                                                        Dimasak
                                                    @else
                                                        Selesai
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        @if ($item->selectedAddons->count() > 0)
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach ($item->selectedAddons as $addon)
                                                    <span
                                                        class="text-[10px] font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                                        + {{ $addon->addon_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($item->notes)
                                            <div class="mt-2">
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 italic">
                                                    Catatan: "{{ $item->notes }}"</p>
                                            </div>
                                        @endif

                                        <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-wide">
                                            Milik: <span
                                                class="text-slate-700 dark:text-slate-300">{{ $item->customer->display_name ?? 'Tamu' }}</span>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-6">
                <a href="{{ route('customer.menu') }}"
                    class="w-full py-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 font-black rounded-2xl flex items-center justify-center gap-2 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                    Tambah Pesanan
                </a>
                <p class="text-center text-sm text-slate-500 dark:text-slate-400 mt-2">
                    Jika ingin merubah pesanan yang sudah ada, silahkan hubungi kasir atau pelayan kami ya!
                </p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {

            const isIos = () => {
                const userAgent = window.navigator.userAgent.toLowerCase();
                return /iphone|ipad|ipod/.test(userAgent);
            }
            const isStandalone = () => {
                return ('standalone' in window.navigator) && (window.navigator.standalone);
            }
            if (isIos() && !isStandalone()) {
                document.getElementById('ios-pwa-banner').classList.remove('hidden');
            }

            if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !==
                "denied") {
                Notification.requestPermission();
            }
        });
    </script>
</div>
