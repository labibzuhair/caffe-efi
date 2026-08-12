<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Manajemen Meja</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Kelola daftar meja dan cetak QR Code untuk pesanan
                pelanggan.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-between">
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

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-red-500 rounded-full text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="font-medium text-red-800 dark:text-red-300">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-red-600 hover:text-red-800 dark:text-red-400">
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
                    {{ $isEditMode ? 'Edit Meja' : 'Tambah Meja Baru' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nomor / Nama
                            Meja</label>
                        <input wire:model="table_number" type="text"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Contoh: Meja 01 / VIP 2">
                        @error('table_number')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kondisi
                            Meja</label>
                        <select wire:model="status"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="available">🟢 Tersedia (Bisa digunakan)</option>
                            <option value="maintenance">⚪ Perbaikan (Ditutup)</option>
                        </select>
                        @error('status')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-4 rounded-xl transition-colors shadow-sm flex justify-center items-center">
                            <span wire:loading.remove
                                wire:target="save">{{ $isEditMode ? 'Perbarui' : 'Simpan' }}</span>
                            <span wire:loading wire:target="save">Memproses...</span>
                        </button>

                        @if ($isEditMode)
                            <button type="button" wire:click="resetForm"
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-colors">Batal</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">

            <div
                class="mb-6 bg-white dark:bg-slate-800 rounded-2xl p-2 border border-slate-200 dark:border-slate-700/50 shadow-sm flex items-center">
                <div class="pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="w-full pl-3 pr-4 py-2 bg-transparent border-none focus:ring-0 text-slate-900 dark:text-slate-100 placeholder-slate-400"
                    placeholder="Cari nomor atau nama meja...">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($tables as $table)
                    <div
                        class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/60 dark:border-slate-700/50 shadow-sm flex flex-col group hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden">

                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <h4 class="text-xl font-black text-slate-900 dark:text-white">{{ $table->table_number }}
                            </h4>

                            @if ($table->status === 'available')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 dark:border-emerald-800/50 dark:bg-emerald-900/30 dark:text-emerald-400">TERSEDIA</span>
                            @elseif($table->status === 'occupied')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200 dark:border-orange-800/50 dark:bg-orange-900/30 dark:text-orange-400">DIPAKAI</span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300">DITUTUP</span>
                            @endif
                        </div>

                        <div class="flex-grow flex flex-col items-center justify-center mb-4 relative z-10">
                            <div id="qr-table-{{ $table->id }}"
                                class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center relative {{ $table->status === 'maintenance' ? 'opacity-30 grayscale' : '' }}">
                                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->margin(1)->style('round')->generate($this->getQrCodeUrl($table->qr_token)) !!}
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-4 gap-1 border-t border-slate-100 dark:border-slate-700/50 pt-4 mt-auto relative z-10">

                            <button type="button"
                                onclick="window.printQR('qr-table-{{ $table->id }}', '{{ $table->table_number }}', '{{ $table->qr_token }}')"
                                class="flex flex-col items-center justify-center text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 transition-colors"
                                title="Cetak QR">
                                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                <span class="text-[9px] font-bold">Cetak</span>
                            </button>

                            <button type="button" wire:click="toggleStatus({{ $table->id }})"
                                class="flex flex-col items-center justify-center text-slate-500 hover:text-purple-600 dark:text-slate-400 dark:hover:text-purple-400 transition-colors {{ $table->status === 'occupied' ? 'opacity-30 cursor-not-allowed' : '' }}"
                                title="{{ $table->status === 'available' ? 'Tutup Meja' : 'Buka Meja' }}">
                                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span
                                    class="text-[9px] font-bold">{{ $table->status === 'available' ? 'Tutup' : 'Buka' }}</span>
                            </button>

                            <button type="button" wire:click="edit({{ $table->id }})"
                                class="flex flex-col items-center justify-center text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 transition-colors {{ $table->status === 'occupied' ? 'opacity-30 cursor-not-allowed' : '' }}"
                                title="Edit Meja">
                                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                <span class="text-[9px] font-bold">Edit</span>
                            </button>

                            <button type="button" wire:click="delete({{ $table->id }})"
                                wire:confirm="Yakin ingin menghapus {{ $table->table_number }}?"
                                class="flex flex-col items-center justify-center text-slate-500 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-400 transition-colors {{ $table->status === 'occupied' ? 'opacity-30 cursor-not-allowed' : '' }}"
                                title="Hapus Meja">
                                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                <span class="text-[9px] font-bold">Hapus</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-1 sm:col-span-2 lg:col-span-3 bg-white dark:bg-slate-800 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-700/50 shadow-sm">
                        <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada meja yang terdaftar.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $tables->links() }}
            </div>

        </div>
    </div>

    <script>
        window.printQR = function(elementId, tableName, qrToken) {
            const qrElement = document.getElementById(elementId);
            if (!qrElement) return;

            const qrSvg = qrElement.innerHTML;
            const printWindow = window.open('', '_blank', 'width=500,height=700');

            if (!printWindow) {
                alert('Tolong izinkan popup browser Anda untuk mencetak QR.');
                return;
            }

            const storeName = @js($storeName);

            const baseUrl = window.location.origin;
            const cleanUrl = baseUrl.replace(/^https?:\/\//, '');
            const shortUrl = `${cleanUrl}/meja/${qrToken}`;

            let htmlContent = '<!DOCTYPE html>';
            htmlContent += '<html lang="id">';
            htmlContent += '<head>';
            htmlContent += '<meta charset="UTF-8">';
            htmlContent += '<title>Cetak QR - ' + tableName + '<' + '/title>';
            htmlContent +=
                '<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800;900&display=swap" rel="stylesheet">';

            htmlContent += '<style>';
            htmlContent +=
                'body { font-family: "Plus Jakarta Sans", sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f8fafc; color: #0f172a; -webkit-print-color-adjust: exact; print-color-adjust: exact; } ';
            htmlContent +=
                '.print-card { background: white; width: 380px; padding: 0; border-radius: 32px; box-shadow: 0 20px 40px -15px rgba(0,0,0,0.1); overflow: hidden; border: 2px solid #e2e8f0; display: flex; flex-direction: column; } ';
            htmlContent +=
                '.header { background: #059669; color: white; padding: 24px 20px; text-align: center; border-bottom: 6px solid #047857; } ';
            htmlContent +=
                '.header h2 { margin: 0; font-size: 16px; font-weight: 700; opacity: 0.9; text-transform: uppercase; letter-spacing: 2px; } ';
            htmlContent += '.header h1 { margin: 5px 0 0 0; font-size: 42px; font-weight: 900; } ';
            htmlContent +=
                '.qr-area { padding: 40px 30px 20px 30px; display: flex; flex-direction: column; align-items: center; background: #ffffff; } ';
            htmlContent +=
                '.qr-container { padding: 15px; border: 3px dashed #cbd5e1; border-radius: 24px; background: #fff; margin-bottom: 24px; } ';
            htmlContent += '.qr-container svg { display: block; width: 180px; height: 180px; } ';
            htmlContent += '.instruction { text-align: center; margin-bottom: 25px; } ';
            htmlContent += '.instruction p { margin: 0; font-size: 20px; font-weight: 800; color: #1e293b; } ';
            htmlContent +=
                '.instruction span { font-size: 13px; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 6px 12px; border-radius: 20px; display: inline-block; margin-top: 10px; } ';
            htmlContent +=
                '.footer { background: #f8fafc; padding: 20px; border-top: 2px dashed #e2e8f0; text-align: center; } ';
            htmlContent += '.footer p { margin: 0 0 8px 0; font-size: 12px; font-weight: 700; color: #64748b; } ';
            htmlContent +=
                '.url-box { background: white; border: 1px solid #cbd5e1; padding: 10px; border-radius: 12px; font-family: monospace; font-size: 14px; font-weight: 800; color: #0f172a; letter-spacing: 0.5px; } ';
            htmlContent +=
                '.brand { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-top: 15px; display: block; }';
            htmlContent +=
                '@media print { body { background: white; } .print-card { box-shadow: none; border: 2px solid #000; border-radius: 20px; } .header { -webkit-print-color-adjust: exact; background: #059669 !important; border-bottom: 4px solid #047857 !important; } .qr-container { border-color: #000; } }';
            htmlContent += '<' + '/style>';
            htmlContent += '<' + '/head>';

            htmlContent += '<body>';
            htmlContent += '<div class="print-card">';

            htmlContent += '<div class="header">';
            htmlContent += '<h2>Scan untuk Memesan</h2>';
            htmlContent += '<h1>' + tableName + '<' + '/h1>';
            htmlContent += '<' + '/div>';

            htmlContent += '<div class="qr-area">';
            htmlContent += '<div class="qr-container">' + qrSvg + '<' + '/div>';
            htmlContent += '<div class="instruction">';
            htmlContent += '<p>Pesan Menu Di Meja Ini??<br>Arahkan Kamera HP Anda<br>ke QR Code di atas ☝️<' + '/p>';
            htmlContent += '<span>Tanpa perlu download aplikasi!<' + '/span>';
            htmlContent += '<' + '/div>';
            htmlContent += '<' + '/div>';

            htmlContent += '<div class="footer">';
            htmlContent += '<p>Kamera bermasalah? Ketik link ini di browser Anda:<' + '/p>';
            htmlContent += '<div class="url-box">' + shortUrl + '<' + '/div>';
            htmlContent += '<span class="brand">' + storeName + ' System<' + '/span>';
            htmlContent += '<' + '/div>';

            htmlContent += '<' + '/div>';
            htmlContent += '<' + '/body>';
            htmlContent += '<' + '/html>';

            printWindow.document.write(htmlContent);
            printWindow.document.close();

            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.print();
                    printWindow.close();
                }, 800);
            };
        };
    </script>
</div>
