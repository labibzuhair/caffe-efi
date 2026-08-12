<?php

use App\Livewire\Actions\Logout;
use App\Models\OrderItem;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public $pickupItems = [];
    public $servedItems = [];

    public function mount()
    {
        $this->loadNotifications();
    }

    #[On('echo:cashier-channel,.App\\Events\\OrderReadyForPickup')]
    public function handleKitchenNotification($payload)
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'cashier'])) {
            $this->loadNotifications();
            $message = $payload['message'] ?? 'Pesanan baru siap diambil!';
            $this->dispatch('notify-cashier', message: $message);
        }
    }

    public function loadNotifications()
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'cashier'])) {
            $rawPickupItems = OrderItem::with(['order.session.table', 'product'])
                ->where('status', 'waiting_pickup')
                ->orderBy('updated_at', 'asc')
                ->get();

            $this->pickupItems = $rawPickupItems
                ->groupBy('order_id')
                ->map(function ($items) {
                    $firstItem = $items->first();

                    $summary = $items
                        ->map(function ($item) {
                            return $item->qty . 'x ' . $item->product->name;
                        })
                        ->join(', ');

                    return (object) [
                        'order_id' => $firstItem->order_id,
                        'table_number' => $firstItem->order->session->table->table_number ?? 'Takeaway',
                        'total_qty' => $items->sum('qty'),
                        'summary' => $summary,
                        'updated_at' => $firstItem->updated_at,
                        'item_ids' => $items->pluck('id')->toArray(),
                    ];
                })
                ->values()
                ->all();

            $rawServedItems = OrderItem::with(['order.session.table', 'product'])
                ->where('status', 'served')
                ->whereDate('updated_at', today())
                ->orderBy('updated_at', 'desc')
                ->take(30)
                ->get();

            $this->servedItems = $rawServedItems
                ->groupBy('order_id')
                ->map(function ($items) {
                    $firstItem = $items->first();
                    $summary = $items
                        ->map(function ($item) {
                            return $item->qty . 'x ' . $item->product->name;
                        })
                        ->join(', ');

                    return (object) [
                        'order_id' => $firstItem->order_id,
                        'table_number' => $firstItem->order->session->table->table_number ?? 'Takeaway',
                        'total_qty' => $items->sum('qty'),
                        'summary' => $summary,
                        'updated_at' => $firstItem->updated_at,
                    ];
                })
                ->values()
                ->all();
        }
    }

    public function markAsServed($itemIds)
    {
        if (is_array($itemIds) && count($itemIds) > 0) {
            OrderItem::whereIn('id', $itemIds)->update(['status' => 'served']);
            $this->loadNotifications();
            $this->dispatch('refresh-cashier-table');
        }
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $setting = \App\Models\Setting::first();
    $storeName = $setting->store_name ?? 'CaffePOS';
@endphp

<nav x-data="{ open: false }"
    class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 transition-colors duration-500 sticky top-0 z-50">

    <div x-data="{ toasts: [] }"
        @notify-cashier.window="
            if (!toasts.includes($event.detail.message)) {
                toasts.push($event.detail.message);
                setTimeout(() => toasts.shift(), 10000);
                let audio = new Audio('{{ asset('audio/kitchen-bell.mp3') }}');
                audio.play().catch(e => console.warn('Autoplay audio diblokir'));
            }
         "
        class="fixed top-16 sm:top-20 left-4 right-4 sm:left-auto sm:right-6 z-[100] flex flex-col gap-3 pointer-events-none">
        <template x-for="toast in toasts">
            <div
                class="bg-amber-400 text-amber-900 px-5 sm:px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 w-full sm:w-[400px] transform transition-all animate-fade-in-up border-2 border-amber-500 pointer-events-auto">
                <div class="bg-amber-100 p-2 rounded-full shrink-0">
                    <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                </div>
                <span class="font-bold text-sm leading-snug" x-text="toast"></span>
            </div>
        </template>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                        @if ($setting && $setting->logo)
                            <img src="{{ asset('storage/' . $setting->logo) }}" alt="{{ $storeName }}"
                                class="h-8 w-auto object-contain">
                            <span
                                class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white hidden sm:block">{{ $storeName }}</span>
                        @else
                            <div class="p-1.5 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-lg shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M20 8h-3V4H3v13a4 4 0 004 4h9a4 4 0 004-4v-4h2a2 2 0 002-2V10a2 2 0 00-2-2zM7 4v14m5-14v14">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white hidden sm:block">{{ $storeName }}</span>
                        @endif
                    </a>
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    @if (auth()->user()->role === 'admin')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate
                            class="dark:text-slate-300 dark:hover:text-emerald-400">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:border-slate-300 dark:hover:border-slate-700 focus:outline-none transition duration-150 ease-in-out h-full">
                                        <div>Manajemen Data</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.categories')" wire:navigate
                                        class="dark:text-slate-300 dark:hover:bg-slate-800">Kategori</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.products')" wire:navigate
                                        class="dark:text-slate-300 dark:hover:bg-slate-800">Produk
                                        Menu</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.tables')" wire:navigate
                                        class="dark:text-slate-300 dark:hover:bg-slate-800">Meja Kafe</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:border-slate-300 dark:hover:border-slate-700 focus:outline-none transition duration-150 ease-in-out h-full">
                                        <div>Sistem & Keuangan</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.expenses')" wire:navigate
                                        class="font-bold text-rose-600 dark:text-rose-400 dark:hover:bg-slate-800">Catat
                                        Pengeluaran</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.reports')" wire:navigate
                                        class="font-bold text-blue-600 dark:text-blue-400 dark:hover:bg-slate-800">Laporan
                                        Detail</x-dropdown-link>
                                    <hr class="border-slate-100 dark:border-slate-700 my-1">
                                    <x-dropdown-link :href="route('admin.users')" wire:navigate
                                        class="dark:text-slate-300 dark:hover:bg-slate-800">Pengguna
                                        (Karyawan)</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.settings')" wire:navigate
                                        class="dark:text-slate-300 dark:hover:bg-slate-800">Pengaturan
                                        Toko</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    @if (in_array(auth()->user()->role, ['admin', 'kitchen']))
                        <x-nav-link :href="route('dapur')" :active="request()->routeIs('dapur')" wire:navigate
                            class="dark:text-slate-300 dark:hover:text-emerald-400 font-bold text-emerald-600">Layar
                            Dapur (KDS)</x-nav-link>
                    @endif

                    @if (in_array(auth()->user()->role, ['admin', 'cashier']))
                        <x-nav-link :href="route('kasir')" :active="request()->routeIs('kasir')" wire:navigate
                            class="dark:text-slate-300 dark:hover:text-emerald-400 font-bold text-amber-600">Dasbor
                            Kasir</x-nav-link>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-1 sm:gap-2">

                @if (in_array(auth()->user()->role, ['admin', 'cashier']))
                    <div x-data="{ openNotif: false, tab: 'pickup' }" @click.outside="openNotif = false" class="relative">
                        <button type="button" @click="openNotif = !openNotif"
                            class="relative p-2 rounded-full text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>

                            @if (count($pickupItems) > 0)
                                <span
                                    class="absolute top-0 right-0 flex h-4 w-4 sm:h-5 sm:w-5 items-center justify-center rounded-full bg-red-500 text-[9px] sm:text-[10px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-slate-900 animate-bounce">
                                    {{ count($pickupItems) }}
                                </span>
                            @endif
                        </button>

                        <div x-show="openNotif" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                            class="absolute right-[-40px] sm:right-0 mt-3 w-[320px] sm:w-[450px] max-w-[95vw] bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden z-[60] flex flex-col"
                            style="max-height: 80vh;">

                            <div
                                class="flex shrink-0 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                <button type="button" @click="tab = 'pickup'"
                                    :class="{ 'border-b-2 border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold': tab === 'pickup', 'text-slate-500 font-medium': tab !== 'pickup' }"
                                    class="flex-1 py-3 text-sm transition-colors relative">
                                    Perlu Diambil
                                    @if (count($pickupItems) > 0)
                                        <span
                                            class="ml-1 bg-red-500 text-white text-[9px] px-1.5 py-0.5 rounded-full">{{ count($pickupItems) }}</span>
                                    @endif
                                </button>
                                <button type="button" @click="tab = 'history'"
                                    :class="{ 'border-b-2 border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold': tab === 'history', 'text-slate-500 font-medium': tab !== 'history' }"
                                    class="flex-1 py-3 text-sm transition-colors">
                                    Riwayat Selesai
                                </button>
                            </div>

                            <div class="overflow-y-auto no-scrollbar p-3 flex-1">

                                <div x-show="tab === 'pickup'" class="space-y-3">
                                    @forelse($pickupItems as $group)
                                        <div wire:key="pickup-order-{{ $group->order_id }}"
                                            class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 flex justify-between items-center gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span
                                                        class="text-[10px] font-black uppercase text-amber-700 dark:text-amber-400 bg-amber-200 dark:bg-amber-900/50 px-2 py-0.5 rounded-md">
                                                        {{ $group->table_number }}
                                                    </span>
                                                    <span
                                                        class="text-[10px] font-bold text-amber-600 dark:text-amber-500">
                                                        {{ $group->total_qty }} Item
                                                    </span>
                                                </div>

                                                <p
                                                    class="font-medium text-sm text-slate-700 dark:text-slate-300 leading-snug break-words">
                                                    {{ $group->summary }}
                                                </p>

                                                <p
                                                    class="text-[10px] font-semibold text-slate-500 mt-2 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Dipanggil {{ $group->updated_at->diffForHumans(null, true, true) }}
                                                    lalu
                                                </p>
                                            </div>

                                            <button wire:click="markAsServed({{ json_encode($group->item_ids) }})"
                                                class="shrink-0 w-10 h-10 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full flex items-center justify-center transition-colors shadow-sm"
                                                title="Tandai Semua Selesai Diambil">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @empty
                                        <div class="py-8 text-center text-slate-400 dark:text-slate-500">
                                            <p class="text-sm font-medium">Tidak ada pesanan yang perlu diambil.</p>
                                        </div>
                                    @endforelse
                                </div>

                                <div x-show="tab === 'history'" class="space-y-3" style="display: none;">
                                    @forelse($servedItems as $group)
                                        <div wire:key="history-order-{{ $group->order_id }}"
                                            class="bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700/50 rounded-xl p-3 opacity-75">
                                            <div class="flex justify-between items-start mb-1.5">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="text-[10px] font-black uppercase text-slate-500 bg-slate-200 dark:bg-slate-700 px-2 py-0.5 rounded-md">
                                                        {{ $group->table_number }}
                                                    </span>
                                                    <span class="text-[10px] font-bold text-slate-400">
                                                        {{ $group->total_qty }} Item
                                                    </span>
                                                </div>
                                                <span
                                                    class="text-[10px] font-semibold text-slate-400 shrink-0 ml-2 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    {{ $group->updated_at->format('H:i') }}
                                                </span>
                                            </div>

                                            <p
                                                class="font-medium text-sm text-slate-600 dark:text-slate-400 leading-snug break-words">
                                                {{ $group->summary }}
                                            </p>
                                        </div>
                                    @empty
                                        <div class="py-8 text-center text-slate-400 dark:text-slate-500">
                                            <p class="text-sm font-medium">Belum ada riwayat pengambilan hari ini.</p>
                                        </div>
                                    @endforelse
                                </div>

                            </div>
                        </div>
                    </div>
                @endif

                <button x-data="{
                    isDark: localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                    toggleTheme() {
                        this.isDark = !this.isDark;
                        if (this.isDark) {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        }
                        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: this.isDark } }));
                    }
                }" @click="toggleTheme()"
                    class="p-2 rounded-full text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <svg x-show="isDark" x-cloak class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <svg x-show="!isDark" class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </button>

                <div class="hidden sm:block">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-lg text-slate-600 dark:text-slate-300 bg-slate-100/50 dark:bg-slate-800/50 hover:text-emerald-600 dark:hover:text-emerald-400 focus:outline-none transition ease-in-out duration-150">
                                <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                    x-on:profile-updated.window="name = $event.detail.name"></div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700/50">
                                <span
                                    class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Role:
                                    {{ auth()->user()->role }}</span>
                            </div>
                            <x-dropdown-link :href="route('profile')" wire:navigate
                                class="dark:text-slate-300 dark:hover:bg-slate-800">{{ __('Profil Saya') }}</x-dropdown-link>
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link
                                    class="dark:text-slate-300 dark:hover:bg-slate-800 text-red-600 dark:text-red-400">{{ __('Keluar') }}</x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                </div>

                <div class="flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }"
        class="hidden sm:hidden dark:bg-slate-900 border-t dark:border-slate-800">
        <div class="pt-2 pb-3 space-y-1">
            @if (auth()->user()->role === 'admin')
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate
                    class="dark:text-slate-300">{{ __('Dashboard') }}</x-responsive-nav-link>
                <div class="px-4 py-2 mt-2"><span
                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Manajemen
                        Data</span></div>
                <x-responsive-nav-link :href="route('admin.categories')" :active="request()->routeIs('admin.categories')" wire:navigate
                    class="dark:text-slate-300 pl-8">Kategori</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.products')" :active="request()->routeIs('admin.products')" wire:navigate
                    class="dark:text-slate-300 pl-8">Produk Menu</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.tables')" :active="request()->routeIs('admin.tables')" wire:navigate
                    class="dark:text-slate-300 pl-8">Meja</x-responsive-nav-link>
                <div class="px-4 py-2 mt-2"><span
                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Sistem
                        & Keuangan</span></div>
                <x-responsive-nav-link :href="route('admin.expenses')" :active="request()->routeIs('admin.expenses')" wire:navigate
                    class="dark:text-slate-300 font-bold text-rose-600 pl-8">Catat Pengeluaran</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')" wire:navigate
                    class="dark:text-slate-300 font-bold text-blue-600 pl-8">Laporan Detail</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" wire:navigate
                    class="dark:text-slate-300 pl-8">Pengguna</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')" wire:navigate
                    class="dark:text-slate-300 pl-8">Pengaturan</x-responsive-nav-link>
                <div class="border-t border-slate-200 dark:border-slate-800 my-2"></div>
            @endif
            @if (in_array(auth()->user()->role, ['admin', 'kitchen']))
                <x-responsive-nav-link :href="route('dapur')" :active="request()->routeIs('dapur')" wire:navigate
                    class="dark:text-slate-300 font-bold text-emerald-600">Layar Dapur (KDS)</x-responsive-nav-link>
            @endif
            @if (in_array(auth()->user()->role, ['admin', 'cashier']))
                <x-responsive-nav-link :href="route('kasir')" :active="request()->routeIs('kasir')" wire:navigate
                    class="dark:text-slate-300 font-bold text-amber-600">Dasbor Kasir</x-nav-link>
            @endif
        </div>
        <div class="pt-4 pb-1 border-t border-slate-200 dark:border-slate-800">
            <div class="px-4">
                <div class="font-bold text-base text-slate-800 dark:text-slate-200" x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                    x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate
                    class="dark:text-slate-300">{{ __('Profil Saya') }}</x-responsive-nav-link>
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link
                        class="text-red-600 dark:text-red-400">{{ __('Keluar') }}</x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
