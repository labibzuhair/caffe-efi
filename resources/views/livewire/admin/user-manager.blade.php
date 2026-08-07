<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Manajemen Staf</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Kelola akses akun Kasir, Dapur, dan Admin untuk
                sistem CaffePOS.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-between">
            <span class="font-medium text-emerald-800 dark:text-emerald-300">{{ session('message') }}</span>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800"><svg class="w-5 h-5"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 flex items-center justify-between">
            <span class="font-medium text-red-800 dark:text-red-300">{{ session('error') }}</span>
            <button @click="show = false" class="text-red-600 hover:text-red-800"><svg class="w-5 h-5" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 sticky top-24">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">
                    {{ $isEditMode ? 'Edit Akun Staf' : 'Tambah Akun Baru' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama
                            Lengkap</label>
                        <input wire:model="name" type="text"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:ring-emerald-500">
                        @error('name')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email (Untuk
                            Login)</label>
                        <input wire:model="email" type="email"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:ring-emerald-500">
                        @error('email')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Peran
                            (Role)</label>
                        <select wire:model="role"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:ring-emerald-500">
                            <option value="kasir">👨‍🍳 Kasir (Akses POS Utama)</option>
                            <option value="dapur">🍳 Dapur (Akses Pesanan Masuk)</option>
                            <option value="admin">👑 Admin (Akses Penuh)</option>
                        </select>
                        @error('role')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            Password {{ $isEditMode ? '(Isi jika ingin diubah)' : '' }}
                        </label>
                        <input wire:model="password" type="password"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:ring-emerald-500">
                        @error('password')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-4 rounded-xl transition-colors">
                            {{ $isEditMode ? 'Perbarui' : 'Simpan' }}
                        </button>
                        @if ($isEditMode)
                            <button type="button" wire:click="resetForm"
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-colors">Batal</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden flex flex-col h-full">

                <div
                    class="p-4 border-b border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-between">
                    <h4 class="font-bold text-slate-800 dark:text-slate-200">Daftar Pengguna</h4>
                    <div class="w-1/2 relative">
                        <input wire:model.live.debounce.300ms="search" type="text"
                            class="w-full pl-4 pr-4 py-2 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-emerald-500"
                            placeholder="Cari nama atau email...">
                    </div>
                </div>

                <div class="overflow-x-auto flex-grow">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">
                                    Nama & Email</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">
                                    Role</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50 bg-white dark:bg-slate-800">
                            @foreach ($users as $user)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ $user->name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->role === 'admin')
                                            <span
                                                class="px-2 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-md text-[10px] font-bold uppercase">👑
                                                Admin</span>
                                        @elseif($user->role === 'dapur')
                                            <span
                                                class="px-2 py-1 bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded-md text-[10px] font-bold uppercase">🍳
                                                Dapur</span>
                                        @else
                                            <span
                                                class="px-2 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-md text-[10px] font-bold uppercase">👨‍🍳
                                                Kasir</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="edit({{ $user->id }})"
                                            class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 mr-3">Edit</button>
                                        <button wire:click="delete({{ $user->id }})"
                                            wire:confirm="Hapus akun {{ $user->name }}?"
                                            class="text-red-500 hover:text-red-700">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
