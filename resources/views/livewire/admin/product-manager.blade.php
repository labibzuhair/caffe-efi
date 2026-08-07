<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Daftar Produk</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Kelola menu, modal (HPP), dan varian rasa.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-between transition-all">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-500 rounded-full text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <span class="font-medium text-emerald-800 dark:text-emerald-300">{{ session('message') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-5">
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 sticky top-24">
                <h3
                    class="text-xl font-black text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                    {{ $isEditMode ? 'Edit Produk' : 'Tambah Produk Baru' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Foto Produk
                            (Opsional)</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file"
                                class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-2xl cursor-pointer bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors relative overflow-hidden group">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}"
                                        class="absolute inset-0 w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-sm font-bold">Ganti Foto</span>
                                    </div>
                                @elseif ($oldImage)
                                    <img src="{{ asset('storage/' . $oldImage) }}"
                                        class="absolute inset-0 w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-sm font-bold">Ganti Foto</span>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                            </path>
                                        </svg>
                                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Klik untuk
                                            upload</p>
                                    </div>
                                @endif
                                <input wire:model="image" id="dropzone-file" type="file" class="hidden"
                                    accept="image/*" />
                            </label>
                        </div>
                        <div wire:loading wire:target="image" class="text-xs text-emerald-500 font-bold mt-2">Mengunggah
                            preview...</div>
                        @error('image')
                            <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nama
                                Produk</label>
                            <input wire:model="name" type="text"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Kopi Susu">
                            @error('name')
                                <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                            <select wire:model="category_id"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">-- Pilih --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Harga Jual
                                Dasar</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm font-bold text-emerald-500 pointer-events-none">Rp</span>
                                <input wire:model="price" type="number"
                                    class="w-full pl-9 font-black text-emerald-600 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="0">
                            </div>
                            @error('price')
                                <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Modal Dasar /
                                HPP</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm font-bold text-amber-500 pointer-events-none">Rp</span>
                                <input wire:model="cogs" type="number"
                                    class="w-full pl-9 font-black text-amber-600 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-amber-500 focus:ring-amber-500"
                                    placeholder="0">
                            </div>
                            @error('cogs')
                                <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-900 dark:text-white">Varian / Opsi
                                    Tambahan</label>
                                <p
                                    class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 max-w-[200px] leading-relaxed">
                                    Tambahkan opsi untuk menu ini. Isi harga ekstra <strong
                                        class="text-emerald-500">0</strong> jika opsi tersebut gratis (Misal: Level
                                    Pedas).
                                </p>
                            </div>
                            <button type="button" wire:click="addAddon"
                                class="px-3 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-lg hover:bg-emerald-100 hover:text-emerald-700 dark:hover:bg-emerald-900/40 dark:hover:text-emerald-400 transition-colors shadow-sm flex items-center gap-1 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah Opsi
                            </button>
                        </div>

                        @if (count($addons) > 0)
                            <div class="space-y-4 max-h-80 overflow-y-auto pr-2 pb-2">
                                @foreach ($addons as $index => $addon)
                                    <div
                                        class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 relative shadow-sm">

                                        <button type="button" wire:click="removeAddon({{ $index }})"
                                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-100 text-red-500 hover:bg-red-500 hover:text-white dark:bg-red-900/50 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white rounded-full flex items-center justify-center transition-colors shadow-sm"
                                            title="Hapus Opsi">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3 pr-2">
                                            <div>
                                                <label
                                                    class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1 tracking-wider">Grup
                                                    Pilihan</label>
                                                <input wire:model="addons.{{ $index }}.category"
                                                    type="text"
                                                    class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"
                                                    placeholder="Mis: Level Pedas, Pilihan Susu">
                                                @error('addons.' . $index . '.category')
                                                    <span
                                                        class="text-[10px] text-red-500 font-bold block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1 tracking-wider">Nama
                                                    Opsi</label>
                                                <input wire:model="addons.{{ $index }}.name" type="text"
                                                    class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500"
                                                    placeholder="Mis: Pedas Mampus, Oat Milk">
                                                @error('addons.' . $index . '.name')
                                                    <span
                                                        class="text-[10px] text-red-500 font-bold block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pr-2">
                                            <div>
                                                <label
                                                    class="block text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase mb-1 tracking-wider">Harga
                                                    Tambahan (+)</label>
                                                <div class="relative">
                                                    <span
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-emerald-500 pointer-events-none">Rp</span>
                                                    <input wire:model="addons.{{ $index }}.additional_price"
                                                        type="number"
                                                        class="w-full pl-8 text-xs font-black text-emerald-600 rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 focus:ring-emerald-500 focus:border-emerald-500"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase mb-1 tracking-wider">Modal
                                                    Tambahan (+)</label>
                                                <div class="relative">
                                                    <span
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-amber-500 pointer-events-none">Rp</span>
                                                    <input wire:model="addons.{{ $index }}.additional_cogs"
                                                        type="number"
                                                        class="w-full pl-8 text-xs font-black text-amber-600 rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 focus:ring-amber-500 focus:border-amber-500"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="text-center py-6 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-white dark:bg-slate-800/50">
                                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500">Menu ini belum
                                    memiliki varian tambahan.</p>
                            </div>
                        @endif
                    </div>

                    <div
                        class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-900 dark:text-white">Menu Tersedia?</span>
                        <button type="button" wire:click="$toggle('is_active')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 {{ $is_active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}">
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                    <div class="pt-2 flex gap-3">
                        @if ($isEditMode)
                            <button type="button" wire:click="resetForm"
                                class="px-5 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-colors">Batal</button>
                        @endif
                        <button type="submit"
                            class="flex-1 bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white font-black py-3 px-4 rounded-xl transition-all shadow-md flex justify-center items-center gap-2">
                            <span wire:loading.remove
                                wire:target="save">{{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Produk Baru' }}</span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-7">
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden flex flex-col h-full">

                <div
                    class="p-4 border-b border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex flex-col sm:flex-row gap-3 w-full">
                        <select wire:model.live="filterCategory"
                            class="w-full sm:w-48 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500 dark:text-slate-200 cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>

                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="text"
                                class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-medium focus:ring-emerald-500 focus:border-emerald-500 dark:text-slate-200"
                                placeholder="Cari nama menu...">
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto flex-grow">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th
                                    class="px-5 py-3 text-left text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Produk</th>
                                <th
                                    class="px-5 py-3 text-left text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Keuangan</th>
                                <th
                                    class="px-5 py-3 text-center text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Tersedia</th>
                                <th
                                    class="px-5 py-3 text-right text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50 bg-white dark:bg-slate-800">
                            @forelse ($products as $prod)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-12 w-12 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center">
                                                @if ($prod->image)
                                                    <img class="h-12 w-12 object-cover"
                                                        src="{{ asset('storage/' . $prod->image) }}" alt="">
                                                @else
                                                    <svg class="w-6 h-6 text-slate-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-slate-900 dark:text-white">
                                                    {{ $prod->name }}</div>
                                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                                    {{ $prod->category->name }}</div>
                                                @if ($prod->addons->count() > 0)
                                                    <span
                                                        class="inline-block mt-1 px-2 py-0.5 bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 text-[10px] font-bold rounded-md">
                                                        {{ $prod->addons->count() }} Varian
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <div class="text-sm font-black text-emerald-600 dark:text-emerald-400">Jual: Rp
                                            {{ number_format($prod->price, 0, ',', '.') }}</div>
                                        <div class="text-xs font-bold text-amber-600 dark:text-amber-500 mt-0.5">HPP:
                                            Rp {{ number_format($prod->cogs, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <button type="button" wire:click="toggleStatus({{ $prod->id }})"
                                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 {{ $prod->is_active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}">
                                                <span
                                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $prod->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3">

                                            <button wire:click="edit({{ $prod->id }})"
                                                onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
                                                class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg transition-colors"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                            </button>

                                            <button wire:click="delete({{ $prod->id }})"
                                                wire:confirm="Yakin ingin menghapus {{ $prod->name }}?"
                                                class="p-2 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-500 rounded-lg transition-colors"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tidak ada
                                            menu yang ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div
                        class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/50">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
