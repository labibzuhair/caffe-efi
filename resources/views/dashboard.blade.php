<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Dashboard Utama') }}
        </h2>
    </x-slot>

    @php
        $filter = request('filter', 'bulanan');
        $selectedMonth = request('bulan', now()->month);
        $selectedYear = request('tahun', now()->year);
        $now = now();

        if ($filter == 'mingguan') {
            $startDate = $now->copy()->subDays(6)->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $periodLabel = '7 Hari Terakhir';
        } elseif ($filter == 'tahun_ini') {
            $startDate = \Carbon\Carbon::create($selectedYear, 1, 1)->startOfYear();
            $endDate = \Carbon\Carbon::create($selectedYear, 12, 31)->endOfYear();
            $periodLabel = 'Tahun ' . $selectedYear;
        } else {
            $startDate = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
            $periodLabel = $startDate->translatedFormat('F Y');
        }

        $availableYears = \App\Models\Order::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        if ($availableYears->isEmpty()) {
            $availableYears = [now()->year];
        }

        $orders = \App\Models\Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->get();
        $omset = $orders->sum('total_price');

        $pengeluaran = \App\Models\Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');

        $hppItems = \App\Models\OrderItem::with(['selectedAddons', 'product.category'])
            ->whereIn('order_id', $orders->pluck('id'))
            ->get();

        $totalHpp = 0;
        foreach ($hppItems as $item) {
            $itemCogs = $item->cogs_at_order;
            foreach ($item->selectedAddons as $addon) {
                $itemCogs += $addon->addon_cogs;
            }
            $totalHpp += $itemCogs * $item->qty;
        }

        $labaBersih = $omset - $totalHpp - $pengeluaran;

        $chartLabels = [];
        $chartOmset = [];
        $chartPengeluaran = [];
        $chartLaba = [];

        if ($filter == 'tahun_ini') {
            for ($m = 1; $m <= 12; $m++) {
                $dt = \Carbon\Carbon::create($selectedYear, $m, 1);
                $chartLabels[] = $dt->translatedFormat('M');

                $mOrders = $orders->filter(fn($o) => $o->created_at->month == $m);
                $mOmset = $mOrders->sum('total_price');

                $mExp = \App\Models\Expense::whereMonth('expense_date', $m)
                    ->whereYear('expense_date', $selectedYear)
                    ->sum('amount');

                $mItems = $hppItems->whereIn('order_id', $mOrders->pluck('id'));
                $mHpp = 0;
                foreach ($mItems as $it) {
                    $cogs = $it->cogs_at_order;
                    foreach ($it->selectedAddons as $ad) {
                        $cogs += $ad->addon_cogs;
                    }
                    $mHpp += $cogs * $it->qty;
                }

                $chartOmset[] = $mOmset;
                $chartPengeluaran[] = $mExp + $mHpp;
                $chartLaba[] = $mOmset - ($mExp + $mHpp);
            }
        } else {
            $daysCount = $startDate->diffInDays($endDate);
            for ($d = 0; $d <= $daysCount; $d++) {
                $dt = $startDate->copy()->addDays($d);
                $chartLabels[] = $dt->translatedFormat('d M');

                $dOrders = $orders->filter(fn($o) => $o->created_at->format('Y-m-d') == $dt->format('Y-m-d'));
                $dOmset = $dOrders->sum('total_price');

                $dExp = \App\Models\Expense::whereDate('expense_date', $dt->format('Y-m-d'))->sum('amount');

                $dItems = $hppItems->whereIn('order_id', $dOrders->pluck('id'));
                $dHpp = 0;
                foreach ($dItems as $it) {
                    $cogs = $it->cogs_at_order;
                    foreach ($it->selectedAddons as $ad) {
                        $cogs += $ad->addon_cogs;
                    }
                    $dHpp += $cogs * $it->qty;
                }

                $chartOmset[] = $dOmset;
                $chartPengeluaran[] = $dExp + $dHpp;
                $chartLaba[] = $dOmset - ($dExp + $dHpp);
            }
        }

        $catGroups = $hppItems->groupBy(fn($item) => $item->product->category->name ?? 'Lainnya');
        $catLabels = $catGroups->keys()->toArray();
        $catSeries = $catGroups->map(fn($group) => $group->sum('qty'))->values()->toArray();
        if (empty($catLabels)) {
            $catLabels = ['No Data'];
            $catSeries = [0];
        }
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl overflow-hidden shadow-lg relative">
                <div class="p-6 sm:p-8 relative z-10">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Halo,
                        {{ auth()->user()->name }}!</h3>
                    <p class="mt-1 text-emerald-50 text-base">Laporan performa periode
                        <strong>{{ $periodLabel }}</strong>.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard') }}"
                class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50">
                <div class="flex flex-col lg:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-3 w-full lg:w-auto">
                        <div class="p-2.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-slate-200">Arsip Laporan</h3>
                            <p class="text-xs text-slate-500 italic">Filter data berdasarkan waktu tertentu.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 w-full lg:w-auto">
                        <select name="filter" onchange="this.form.submit()"
                            class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:ring-emerald-500">
                            <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                            <option value="mingguan" {{ $filter == 'mingguan' ? 'selected' : '' }}>7 Hari Terakhir
                            </option>
                            <option value="tahun_ini" {{ $filter == 'tahun_ini' ? 'selected' : '' }}>Tahunan</option>
                        </select>

                        <select name="bulan" {{ in_array($filter, ['mingguan', 'tahun_ini']) ? 'disabled' : '' }}
                            class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:ring-emerald-500 {{ in_array($filter, ['mingguan', 'tahun_ini']) ? 'opacity-50' : '' }}">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2024, $m, 1)->translatedFormat('F') }}</option>
                            @endfor
                        </select>

                        <select name="tahun" {{ $filter == 'mingguan' ? 'disabled' : '' }}
                            class="bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:ring-emerald-500 {{ $filter == 'mingguan' ? 'opacity-50' : '' }}">
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    {{ $year }}</option>
                            @endforeach
                        </select>

                        <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>

                        <a href="{{ route('dashboard') }}"
                            class="bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-transform">
                    <div
                        class="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-500 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Omset Kotor</p>
                        <h4 class="text-xl font-black text-slate-900 dark:text-white truncate">Rp
                            {{ number_format($omset, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-transform">
                    <div
                        class="w-14 h-14 rounded-full bg-orange-50 dark:bg-orange-900/30 text-orange-500 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Modal (HPP)</p>
                        <h4 class="text-xl font-black text-slate-900 dark:text-white truncate">Rp
                            {{ number_format($totalHpp, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-transform">
                    <div
                        class="w-14 h-14 rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Pengeluaran</p>
                        <h4 class="text-xl font-black text-rose-600 truncate">Rp
                            {{ number_format($pengeluaran, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/50 shadow-sm flex items-center gap-4 hover:-translate-y-1 transition-transform">
                    <div
                        class="w-14 h-14 rounded-full {{ $labaBersih >= 0 ? 'bg-emerald-50 text-emerald-500' : 'bg-red-50 text-red-500' }} flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Laba Bersih</p>
                        <h4
                            class="text-xl font-black {{ $labaBersih >= 0 ? 'text-emerald-600' : 'text-red-600' }} truncate">
                            Rp {{ number_format($labaBersih, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <div x-data="window.dashboardCharts()" x-init="initCharts()"
                @theme-changed.window="updateTheme($event.detail.isDark)">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div
                        class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/50 shadow-sm">
                        <h5 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Tren Finansial Lengkap</h5>
                        <div wire:ignore>
                            <div id="financialChart" class="w-full h-80"></div>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200/60 dark:border-slate-700/50 shadow-sm">
                        <h5 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Penjualan Kategori</h5>
                        <div wire:ignore>
                            <div id="categoryChart" class="w-full h-80 flex justify-center items-center"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        /* Memaksa Tooltip ApexCharts agar selalu gelap jika HTML memiliki class 'dark' */
        .dark .apexcharts-tooltip {
            background: #1e293b !important;
            /* slate-800 */
            border: 1px solid #334155 !important;
            /* slate-700 */
            color: #f8fafc !important;
            /* slate-50 */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
        }

        .dark .apexcharts-tooltip-title {
            background: #0f172a !important;
            /* slate-900 */
            border-bottom: 1px solid #334155 !important;
            font-weight: 800 !important;
        }

        .dark .apexcharts-tooltip-text-y-value {
            color: #f8fafc !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts" data-navigate-track></script>

    <script>
        window.dashboardCharts = function() {
            return {
                finChartInstance: null,
                catChartInstance: null,

                initCharts() {
                    if (typeof ApexCharts === 'undefined') {
                        setTimeout(() => this.initCharts(), 150);
                        return;
                    }

                    const isDark = document.documentElement.classList.contains('dark');
                    const textColor = isDark ? '#94a3b8' : '#64748b';
                    const tooltipTheme = isDark ? 'dark' : 'light';

                    const finOptions = {
                        series: [{
                                name: 'Omset Kotor',
                                type: 'area',
                                data: @json($chartOmset)
                            },
                            {
                                name: 'Laba Bersih',
                                type: 'line',
                                data: @json($chartLaba)
                            },
                            {
                                name: 'Biaya (HPP+Opex)',
                                type: 'line',
                                data: @json($chartPengeluaran)
                            }
                        ],
                        chart: {
                            height: 350,
                            type: 'line',
                            toolbar: {
                                show: false
                            },
                            background: 'transparent'
                        },
                        stroke: {
                            curve: 'smooth',
                            width: [3, 3, 3]
                        },
                        colors: ['#3b82f6', '#10b981', '#f43f5e'],
                        xaxis: {
                            categories: @json($chartLabels),
                            labels: {
                                style: {
                                    colors: textColor
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: (val) => "Rp " + (val / 1000).toFixed(0) + "k",
                                style: {
                                    colors: textColor
                                }
                            }
                        },
                        tooltip: {
                            theme: tooltipTheme
                        },
                        theme: {
                            mode: isDark ? 'dark' : 'light'
                        }
                    };

                    const catOptions = {
                        series: @json($catSeries),
                        chart: {
                            type: 'donut',
                            height: 300
                        },
                        labels: @json($catLabels),
                        colors: ['#10b981', '#f59e0b', '#0ea5e9', '#8b5cf6', '#f43f5e'],
                        legend: {
                            position: 'bottom',
                            labels: {
                                colors: textColor
                            }
                        },
                        tooltip: {
                            theme: tooltipTheme
                        },
                        theme: {
                            mode: isDark ? 'dark' : 'light'
                        }
                    };

                    if (this.finChartInstance) {
                        this.finChartInstance.destroy();
                    }
                    if (this.catChartInstance) {
                        this.catChartInstance.destroy();
                    }

                    this.finChartInstance = new ApexCharts(document.getElementById("financialChart"), finOptions);
                    this.finChartInstance.render();

                    this.catChartInstance = new ApexCharts(document.getElementById("categoryChart"), catOptions);
                    this.catChartInstance.render();
                },

                updateTheme(isDark) {
                    const mode = isDark ? 'dark' : 'light';

                    if (this.finChartInstance) {
                        this.finChartInstance.updateOptions({
                            theme: {
                                mode
                            },
                            tooltip: {
                                theme: mode
                            }
                        });
                    }
                    if (this.catChartInstance) {
                        this.catChartInstance.updateOptions({
                            theme: {
                                mode
                            },
                            tooltip: {
                                theme: mode
                            }
                        });
                    }
                }
            };
        };
    </script>
</x-app-layout>
