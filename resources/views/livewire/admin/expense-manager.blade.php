<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Catatan Pengeluaran</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Catat belanja operasional agar Laba Bersih akurat.
            </p>
        </div>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-between">
            <span class="font-bold text-emerald-800 dark:text-emerald-300">✅ {{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6">
                <h3
                    class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                    {{ $isEditMode ? 'Edit Pengeluaran' : 'Catat Pengeluaran Baru' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal</label>
                        <input wire:model="expense_date" type="date"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-emerald-500">
                        @error('expense_date')
                            <span class="text-xs text-red-500 font-bold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                        <select wire:model="category"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-emerald-500">
                            <option value="">-- Pilih --</option>
                            <option value="Bahan Baku Tambahan">Bahan Baku Tambahan (Beli di Pasar)</option>
                            <option value="Operasional">Operasional (Listrik, Air, WiFi)</option>
                            <option value="Gaji Karyawan">Gaji Karyawan</option>
                            <option value="Maintenance">Maintenance (Perbaikan Alat)</option>
                            <option value="Lain-lain">Lain-lain</option>
                        </select>
                        @error('category')
                            <span class="text-xs text-red-500 font-bold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nominal
                            (Rp)</label>
                        <input wire:model="amount" type="number"
                            class="w-full font-black text-rose-600 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:ring-rose-500"
                            placeholder="0">
                        @error('amount')
                            <span class="text-xs text-red-500 font-bold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan
                            Singkat</label>
                        <textarea wire:model="description" rows="2"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-emerald-500"
                            placeholder="Misal: Beli gas LPG 2 tabung"></textarea>
                        @error('description')
                            <span class="text-xs text-red-500 font-bold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-slate-900 dark:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl shadow-md">{{ $isEditMode ? 'Simpan' : 'Tambah' }}</button>
                        @if ($isEditMode)
                            <button type="button" wire:click="resetForm"
                                class="px-5 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-bold">Batal</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col h-full overflow-hidden">
                <div
                    class="p-5 border-b border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex gap-2">
                        <select wire:model.live="filterMonth"
                            class="rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-bold">
                            @for ($m = 1; $m <= 12; ++$m)
                                <option value="{{ sprintf('%02d', $m) }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                        <select wire:model.live="filterYear"
                            class="rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-bold">
                            @for ($y = date('Y'); $y >= 2023; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Total Bulan Ini</p>
                        <p class="text-xl font-black text-rose-600 dark:text-rose-400">Rp
                            {{ number_format($totalExpenses, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto flex-grow">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Keterangan
                                </th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Nominal</th>
                                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50 bg-white dark:bg-slate-800">
                            @forelse ($expenses as $exp)
                                <tr>
                                    <td class="px-5 py-3 text-sm text-slate-600 dark:text-slate-300">
                                        {{ \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3">
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ $exp->description }}</p>
                                        <p
                                            class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-700 inline-block px-2 py-0.5 rounded mt-1">
                                            {{ $exp->category }}</p>
                                    </td>
                                    <td
                                        class="px-5 py-3 text-right text-sm font-black text-rose-600 dark:text-rose-400">
                                        Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <button wire:click="edit({{ $exp->id }})"
                                            class="text-emerald-600 hover:text-emerald-800 mr-2 text-sm font-bold">Edit</button>
                                        <button wire:click="delete({{ $exp->id }})"
                                            wire:confirm="Hapus catatan ini?"
                                            class="text-red-500 hover:text-red-700 text-sm font-bold">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-slate-500 text-sm font-medium">Belum
                                        ada pengeluaran bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($expenses->hasPages())
                    <div class="p-4 border-t border-slate-200">{{ $expenses->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
