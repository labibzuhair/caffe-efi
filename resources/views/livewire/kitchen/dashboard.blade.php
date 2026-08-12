<div class="py-8 bg-slate-100 dark:bg-slate-900 min-h-screen">
    <div class="max-w-[96rem] mx-auto px-4 sm:px-6 lg:px-8">

        @if (session()->has('success_call'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="fixed top-6 right-6 z-50 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 animate-fade-in-up">
                <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
                <span class="font-bold">{{ session('success_call') }}</span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                    👨‍🍳 Kitchen Display System
                    <span class="relative flex h-4 w-4">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500"></span>
                    </span>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">Sistem prioritas otomatis. Terlama
                    diurutkan pertama (FIFO).</p>
            </div>

            <div
                class="bg-white dark:bg-slate-800 px-5 py-2.5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 font-black text-xl text-slate-700 dark:text-slate-300 w-full sm:w-auto text-center sm:text-left">
                <span id="clock"></span>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 mb-8 flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-64">
                <select wire:model.live="filterCategory"
                    class="block w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white font-bold focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition-colors">
                    <option value="">🍔 Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live="searchProduct" type="text"
                    class="block w-full pl-12 pr-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition-colors font-medium"
                    placeholder="Cari pesanan spesifik...">
            </div>

            <div class="w-full md:w-64 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <input wire:model.live="searchTable" type="text"
                    class="block w-full pl-12 pr-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition-colors font-medium"
                    placeholder="Cari Meja...">
            </div>
        </div>

        @if ($orders->count() === 0)
            <div
                class="flex flex-col items-center justify-center py-32 bg-white dark:bg-slate-800 rounded-3xl border-2 border-dashed border-slate-300 dark:border-slate-700">
                <svg class="w-24 h-24 text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-slate-500 dark:text-slate-400">Dapur Sedang Kosong</h2>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                @foreach ($orders as $order)
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-md border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col h-full animate-fade-in-up">

                        @php
                            $oldestItem = $order->items->sortBy('created_at')->first();
                            $hasPending = $order->items->contains('status', 'pending');

                            // Hitung yang sudah ready (HIJAU) untuk dipanggil ke Kasir
                            $readyCount = $order->items->where('status', 'ready_to_serve')->count();

                            // Ringkasan semua item yang sedang TAMPIL di dapur
                            $visibleItems = $order->items->whereIn('status', ['pending', 'cooking', 'ready_to_serve']);
                            $summaryCounts = [];

                            foreach ($visibleItems as $vItem) {
                                $prodName = $vItem->product->name;
                                if (!isset($summaryCounts[$prodName])) {
                                    $summaryCounts[$prodName] = 0;
                                }
                                $summaryCounts[$prodName] += $vItem->qty;
                            }
                        @endphp

                        <div
                            class="bg-slate-800 text-white p-4 flex justify-between items-center border-b-4 {{ $hasPending ? 'border-red-500' : 'border-amber-500' }}">
                            <div>
                                <h3 class="text-2xl font-black">{{ $order->session->table->table_number ?? 'Takeaway' }}
                                </h3>
                                <p class="text-[10px] text-slate-300 opacity-80 tracking-widest uppercase">
                                    {{ $order->order_number }}</p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="bg-slate-700/80 px-2.5 py-1.5 rounded-lg text-sm font-black border border-slate-600 shadow-inner flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $oldestItem ? $oldestItem->created_at->diffForHumans(null, true, true) : 'Baru saja' }}
                                </span>
                            </div>
                        </div>

                        @if (count($summaryCounts) > 0)
                            <div
                                class="bg-indigo-50 dark:bg-indigo-900/20 p-3 border-b border-indigo-100 dark:border-indigo-800">
                                <p
                                    class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                    Ringkasan Meja:
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($summaryCounts as $name => $qty)
                                        <span
                                            class="inline-flex items-center gap-1 bg-white dark:bg-slate-800 px-2 py-1 rounded border border-indigo-200 dark:border-indigo-700 shadow-sm text-xs font-bold text-slate-700 dark:text-slate-300">
                                            <span
                                                class="text-indigo-600 dark:text-indigo-400 text-sm">{{ $qty }}x</span>
                                            {{ $name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="p-3 flex-grow bg-slate-50 dark:bg-slate-900/50">
                            <ul class="space-y-3">
                                @foreach ($order->items as $item)
                                    @if (in_array($item->status, ['pending', 'cooking', 'ready_to_serve']))
                                        <li
                                            class="bg-white dark:bg-slate-800 p-3.5 rounded-xl shadow-sm border {{ $item->status == 'cooking' ? 'border-amber-400 dark:border-amber-500/50' : ($item->status == 'ready_to_serve' ? 'border-emerald-400 bg-emerald-50/50 dark:bg-emerald-900/10' : 'border-slate-200 dark:border-slate-700') }} flex flex-col gap-2 transition-colors">

                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <span
                                                        class="inline-block px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded mb-1 uppercase tracking-wider">
                                                        👦 {{ $item->customer->display_name ?? 'Tamu' }}
                                                    </span>

                                                    <h4
                                                        class="font-bold text-base text-slate-900 dark:text-white leading-tight flex items-start gap-2">
                                                        <span
                                                            class="text-emerald-600 dark:text-emerald-400 font-black text-lg">{{ $item->qty }}x</span>
                                                        <span class="mt-0.5">{{ $item->product->name }}</span>
                                                    </h4>

                                                    @if ($item->selectedAddons->count() > 0)
                                                        <div class="mt-2 flex flex-wrap gap-1">
                                                            @foreach ($item->selectedAddons as $addon)
                                                                <span
                                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300">+
                                                                    {{ $addon->addon_name }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if ($item->notes)
                                                        <div
                                                            class="mt-2.5 bg-red-50 dark:bg-red-900/30 p-2.5 rounded-lg border border-red-200 dark:border-red-800/50">
                                                            <p
                                                                class="text-xs font-bold text-red-600 dark:text-red-400 flex items-start gap-1.5">
                                                                <span
                                                                    class="italic leading-snug">"{{ $item->notes }}"</span>
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div
                                                class="flex gap-2 mt-2 pt-2 border-t border-slate-100 dark:border-slate-700 border-dashed">
                                                @if ($item->status == 'pending')
                                                    <button
                                                        wire:click="updateItemStatus({{ $item->id }}, 'cooking')"
                                                        class="flex-1 py-2.5 bg-amber-100 hover:bg-amber-200 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 font-black text-xs rounded-xl transition-colors border border-amber-200 dark:border-amber-800 shadow-sm flex justify-center items-center gap-1.5">
                                                        MULAI MASAK
                                                    </button>
                                                @elseif($item->status == 'cooking')
                                                    <button
                                                        wire:click="updateItemStatus({{ $item->id }}, 'ready_to_serve')"
                                                        class="flex-1 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs rounded-xl transition-colors shadow-sm shadow-emerald-500/30 flex justify-center items-center gap-1.5">
                                                        SELESAI DIMASAK
                                                    </button>
                                                @elseif($item->status == 'ready_to_serve')
                                                    <div
                                                        class="flex-1 py-2 bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400 font-black text-[10px] uppercase rounded-xl flex justify-center items-center gap-1.5 border border-emerald-200 dark:border-emerald-800">
                                                        <span
                                                            class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        SIAP DIPANGGIL
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        @if ($readyCount > 0)
                            <div
                                class="p-3 bg-emerald-50 dark:bg-emerald-900/20 border-t border-emerald-100 dark:border-emerald-800">
                                <button wire:click="callCashier({{ $order->id }})"
                                    class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-sm rounded-xl flex justify-center items-center gap-2 shadow-lg shadow-emerald-500/30 transition-transform active:scale-[0.98]">
                                    <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                        </path>
                                    </svg>
                                    PANGGIL KASIR ({{ $readyCount }} MENU)
                                </button>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const initEcho = () => {
                if (window.Echo) {
                    window.Echo.channel('kitchen-channel')
                        .listen('OrderPlaced', (e) => {
                            Livewire.dispatch('refreshOrders'); // Suruh dapur refresh
                        });
                } else {
                    setTimeout(initEcho, 500);
                }
            };
            initEcho();
        });
    </script>
</div>
