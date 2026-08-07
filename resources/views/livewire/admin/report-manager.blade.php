<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    @php
        $setting = \App\Models\Setting::first();
    @endphp

    <style wire:ignore>
        @media print {

            nav,
            .print\:hidden {
                display: none !important;
            }

            body {
                background-color: white !important;
                color: black !important;
                font-family: Arial, Helvetica, sans-serif !important;
            }

            @page {
                margin: 1cm 1.5cm;
                size: landscape;
            }

            .shadow-sm {
                box-shadow: none !important;
                border: none !important;
            }

            .rounded-3xl,
            .rounded-2xl {
                border-radius: 0 !important;
            }

            .bg-white,
            .dark\:bg-slate-800,
            .bg-slate-50,
            .dark\:bg-slate-900\/50,
            .bg-slate-100 {
                background-color: transparent !important;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            p,
            span,
            th,
            td,
            div {
                color: black !important;
            }

            .summary-cards {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                border-top: 2px solid black !important;
                border-bottom: 2px solid black !important;
                padding: 15px 0 !important;
                margin-bottom: 25px !important;
            }

            .summary-cards>div {
                border: none !important;
                padding: 0 10px !important;
                text-align: center !important;
                flex: 1;
            }

            .summary-cards>div>p {
                font-size: 10px !important;
                font-weight: bold !important;
                text-transform: uppercase;
                margin-bottom: 4px !important;
            }

            .summary-cards>div>h4 {
                font-size: 14px !important;
                margin: 0 !important;
            }

            .summary-cards>div>h4>span {
                display: none !important;
            }

            .overflow-x-auto {
                overflow: visible !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 1px solid black !important;
                margin-bottom: 20px !important;
            }

            thead {
                display: table-header-group !important;
            }

            th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                border: 1px solid black !important;
                color: black !important;
                font-size: 11px !important;
                padding: 8px !important;
                text-transform: uppercase !important;
            }

            td {
                border: 1px solid black !important;
                font-size: 11px !important;
                padding: 6px 8px !important;
                white-space: normal !important;
            }

            tfoot {
                display: table-row-group !important;
            }

            tfoot td {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                border-top: 2px solid black !important;
                font-size: 12px !important;
                font-weight: bold !important;
            }

            tr {
                page-break-inside: avoid !important;
            }

            /* Class untuk memaksa tabel pengeluaran tidak terpotong aneh di tengah halaman */
            .page-break-before {
                page-break-before: always;
                margin-top: 20px;
            }
        }
    </style>

    <div class="hidden print:block mb-6 pb-2">
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div style="flex: 1;">
                <h1 style="font-size: 24px; font-weight: 900; margin: 0; text-transform: uppercase;">
                    {{ $setting->store_name ?? 'CAFFEPOS' }}</h1>
                <p style="font-size: 12px; margin: 4px 0 0 0;">
                    {{ $setting->store_address ?? 'Alamat Kafe Belum Diatur' }}</p>
                <p style="font-size: 12px; margin: 2px 0 0 0;">Telp: {{ $setting->store_phone ?? '-' }}</p>
            </div>
            <div style="flex: 1; text-align: right;">
                <h2
                    style="font-size: 18px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px;">
                    Laporan Keuangan (Laba/Rugi)</h2>
                <p style="font-size: 12px; margin: 8px 0 0 0;">Periode:
                    <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> -
                    <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong></p>
                <p style="font-size: 11px; margin: 4px 0 0 0; color: #555;">Dicetak tgl:
                    {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="print:hidden mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Laporan & Laba Rugi</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Pantau omset, pengeluaran operasional, dan laba
                bersih riil bisnis Anda.</p>
        </div>
        <button onclick="window.print()"
            class="flex items-center gap-2 bg-slate-800 hover:bg-slate-700 dark:bg-emerald-600 dark:hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-md shadow-emerald-900/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            Cetak Laporan PDF
        </button>
    </div>

    <div
        class="print:hidden bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 mb-6 flex flex-col sm:flex-row items-center gap-4">
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Dari:</span>
            <input type="date" wire:model="startDate"
                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-sm focus:ring-emerald-500">
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Sampai:</span>
            <input type="date" wire:model="endDate"
                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-sm focus:ring-emerald-500">
        </div>
        <button wire:click="filterReport"
            class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-xl text-sm transition-all shadow-sm">
            Terapkan Filter
        </button>
    </div>

    <div class="summary-cards grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div
            class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
            <h4 class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ $totalOrders }} <span
                    class="text-xs font-medium text-slate-400">struk</span></h4>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Omset Kotor</p>
            <h4 class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1">Rp
                {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Modal Item (HPP)</p>
            <h4 class="text-xl font-black text-orange-500 dark:text-orange-400 mt-1">Rp
                {{ number_format($totalCogs, 0, ',', '.') }}</h4>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Laba Kotor</p>
            <h4 class="text-xl font-black text-emerald-500 dark:text-emerald-400 mt-1">Rp
                {{ number_format($grossProfit, 0, ',', '.') }}</h4>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-rose-200 dark:border-rose-900/50 shadow-sm flex flex-col justify-center bg-rose-50/30 dark:bg-rose-900/10">
            <p class="text-[11px] font-bold text-rose-500 uppercase tracking-wider">Pengeluaran Ops</p>
            <h4 class="text-xl font-black text-rose-600 dark:text-rose-400 mt-1">Rp
                {{ number_format($totalExpenses, 0, ',', '.') }}</h4>
        </div>
        <div
            class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-emerald-300 dark:border-emerald-700 shadow-md flex flex-col justify-center bg-emerald-50/50 dark:bg-emerald-900/20 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-500/10 rounded-full blur-xl"></div>
            <p class="text-[11px] font-black text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">LABA
                BERSIH</p>
            <h4
                class="text-xl font-black text-emerald-700 dark:text-emerald-400 mt-1 {{ $netProfit < 0 ? 'text-red-600 dark:text-red-400' : '' }}">
                Rp {{ number_format($netProfit, 0, ',', '.') }}
            </h4>
        </div>
    </div>

    <h3 class="text-lg font-black text-slate-800 dark:text-white mb-4">A. Rincian Pemasukan (Omset)</h3>
    <div
        class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl &
                            Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No.
                            Struk</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Metode</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Item
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Omset
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Modal
                            (HPP)</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Laba
                            Kotor</th>
                        <th
                            class="print:hidden px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50 bg-white dark:bg-slate-800">
                    @forelse ($orders as $order)
                        @php
                            $orderHpp = 0;
                            foreach ($order->items as $item) {
                                $itemCogs = $item->cogs_at_order;
                                foreach ($item->selectedAddons as $addon) {
                                    $itemCogs += $addon->addon_cogs;
                                }
                                $orderHpp += $itemCogs * $item->qty;
                            }
                            $orderProfit = $order->total_price - $orderHpp;
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</span><br>
                                <span
                                    class="text-[10px] text-slate-500">{{ $order->session->table->table_number ?? 'Bawa Pulang' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wide border {{ ($order->payment_method ?? 'Cash') == 'Cash' ? 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/40 dark:text-green-400 dark:border-green-800' : 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/40 dark:text-blue-400 dark:border-blue-800' }}">
                                    {{ $order->payment_method ?? 'Cash' }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-slate-600 dark:text-slate-300">
                                {{ $order->items->sum('qty') }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-orange-500 dark:text-orange-400">
                                Rp {{ number_format($orderHpp, 0, ',', '.') }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($orderProfit, 0, ',', '.') }}
                            </td>

                            <td class="print:hidden px-6 py-4 whitespace-nowrap text-center">
                                <button type="button"
                                    onclick="silentPrintReceipt('{{ route('cetak.struk', $order->id) }}')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-bold transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                        </path>
                                    </svg>
                                    Struk
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm font-medium text-slate-500">
                                Tidak ada transaksi lunas pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-slate-100 dark:bg-slate-900 border-t-2 border-slate-300 dark:border-slate-600">
                    <tr>
                        <td colspan="4"
                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">
                            Total Pemasukan Halaman Ini
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-right text-base font-black text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($orders->sum('total_price'), 0, ',', '.') }}
                        </td>
                        <td colspan="3" class="print:hidden"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if ($orders->hasPages())
            <div
                class="print:hidden px-6 py-4 border-t border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <div class="page-break-before">
        <h3 class="text-lg font-black text-slate-800 dark:text-white mb-4">B. Rincian Pengeluaran Operasional</h3>
        <div
            class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                    <thead class="bg-rose-50 dark:bg-rose-900/20">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider">
                                Tanggal</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider">
                                Kategori</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider">
                                Keterangan</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider">
                                Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50 bg-white dark:bg-slate-800">
                        @forelse ($expensesList as $exp)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                    {{ \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded">{{ $exp->category }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $exp->description }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-rose-600 dark:text-rose-400">
                                    Rp {{ number_format($exp->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm font-medium text-slate-500">
                                    Tidak ada catatan pengeluaran pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-rose-50 dark:bg-rose-900/20 border-t-2 border-rose-200 dark:border-rose-800">
                        <tr>
                            <td colspan="3"
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-rose-900 dark:text-rose-100 uppercase tracking-widest">
                                Total Pengeluaran
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-base font-black text-rose-600 dark:text-rose-400">
                                Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="hidden print:flex justify-end mt-12 break-inside-avoid">
        <div class="text-center w-48">
            <p class="text-sm text-black mb-16">Mengetahui,</p>
            <p class="text-sm font-bold text-black underline">{{ auth()->user()->name }}</p>
            <p class="text-xs text-slate-600">{{ auth()->user()->role == 'admin' ? 'Administrator' : 'Staff' }}</p>
        </div>
    </div>

    <script>
        function silentPrintReceipt(url) {
            let printFrame = document.getElementById('hidden-receipt-printer');
            if (!printFrame) {
                printFrame = document.createElement('iframe');
                printFrame.id = 'hidden-receipt-printer';
                printFrame.style.position = 'absolute';
                printFrame.style.width = '0px';
                printFrame.style.height = '0px';
                printFrame.style.border = 'none';
                printFrame.style.opacity = '0';
                document.body.appendChild(printFrame);
            }
            printFrame.src = url;
        }
    </script>
</div>
