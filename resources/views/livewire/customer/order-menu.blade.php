<div class="min-h-screen bg-slate-50 dark:bg-slate-900 pb-24 transition-colors duration-500">

    <div
        class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-800/50 pt-4 shadow-sm">
        <div class="max-w-3xl mx-auto px-4 flex justify-between items-center mb-3">
            <div>
                <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-0.5">
                    {{ $table->table_number }}</p>
                <h1 class="text-xl font-black text-slate-900 dark:text-white line-clamp-1">Pesan Makanan 🍽️</h1>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('customer.active-order') }}"
                    class="p-2 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 font-bold text-xs flex items-center gap-1.5 hover:bg-emerald-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    Bill
                </a>

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

        <div class="max-w-3xl mx-auto px-4 py-3 bg-slate-50/50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800"
            x-data="{ showModal: false }" @person-added.window="showModal = false">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Memesan untuk:</p>
            <div class="flex overflow-x-auto no-scrollbar gap-2 pb-1">
                @foreach ($activeCustomers as $cust)
                    <button wire:click="$set('selectedCustomerId', {{ $cust->id }})"
                        class="whitespace-nowrap px-4 py-2 rounded-xl text-sm font-bold border transition-all flex items-center gap-2 {{ $selectedCustomerId == $cust->id ? 'bg-emerald-100 border-emerald-500 text-emerald-700 dark:bg-emerald-900/40 dark:border-emerald-500 dark:text-emerald-400 shadow-sm' : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300' }}">
                        <div
                            class="w-2 h-2 rounded-full {{ $selectedCustomerId == $cust->id ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300 dark:bg-slate-600' }}">
                        </div>
                        {{ $cust->display_name }} {{ $cust->id == $customer->id ? '(Saya)' : '' }}
                    </button>
                @endforeach
                <button @click="showModal = true"
                    class="whitespace-nowrap px-4 py-2 rounded-xl text-sm font-bold bg-slate-100 border border-dashed border-slate-300 text-slate-500 hover:bg-slate-200 dark:bg-slate-800/50 dark:border-slate-600 dark:text-slate-400 dark:hover:bg-slate-700 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Orang
                </button>
            </div>

            <div x-cloak x-show="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
                <div @click.away="showModal = false"
                    class="bg-white dark:bg-slate-800 rounded-3xl p-6 w-full max-w-sm shadow-2xl transform transition-all">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white mb-2">Tambah Teman Meja</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Siapa lagi yang ingin memesan dari HP
                        ini?</p>
                    <input wire:model="newPersonName" type="text"
                        class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white mb-4 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Nama teman..." @keydown.enter="$wire.addNewPerson()">
                    @error('newPersonName')
                        <span class="text-xs text-red-500 mb-4 block">{{ $message }}</span>
                    @enderror
                    <div class="flex gap-2">
                        <button @click="showModal = false"
                            class="flex-1 py-3 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl">Batal</button>
                        <button wire:click="addNewPerson"
                            class="flex-1 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-500">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-4 flex overflow-x-auto no-scrollbar gap-2 py-3 bg-white dark:bg-slate-900">
            @foreach ($categories as $cat)
                @if ($cat->products->count() > 0)
                    <a href="#cat-{{ $cat->id }}"
                        class="whitespace-nowrap px-4 py-1.5 bg-slate-100 dark:bg-slate-800 text-sm font-bold text-slate-600 dark:text-slate-300 rounded-full hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-white transition-colors border border-slate-200/50 dark:border-slate-700/50">
                        {{ $cat->name }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 pt-4 space-y-10">
        @foreach ($categories as $cat)
            @if ($cat->products->count() > 0)
                <div id="cat-{{ $cat->id }}" class="scroll-mt-48">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white mb-4 flex items-center gap-3">
                        {{ $cat->name }}
                        <div class="h-px bg-slate-200 dark:bg-slate-700 flex-1"></div>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($cat->products as $product)
                            <div x-data="{ p: {{ Js::from($product) }} }"
                                class="bg-white dark:bg-slate-800 rounded-2xl p-3 flex gap-4 shadow-sm border border-slate-100 dark:border-slate-700/50 relative {{ !$product->is_active ? 'opacity-60 grayscale' : '' }}">

                                <button type="button"
                                    @if ($product->is_active) @if ($product->addons && $product->addons->count() > 0)
                                            @click="$dispatch('open-product-modal', { product: p, customerId: $wire.get('selectedCustomerId') })"
                                        @else
                                            @click="$dispatch('add-to-cart', { productId: p.id, customerId: $wire.get('selectedCustomerId'), qty: 1, addons: [], note: '' })" @endif
                                    @endif
                                    class="w-24 h-24 shrink-0 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700 relative focus:outline-none {{ $product->is_active ? 'focus:ring-2 focus:ring-emerald-500 cursor-pointer' : 'cursor-not-allowed' }}">

                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-300 dark:text-slate-500" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif

                                    @if (!$product->is_active)
                                        <div
                                            class="absolute inset-0 bg-slate-900/60 flex items-center justify-center backdrop-blur-[2px]">
                                            <span
                                                class="text-white font-black text-[10px] uppercase tracking-widest border-y border-white/50 py-1 px-2">Habis</span>
                                        </div>
                                    @elseif($product->addons && $product->addons->count() > 0)
                                        <div
                                            class="absolute bottom-0 inset-x-0 bg-black/50 backdrop-blur-sm text-[9px] text-white text-center py-1 font-bold tracking-wider">
                                            OPSI MENU
                                        </div>
                                    @endif
                                </button>

                                <div class="flex-1 flex flex-col justify-between py-1">
                                    <div>
                                        <h3
                                            class="font-bold text-slate-900 dark:text-white leading-tight line-clamp-2 text-sm">
                                            {{ $product->name }}</h3>
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                                        <span
                                            class="font-black {{ $product->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500' }} text-sm">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>

                                        @if ($product->is_active)
                                            <button type="button"
                                                @if ($product->addons && $product->addons->count() > 0) @click="$dispatch('open-product-modal', { product: p, customerId: $wire.get('selectedCustomerId') })"
                                                @else
                                                    @click="$dispatch('add-to-cart', { productId: p.id, customerId: $wire.get('selectedCustomerId'), qty: 1, addons: [], note: '' })" @endif
                                                class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-white transition-all transform active:scale-90">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <button disabled
                                                class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-400 flex items-center justify-center cursor-not-allowed">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <livewire:customer.floating-cart />

    <div x-data="productModal()" @open-product-modal.window="open($event.detail)" x-show="show" x-cloak
        class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center">

        <div x-show="show" x-transition.opacity @click="close()"
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div x-show="show" x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-y-full sm:translate-y-10 sm:opacity-0"
            x-transition:enter-end="translate-y-0 sm:translate-y-0 sm:opacity-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 sm:translate-y-0 sm:opacity-100"
            x-transition:leave-end="translate-y-full sm:translate-y-10 sm:opacity-0"
            class="relative bg-white dark:bg-slate-900 w-full sm:max-w-md max-h-[90vh] rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden">

            <div class="w-full flex justify-center py-3 sm:hidden absolute top-0 z-10" @click="close()">
                <div class="w-12 h-1.5 bg-white/50 backdrop-blur-md rounded-full"></div>
            </div>

            <div class="w-full h-48 sm:h-56 bg-slate-100 dark:bg-slate-800 relative shrink-0">
                <template x-if="product?.image">
                    <img :src="'/storage/' + product.image" class="w-full h-full object-cover">
                </template>
                <button @click="close()"
                    class="absolute top-4 right-4 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center backdrop-blur-md hover:bg-black/70">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 no-scrollbar">
                <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-1 leading-tight"
                    x-text="product?.name"></h2>
                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mb-6"
                    x-text="product ? formatRupiah(product.price) : ''"></p>

                <template x-if="product?.addons && product.addons.length > 0">
                    <div class="mb-4">
                        <div
                            class="bg-slate-100 dark:bg-slate-800 px-4 py-2.5 -mx-5 mb-4 border-y border-slate-200 dark:border-slate-700">
                            <h3 class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                                Tambahan (Opsional)</h3>
                        </div>
                        <div class="space-y-3">
                            <template x-for="addon in product.addons" :key="addon.id">
                                <label
                                    class="flex items-center justify-between p-3.5 border border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" :checked="selectedAddons.some(a => a.id === addon.id)"
                                            @change="toggleAddon(addon)"
                                            class="w-5 h-5 text-emerald-500 rounded-md border-slate-300 dark:border-slate-600 focus:ring-emerald-500 bg-white dark:bg-slate-900">
                                        <span class="font-bold text-slate-700 dark:text-slate-300"
                                            x-text="addon.name"></span>
                                    </div>
                                    <span x-show="getAddonPrice(addon) > 0"
                                        class="text-sm font-black text-slate-500 dark:text-slate-400"
                                        x-text="'+ ' + formatRupiah(getAddonPrice(addon))"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="mt-2">
                    <div
                        class="bg-slate-100 dark:bg-slate-800 px-4 py-2.5 -mx-5 mb-4 border-y border-slate-200 dark:border-slate-700">
                        <h3 class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                            Catatan Khusus (Opsional)</h3>
                    </div>
                    <textarea x-model="note" rows="2" placeholder="Contoh: Pedas sedang, jangan pakai daun bawang..."
                        class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-emerald-500 focus:border-emerald-500 text-slate-700 dark:text-slate-300 placeholder-slate-400 resize-none"></textarea>
                </div>
            </div>

            <div class="p-5 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-bold text-slate-500">Jumlah Pesanan</span>
                    <div class="flex items-center gap-5">
                        <button @click="if(qty > 1) qty--"
                            class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-90 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4">
                                </path>
                            </svg>
                        </button>
                        <span class="font-black text-2xl text-slate-900 dark:text-white w-4 text-center"
                            x-text="qty"></span>
                        <button @click="qty++"
                            class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center hover:bg-emerald-200 dark:hover:bg-emerald-800 active:scale-90 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <button @click="addToCart()"
                    class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-lg rounded-2xl shadow-lg shadow-emerald-900/20 transition-transform active:scale-[0.98] flex justify-between px-6 items-center">
                    <span>Tambah Pesanan</span>
                    <span x-text="formatRupiah(totalPrice)"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productModal', () => ({
                show: false,
                product: null,
                customerId: null,
                qty: 1,
                selectedAddons: [],
                note: '',
                oldCartKey: null,

                open(detail) {
                    let data = detail[0] || detail;

                    this.product = data.product;
                    this.customerId = data.customerId;
                    this.qty = data.qty || 1;
                    this.note = data.note || '';
                    this.oldCartKey = data.oldCartKey || null;

                    if (data.selectedAddonIds && data.selectedAddonIds.length > 0 && this.product
                        .addons) {
                        this.selectedAddons = this.product.addons.filter(a => data.selectedAddonIds
                            .includes(a.id));
                    } else {
                        this.selectedAddons = [];
                    }

                    this.show = true;
                },
                close() {
                    this.show = false;
                    setTimeout(() => {
                        this.product = null;
                    }, 300);
                },
                toggleAddon(addon) {
                    const index = this.selectedAddons.findIndex(a => a.id === addon.id);
                    if (index > -1) {
                        this.selectedAddons.splice(index, 1);
                    } else {
                        this.selectedAddons.push(addon);
                    }
                },
                getAddonPrice(addon) {
                    let p = addon.price ?? addon.addon_price ?? addon.additional_price ?? addon.harga ??
                        0;
                    if (p === null || p === '') return 0;
                    return parseFloat(p) || 0;
                },
                get totalPrice() {
                    if (!this.product) return 0;
                    let base = parseFloat(this.product.price) || 0;
                    let addonTotal = this.selectedAddons.reduce((sum, a) => sum + this
                        .getAddonPrice(a), 0);
                    return (base + addonTotal) * this.qty;
                },
                formatRupiah(angka) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(angka);
                },
                addToCart() {
                    this.$dispatch('add-to-cart', {
                        productId: this.product.id,
                        customerId: this.customerId,
                        qty: this.qty,
                        addons: this.selectedAddons.map(a => ({
                            id: a.id,
                            name: a.name,
                            price: this.getAddonPrice(a),
                            cogs: a.cogs ?? a.addon_cogs ?? a.modal ?? 0
                        })),
                        note: this.note,
                        oldCartKey: this
                            .oldCartKey
                    });
                    this.close();
                }
            }));
        });
    </script>

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
</div>
