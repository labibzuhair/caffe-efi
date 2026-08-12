<div>
    <div wire:loading wire:target="checkout" class="fixed inset-0 z-[100]">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm flex flex-col items-center justify-center">
            <div
                class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-2xl flex flex-col items-center transform scale-105 transition-transform">
                <svg class="animate-spin h-12 w-12 text-emerald-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-100" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <h3 class="text-xl font-black text-slate-900 dark:text-white">Memproses Pesanan...</h3>
                <p class="text-sm font-medium text-slate-500 mt-1">Jangan tutup halaman ini</p>
            </div>
        </div>
    </div>

    @if (count($cart) > 0)
        <button wire:click="$toggle('isOpen')" x-data="{ animate: false }"
            @add-to-cart.window="animate = true; setTimeout(() => animate = false, 300)"
            :class="animate ? 'scale-110 ring-4 ring-emerald-300 dark:ring-emerald-700' : 'hover:scale-105'"
            class="fixed bottom-6 right-6 z-50 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full p-4 shadow-2xl shadow-emerald-500/40 transition-all duration-300 flex items-center gap-3">
            <div class="relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <span
                    class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-emerald-600 transition-transform duration-300"
                    :class="animate ? 'scale-150' : 'scale-100'">
                    {{ $totalItems }}
                </span>
            </div>
            <span class="font-bold hidden sm:block">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </button>
    @endif

    <div x-data="{ open: @entangle('isOpen') }" x-show="open" class="fixed inset-0 z-50 overflow-hidden" style="display: none;"
        x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div x-show="open" @click="open = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div x-show="open" class="w-screen max-w-md transform"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">

                <div class="flex h-full flex-col bg-white dark:bg-slate-900 shadow-2xl">

                    <div
                        class="flex items-center justify-between p-6 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">Pesanan
                            Bersama</h2>
                        <button @click="open = false"
                            class="text-slate-400 hover:text-slate-500 bg-slate-100 dark:bg-slate-800 rounded-full p-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-50 dark:bg-slate-900/50">
                        @if (count($cart) === 0)
                            <div class="flex flex-col items-center justify-center h-full text-center opacity-50">
                                <svg class="w-20 h-20 mb-4 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <p class="text-lg font-bold text-slate-900 dark:text-white">Keranjang Kosong</p>
                            </div>
                        @else
                            <div
                                class="mb-5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 rounded-xl p-3 flex gap-3 animate-fade-in-up">
                                <div class="shrink-0 pt-0.5">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-blue-800 dark:text-blue-300">Live Shared Cart 🔄
                                    </p>
                                    <p class="text-[11px] text-blue-600 dark:text-blue-400 mt-0.5 leading-relaxed">
                                        Anda bisa melihat pesanan teman semeja Anda di sini secara real-time. Klik
                                        "Ubah" untuk menambahkan catatan atau alergi.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                @foreach ($cart as $customerId => $customerData)
                                    <div
                                        class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-100 dark:border-slate-700/50">
                                        <div
                                            class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100 dark:border-slate-700">
                                            <div
                                                class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xs font-black">
                                                {{ substr($customerData['name'] ?? '?', 0, 1) }}
                                            </div>
                                            <div class="flex-1 flex justify-between items-center">
                                                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200">Pesanan
                                                    {{ $customerData['name'] ?? 'Tamu' }}</h3>
                                                @php
                                                    $personTotal = 0;
                                                    foreach ($customerData['items'] as $item) {
                                                        $personTotal +=
                                                            ($item['final_price'] ?? 0) * ($item['qty'] ?? 1);
                                                    }
                                                @endphp
                                                <span
                                                    class="text-xs font-black text-emerald-600 dark:text-emerald-400">Rp
                                                    {{ number_format($personTotal, 0, ',', '.') }}</span>
                                            </div>
                                        </div>

                                        <ul class="space-y-4">
                                            @foreach ($customerData['items'] as $cartItemId => $item)
                                                <li class="flex items-start gap-3">
                                                    <div
                                                        class="h-12 w-12 shrink-0 rounded-lg bg-slate-100 dark:bg-slate-700 overflow-hidden flex items-center justify-center mt-1">
                                                        @if (!empty($item['image']))
                                                            <img src="{{ asset('storage/' . $item['image']) }}"
                                                                class="h-full w-full object-cover">
                                                        @else
                                                            <svg class="w-6 h-6 text-slate-300 dark:text-slate-500"
                                                                fill="currentColor" viewBox="0 0 24 24">
                                                                <path
                                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1">
                                                        <h4
                                                            class="text-sm font-bold text-slate-900 dark:text-white leading-tight line-clamp-1">
                                                            {{ $item['name'] ?? 'Menu' }}</h4>

                                                        <div class="mt-1 flex flex-col gap-0.5">
                                                            <div
                                                                class="flex justify-between text-[11px] text-slate-500 dark:text-slate-400">
                                                                <span>Harga Menu</span>
                                                                <span>Rp
                                                                    {{ number_format($item['base_price'] ?? 0, 0, ',', '.') }}</span>
                                                            </div>

                                                            @if (!empty($item['addons']))
                                                                @foreach ($item['addons'] as $addon)
                                                                    <div
                                                                        class="flex justify-between text-[11px] text-slate-500 dark:text-slate-400">
                                                                        <span>+ {{ $addon['name'] }}</span>
                                                                        <span>Rp
                                                                            {{ number_format($addon['price'] ?? 0, 0, ',', '.') }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>

                                                        @if (!empty($item['note']))
                                                            <div
                                                                class="mt-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded px-2 py-1 inline-block">
                                                                <p
                                                                    class="text-[10px] text-amber-700 dark:text-amber-400 italic">
                                                                    "{{ $item['note'] }}"</p>
                                                            </div>
                                                        @endif

                                                        <div
                                                            class="flex justify-between items-center mt-2 pt-2 border-t border-slate-100 dark:border-slate-700/50">
                                                            <span
                                                                class="text-[10px] font-bold text-slate-400 uppercase">Subtotal</span>
                                                            <p
                                                                class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                                                Rp
                                                                {{ number_format($item['final_price'] ?? 0, 0, ',', '.') }}
                                                            </p>
                                                        </div>

                                                        <div class="flex items-center gap-3 mt-3">
                                                            <div
                                                                class="flex items-center border border-slate-200 dark:border-slate-600 rounded-md overflow-hidden">
                                                                <button wire:click="decrease({{ $item['id'] }})"
                                                                    class="px-2 py-0.5 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200">-</button>
                                                                <span
                                                                    class="px-2 text-xs font-bold">{{ $item['qty'] ?? 1 }}</span>
                                                                <button wire:click="increase({{ $item['id'] }})"
                                                                    class="px-2 py-0.5 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200">+</button>
                                                            </div>

                                                            <button wire:click="editItem({{ $item['id'] }})"
                                                                class="text-[10px] px-2 py-1 rounded bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 font-bold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors border border-blue-200 dark:border-blue-800">
                                                                Ubah
                                                            </button>
                                                            <button wire:click="remove({{ $item['id'] }})"
                                                                class="text-[10px] font-bold text-red-500 hover:text-red-600 hover:underline">
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if (count($cart) > 0)
                        <div class="border-t border-slate-200 dark:border-slate-800 p-6 bg-white dark:bg-slate-900">
                            <div class="space-y-1.5 mb-4">
                                <div class="flex justify-between text-sm text-slate-500">
                                    <p>Subtotal Semua (Belum Pajak)</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-300">Rp
                                        {{ number_format($subtotal, 0, ',', '.') }}</p>
                                </div>
                                @if ($taxPercentage > 0)
                                    <div class="flex justify-between text-sm text-slate-500">
                                        <p>Estimasi Pajak ({{ $taxPercentage }}%)</p>
                                        <p class="font-semibold text-slate-700 dark:text-slate-300">Rp
                                            {{ number_format($taxAmount, 0, ',', '.') }}</p>
                                    </div>
                                @endif
                                <div
                                    class="flex justify-between text-lg font-black text-slate-900 dark:text-white pt-2 border-t border-slate-100 dark:border-slate-800">
                                    <p>Total Belanja Meja</p>
                                    <p class="text-emerald-600 dark:text-emerald-400">Rp
                                        {{ number_format($total, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <button wire:click="checkout" wire:loading.attr="disabled" wire:target="checkout"
                                class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-400 dark:disabled:bg-slate-700 disabled:cursor-not-allowed px-6 py-3.5 text-center font-bold text-white shadow-lg shadow-emerald-500/30 transition-all flex justify-center items-center gap-2">
                                <span wire:loading.remove wire:target="checkout">Pesan Sekarang &rarr;</span>
                                <span wire:loading wire:target="checkout">Mempersiapkan Nota...</span>
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
