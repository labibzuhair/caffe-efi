<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Kategori Menu</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Kelola daftar kategori untuk mengelompokkan produk
                di katalog CaffePOS.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-between transition-all duration-500">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-500 rounded-full text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <span class="font-medium text-emerald-800 dark:text-emerald-300">{{ session('message') }}</span>
            </div>
            <button @click="show = false"
                class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 sticky top-24">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">
                    {{ $isEditMode ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama
                            Kategori</label>
                        <input wire:model="name" type="text"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500 transition-colors"
                            placeholder="Contoh: Kopi Susu">
                        @error('name')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <span class="flex-grow flex flex-col">
                            <span class="text-sm font-medium text-slate-900 dark:text-white">Status Aktif</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Tampilkan di katalog publik?</span>
                        </span>
                        <button type="button" wire:click="$toggle('is_active')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 {{ $is_active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}">
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-4 rounded-xl transition-colors shadow-sm hover:shadow-emerald-500/30 flex justify-center items-center">
                            <span wire:loading.remove
                                wire:target="save">{{ $isEditMode ? 'Perbarui' : 'Simpan' }}</span>
                            <span wire:loading wire:target="save">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </button>

                        @if ($isEditMode)
                            <button type="button" wire:click="resetForm"
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-colors">
                                Batal
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Nama Kategori</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-4 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50 bg-white dark:bg-slate-800">
                            @forelse ($categories as $cat)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ $cat->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button wire:click="toggleStatus({{ $cat->id }})"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold cursor-pointer transition-colors {{ $cat->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 hover:bg-emerald-200' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300 hover:bg-slate-200' }}">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $cat->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-500' }}"></span>
                                            {{ $cat->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3">
                                            <button wire:click="edit({{ $cat->id }})"
                                                class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors">
                                                Edit
                                            </button>

                                            <button wire:click="delete({{ $cat->id }})"
                                                wire:confirm="Yakin ingin menghapus kategori {{ $cat->name }}? Ini tidak bisa dikembalikan."
                                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center">
                                        <div
                                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-900 mb-4">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                </path>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Belum ada
                                            kategori yang ditambahkan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($categories->hasPages())
                    <div
                        class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/50">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
