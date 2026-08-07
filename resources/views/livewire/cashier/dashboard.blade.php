<div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
    <div class="max-w-[96rem] mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                    💰 Dasbor Kasir
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium flex items-center flex-wrap gap-4">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        Kosong</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        Menunggu (Hanya Scan)</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500"></span>
                        Terisi</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400"></span>
                        Lunas/Kotor</span>
                </p>
            </div>

            <div class="w-full md:w-72 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live="searchTable" type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-slate-300 dark:border-slate-700 rounded-xl leading-5 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition-colors"
                    placeholder="Cari nomor meja... (Mis: Meja 02)">
            </div>
        </div>

        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="mb-6 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl relative flex items-center gap-2"
                role="alert">
                <span class="block sm:inline font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
            @foreach ($tables as $table)
                @php
                    $session = $table->sessions->first();

                    $subtotalMurni = 0;
                    $unpaidSubtotal = 0;

                    $unpaidOrders = 0;
                    $hasOrders = false;

                    if ($session) {
                        $hasOrders = $session->orders->count() > 0;
                        foreach ($session->orders as $order) {
                            $orderTotal = 0;
                            foreach ($order->items as $item) {
                                $itemPrice = $item->price_at_order;
                                foreach ($item->selectedAddons as $addon) {
                                    $itemPrice += $addon->addon_price;
                                }
                                $orderTotal += $itemPrice * $item->qty;
                            }

                            $subtotalMurni += $orderTotal;

                            if ($order->payment_status == 'unpaid') {
                                $unpaidOrders++;
                                $unpaidSubtotal += $orderTotal;
                            }
                        }
                    }

                    $tax = $subtotalMurni * ($taxPercentage / 100);
                    $total = $subtotalMurni + $tax;

                    $unpaidTax = $unpaidSubtotal * ($taxPercentage / 100);
                    $unpaidTotal = $unpaidSubtotal + $unpaidTax;

                    $displayStatus = $table->status;

                    if ($displayStatus === 'dirty' && $unpaidOrders > 0) {
                        $displayStatus = 'occupied';
                    }

                    $isWaitingOnly = $displayStatus === 'occupied' && !$hasOrders;

                    // Pewarnaan Border Dinamis
                    $borderColor = 'border-emerald-500';
                    if ($isWaitingOnly) {
                        $borderColor = 'border-blue-500';
                    } elseif ($displayStatus === 'occupied') {
                        $borderColor = 'border-red-500';
                    } elseif ($displayStatus === 'dirty') {
                        $borderColor = 'border-amber-400';
                    }
                @endphp

                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border-t-4 {{ $borderColor }} border-x border-b border-x-slate-200 border-b-slate-200 dark:border-x-slate-700 dark:border-b-slate-700 overflow-hidden flex flex-col transition-all hover:shadow-md">

                    <div class="p-5 border-b border-slate-100 dark:border-slate-700/50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 dark:text-white">
                                    {{ $table->table_number }}</h3>
                                @if ($session)
                                    <p
                                        class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                                        👥 {{ $session->customers->count() }} Orang
                                    </p>
                                @else
                                    <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">Tidak ada
                                        pelanggan</p>
                                @endif
                            </div>

                            @if ($displayStatus === 'available')
                                <span
                                    class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide">KOSONG</span>
                            @elseif ($isWaitingOnly)
                                <span
                                    class="bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide">MENUNGGU</span>
                            @elseif ($displayStatus === 'occupied')
                                <span
                                    class="bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> TERISI
                                </span>
                            @elseif ($displayStatus === 'dirty')
                                <span
                                    class="bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wide">LUNAS
                                    / KOTOR</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-5 flex-1 bg-slate-50/50 dark:bg-slate-900/30">
                        @if ($displayStatus === 'available')
                            <div class="h-full flex flex-col items-center justify-center opacity-50 py-4">
                                <svg class="w-12 h-12 text-emerald-500 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="text-xs font-bold text-slate-500">Siap Digunakan</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    @if ($unpaidOrders > 0 && $table->status === 'dirty')
                                        <span class="text-slate-500 dark:text-slate-400">Tagihan Susulan:</span>
                                        <span class="font-black text-emerald-600 dark:text-emerald-400">
                                            Rp {{ number_format($unpaidTotal, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 dark:text-slate-400">Total Tagihan:</span>
                                        <span
                                            class="font-black {{ $displayStatus === 'dirty' ? 'text-slate-900 dark:text-white line-through opacity-70' : 'text-emerald-600 dark:text-emerald-400' }}">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-500 dark:text-slate-400">Status Bayar:</span>
                                    @if ($isWaitingOnly)
                                        <span class="font-bold text-blue-500">Menunggu Pesanan...</span>
                                    @elseif ($unpaidOrders > 0)
                                        <span class="font-bold text-red-500">Belum Lunas ({{ $unpaidOrders }}
                                            Nota)</span>
                                    @else
                                        <span class="font-bold text-emerald-500">Lunas Semua</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div
                        class="p-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-1 gap-2 bg-white dark:bg-slate-800">
                        @if ($isWaitingOnly)
                            <button wire:click="clearTable({{ $table->id }}, {{ $session->id }})"
                                wire:confirm="Pelanggan pindah meja? Yakin batalkan sesi ini?"
                                class="w-full py-2.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 font-bold rounded-xl transition-colors text-sm flex justify-center items-center gap-2 shadow-sm">
                                🔄 Bersihkan Paksa Sesi
                            </button>
                            <button wire:click="openPaymentModal({{ $session->id }})"
                                class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-300 font-bold rounded-xl text-[11px] flex justify-center items-center gap-1 transition-colors">
                                📝 Tambah Pesanan Manual
                            </button>
                        @elseif ($displayStatus === 'occupied')
                            <button wire:click="openPaymentModal({{ $session->id }})"
                                class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition-colors text-sm flex justify-center items-center gap-2 shadow-sm shadow-emerald-500/20">
                                💵 Proses Pembayaran
                            </button>
                            <button wire:click="openCallModal({{ $session->id }}, '{{ $table->table_number }}')"
                                class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-300 font-bold rounded-xl text-sm flex justify-center items-center gap-2 transition-colors">
                                🔔 Panggil Pelanggan
                            </button>
                        @elseif ($displayStatus === 'dirty')
                            <button wire:click="openPaymentModal({{ $session->id }})"
                                class="w-full py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/40 font-bold rounded-xl transition-colors text-sm flex justify-center items-center gap-2 shadow-sm border border-emerald-200 dark:border-emerald-800">
                                📝 Tambah Pesanan Susulan
                            </button>

                            <button wire:click="clearTable({{ $table->id }}, {{ $session->id }})"
                                class="w-full py-2.5 bg-amber-400 hover:bg-amber-300 text-amber-900 font-black rounded-xl transition-colors text-sm flex justify-center items-center gap-2 shadow-sm shadow-amber-400/20">
                                🧹 Selesai & Bersihkan Meja
                            </button>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    onclick="silentPrintReceipt('{{ route('cetak.struk', $session->orders->where('payment_status', 'paid')->last()->id ?? 0) }}')"
                                    class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-[11px] flex justify-center items-center gap-1 transition-colors">
                                    🖨️ Cetak Struk
                                </button>
                                <button wire:click="openCallModal({{ $session->id }}, '{{ $table->table_number }}')"
                                    class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300 font-bold rounded-xl text-[11px] flex justify-center items-center gap-1 transition-colors">
                                    🔔 Panggil
                                </button>
                            </div>
                        @else
                            <div class="py-2.5 text-center text-slate-400 text-xs font-medium">
                                Tidak ada aksi.
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if ($callModalOpen)
        <div
            class="fixed inset-0 z-[60] overflow-y-auto flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" wire:click="closeCallModal">
            </div>
            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700 z-10">
                <div class="px-6 py-5">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                        🔔 Panggil Pelanggan
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Pesan ini akan muncul sebagai Notifikasi
                        & Pop-up di layar HP Pelanggan.</p>

                    <textarea wire:model="callMessage" rows="3"
                        class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 text-sm p-3"></textarea>

                    <div class="mt-6 flex flex-col sm:flex-row-reverse gap-3">
                        <button wire:click="sendCallToCustomer"
                            class="w-full sm:w-auto inline-flex justify-center rounded-xl px-5 py-2.5 bg-emerald-600 text-sm font-bold text-white hover:bg-emerald-500 shadow-sm">
                            Kirim Notifikasi
                        </button>
                        <button wire:click="closeCallModal"
                            class="w-full sm:w-auto inline-flex justify-center rounded-xl px-5 py-2.5 bg-slate-100 text-sm font-bold text-slate-700 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($paymentModalOpen && $selectedSession)
        @php
            $modalSubtotal = 0;
            $personSubtotals = [];

            foreach ($selectedSession->orders->where('payment_status', 'unpaid') as $order) {
                foreach ($order->items as $item) {
                    $name = $item->customer->display_name ?? 'Tamu';
                    if (!isset($personSubtotals[$name])) {
                        $personSubtotals[$name] = 0;
                    }

                    $itemPrice = $item->price_at_order;
                    foreach ($item->selectedAddons as $addon) {
                        $itemPrice += $addon->addon_price;
                    }

                    $itemCost = $itemPrice * $item->qty;
                    $personSubtotals[$name] += $itemCost;
                    $modalSubtotal += $itemCost;
                }
            }

            $modalTax = $modalSubtotal * ($taxPercentage / 100);
            $modalTotal = $modalSubtotal + $modalTax;
        @endphp

        <div
            class="fixed inset-0 z-50 overflow-y-auto flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"
                wire:click="closePaymentModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-slate-200 dark:border-slate-700">
                <div
                    class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white">Pembayaran
                        {{ $selectedSession->table->table_number }}</h3>
                    <button wire:click="closePaymentModal"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-5 overflow-y-auto overflow-x-hidden max-h-[70vh] no-scrollbar">

                    @if (session()->has('success_cancel'))
                        <div
                            class="mb-4 bg-emerald-50 text-emerald-600 px-3 py-2 rounded-lg text-sm border border-emerald-200 font-bold">
                            ✅ {{ session('success_cancel') }}</div>
                    @endif
                    @if (session()->has('error_cancel'))
                        <div
                            class="mb-4 bg-red-50 text-red-600 px-3 py-2 rounded-lg text-sm border border-red-200 font-bold">
                            ❌ {{ session('error_cancel') }}</div>
                    @endif
                    @if (session()->has('success_add'))
                        <div
                            class="mb-4 bg-teal-50 text-teal-600 px-3 py-2 rounded-lg text-sm border border-teal-200 font-bold">
                            🚀 {{ session('success_add') }}</div>
                    @endif

                    <div class="mb-6">
                        <h4
                            class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Rincian & Kontrol Pesanan
                        </h4>

                        <div
                            class="space-y-3 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                            @forelse ($selectedSession->orders->where('payment_status', 'unpaid') as $order)
                                @foreach ($order->items as $item)
                                    @php
                                        $itemPriceAcc = $item->price_at_order;
                                        foreach ($item->selectedAddons as $addon) {
                                            $itemPriceAcc += $addon->addon_price;
                                        }
                                    @endphp

                                    <div
                                        class="flex justify-between items-center bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                        <div class="flex-1 min-w-0 pr-4">
                                            <div class="flex justify-between items-start mb-1">
                                                <p
                                                    class="font-bold text-sm text-slate-900 dark:text-white truncate pr-2">
                                                    {{ $item->product->name }}</p>
                                                <span
                                                    class="font-black text-sm text-emerald-600 dark:text-emerald-400 whitespace-nowrap">Rp
                                                    {{ number_format($itemPriceAcc * $item->qty, 0, ',', '.') }}</span>
                                            </div>

                                            @if ($item->selectedAddons->count() > 0)
                                                <div class="flex flex-wrap gap-1 mb-1.5">
                                                    @foreach ($item->selectedAddons as $addon)
                                                        <span
                                                            class="text-[10px] bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded font-medium">+
                                                            {{ $addon->addon_name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-2">
                                                <span
                                                    class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $item->qty }}x
                                                    Rp {{ number_format($itemPriceAcc, 0, ',', '.') }}</span>
                                                <span
                                                    class="text-[10px] text-slate-500 uppercase tracking-wider border-l border-slate-300 dark:border-slate-600 pl-3">Pemesan:
                                                    {{ $item->customer->display_name ?? 'Tamu' }}</span>
                                            </div>

                                            @if ($item->notes)
                                                <div
                                                    class="mt-1 mb-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded px-2 py-1 inline-block">
                                                    <p class="text-[10px] text-amber-700 dark:text-amber-400 italic">
                                                        "{{ $item->notes }}"</p>
                                                </div>
                                            @endif

                                            <div>
                                                @if ($item->status == 'pending')
                                                    <span
                                                        class="text-[10px] bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded font-bold border border-slate-200 dark:border-slate-600">⌛
                                                        Menunggu Dapur</span>
                                                @elseif($item->status == 'cooking')
                                                    <span
                                                        class="text-[10px] bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded font-bold border border-amber-200 dark:border-amber-800">🔥
                                                        Dimasak</span>
                                                @else
                                                    <span
                                                        class="text-[10px] bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 px-2 py-0.5 rounded font-bold border border-emerald-200 dark:border-emerald-800">✅
                                                        Selesai</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="shrink-0 border-l border-slate-100 dark:border-slate-700 pl-4">
                                            @if ($item->status == 'pending')
                                                <button wire:click="cancelItem({{ $item->id }})"
                                                    class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 p-2.5 rounded-xl transition-colors border border-transparent hover:border-red-200 dark:hover:border-red-800"
                                                    title="Batalkan Pesanan">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @else
                                                <div class="p-2.5 text-slate-300 dark:text-slate-600 cursor-not-allowed"
                                                    title="Sudah/Sedang dimasak, tidak bisa batal.">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @empty
                                <div class="text-center py-4 text-slate-500 dark:text-slate-400 text-sm font-medium">
                                    Pelanggan ini sudah melakukan scan, tapi belum mengirimkan pesanan apapun.
                                </div>
                            @endforelse

                            <div class="pt-4 mt-4 border-t border-slate-200 dark:border-slate-700 border-dashed">
                                <p
                                    class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">
                                    Tambah Menu Pengganti / Susulan (Cepat)</p>

                                <div class="flex flex-wrap gap-2 mb-2">
                                    <select wire:model.live="filterCategory"
                                        class="w-full sm:w-1/3 text-sm rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-emerald-500">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="relative flex-1 min-w-[200px]">
                                        <input type="text" wire:model.live="searchProduct"
                                            class="w-full text-sm rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-emerald-500 pl-8"
                                            placeholder="Cari nama menu...">
                                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-2">
                                    <select wire:model.live="newProductId"
                                        class="flex-1 text-sm rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-emerald-500">
                                        <option value="">-- Pilih Menu Dasar ({{ $availableProducts->count() }})
                                            --</option>
                                        @foreach ($availableProducts as $prod)
                                            <option value="{{ $prod->id }}">{{ $prod->name }} - Rp
                                                {{ number_format($prod->price, 0, ',', '.') }}</option>
                                        @endforeach
                                    </select>

                                    <select wire:model="newCustomerId"
                                        class="w-full sm:w-32 text-sm rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-emerald-500">
                                        @foreach ($selectedSession->customers as $cust)
                                            <option value="{{ $cust->id }}">{{ $cust->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if ($newProductId)
                                    @php
                                        $selectedProd = $availableProducts->firstWhere('id', $newProductId);
                                    @endphp
                                    <div
                                        class="mt-3 p-3 bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-800/50 rounded-xl shadow-inner">
                                        @if ($selectedProd && $selectedProd->addons->count() > 0)
                                            <p
                                                class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase mb-2">
                                                Pilih Varian:</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                                                @foreach ($selectedProd->addons as $addon)
                                                    <label
                                                        class="flex items-center gap-2 cursor-pointer bg-slate-50 dark:bg-slate-900/50 p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-emerald-400 transition-colors">
                                                        <input type="checkbox" wire:model="newAddons"
                                                            value="{{ $addon->id }}"
                                                            class="text-emerald-500 rounded focus:ring-emerald-500 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600">
                                                        <span
                                                            class="text-xs font-medium text-slate-700 dark:text-slate-300 flex-1">{{ $addon->name }}</span>
                                                        @if ($addon->additional_price > 0)
                                                            <span
                                                                class="text-[10px] font-bold text-slate-400">+Rp{{ number_format($addon->additional_price, 0, ',', '.') }}</span>
                                                        @endif
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="flex gap-2">
                                            <textarea wire:model="newNotes" rows="1"
                                                class="flex-1 text-sm rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-emerald-500 resize-none"
                                                placeholder="Catatan (Mis: Pedas Sedang)"></textarea>
                                            <input type="number" wire:model="newQty" min="1"
                                                class="w-16 text-sm rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 text-slate-900 dark:text-white text-center focus:ring-emerald-500"
                                                placeholder="Qty">
                                        </div>
                                    </div>

                                    <div class="mt-3 flex justify-end">
                                        <button wire:click="addItem"
                                            class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2 rounded-lg font-bold text-sm transition-all shadow-sm flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Tambahkan ke Nota
                                        </button>
                                    </div>
                                @endif

                                @if (session()->has('error_add'))
                                    <p class="text-xs text-red-500 mt-1.5 font-semibold">{{ session('error_add') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4 mt-6">
                        <div class="flex justify-between text-sm text-slate-500 dark:text-slate-400 mb-2">
                            <span>Subtotal Menu</span>
                            <span>Rp {{ number_format($modalSubtotal, 0, ',', '.') }}</span>
                        </div>
                        @if ($taxPercentage > 0)
                            <div class="flex justify-between text-sm text-slate-500 dark:text-slate-400 mb-2">
                                <span>Pajak ({{ $taxPercentage }}%)</span>
                                <span>Rp {{ number_format($modalTax, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div
                            class="flex justify-between text-3xl font-black text-slate-900 dark:text-white pt-2 border-t border-slate-100 dark:border-slate-700">
                            <span>TOTAL</span>
                            <span class="text-emerald-600 dark:text-emerald-400">Rp
                                {{ number_format($modalTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if ($modalTotal > 0)
                        <div
                            class="mt-6 bg-slate-50 dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-700">
                            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Pilih Metode
                                Pembayaran</h4>

                            <select wire:model.live="paymentMethod"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-bold py-3 focus:ring-emerald-500 focus:border-emerald-500 mb-4">
                                <option value="Cash">💵 Tunai (Cash)</option>
                                <option value="QRIS">📱 QRIS</option>
                                <option value="Transfer">🏦 Transfer Bank</option>
                                <option value="Debit">💳 Kartu Debit/Kredit</option>
                            </select>

                            @if ($paymentMethod === 'Cash')
                                <div class="space-y-4 animate-fade-in-up">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Uang
                                            Diterima (Rp)</label>
                                        <input type="text" wire:model.live.debounce.300ms="cashReceived"
                                            placeholder="Ketik nominal..."
                                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-black text-xl py-3 focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>

                                    <div
                                        class="bg-emerald-100 dark:bg-emerald-900/40 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800 flex justify-between items-center">
                                        <span
                                            class="font-bold text-emerald-800 dark:text-emerald-400">Kembalian:</span>
                                        <span class="text-2xl font-black text-emerald-700 dark:text-emerald-300">Rp
                                            {{ number_format($changeAmount, 0, ',', '.') }}</span>
                                    </div>

                                    @if (session()->has('error_payment'))
                                        <p class="text-xs font-bold text-red-500 mt-2">❌
                                            {{ session('error_payment') }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div
                    class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row-reverse gap-3">
                    @if ($modalTotal > 0)
                        <button wire:click="processPayment" wire:loading.attr="disabled" type="button"
                            class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl border border-transparent shadow-sm px-6 py-3 bg-emerald-600 text-base font-black text-white hover:bg-emerald-500 focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <span wire:loading.remove wire:target="processPayment">✅ Selesaikan Pembayaran</span>
                            <span wire:loading wire:target="processPayment">Memproses...</span>
                        </button>
                    @endif

                    <button wire:click="closePaymentModal" type="button"
                        class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-6 py-3 bg-white dark:bg-slate-800 text-base font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        function silentPrintReceipt(url) {
            let printFrame = document.getElementById('hidden-receipt-printer');
            if (!printFrame) {
                printFrame = document.createElement('iframe');
                printFrame.id = 'hidden-receipt-printer';
                printFrame.style.display = 'none';
                document.body.appendChild(printFrame);
            }
            printFrame.src = url;
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('print-receipt', (data) => {
                let printUrl = Array.isArray(data) ? data[0].url : data.url;
                if (printUrl) silentPrintReceipt(printUrl);
            });
        });
    </script>
</div>
