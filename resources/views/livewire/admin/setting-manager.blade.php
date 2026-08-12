<div x-data="{ showHelpModal: false }" class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 relative">

    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Pengaturan Toko & Profil</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Atur identitas digital, kontak, lokasi GPS, sosial
            media, dan pengaturan pajak kafe Anda.</p>
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
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">

        <div
            class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-8">
            <h3
                class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                1. Identitas Utama & Sistem</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Logo Kafe</label>
                    <label
                        class="flex flex-col items-center justify-center w-full aspect-square border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-2xl cursor-pointer bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 transition-colors relative overflow-hidden group">
                        @if ($newLogo)
                            <img src="{{ $newLogo->temporaryUrl() }}"
                                class="absolute inset-0 w-full h-full object-contain p-2">
                        @elseif ($logo)
                            <img src="{{ asset('storage/' . $logo) }}"
                                class="absolute inset-0 w-full h-full object-contain p-2">
                        @else
                            <div class="flex flex-col items-center justify-center p-4 text-center">
                                <svg class="w-8 h-8 text-slate-400 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="text-xs text-slate-500 font-bold">Upload Logo</span>
                            </div>
                        @endif
                        <input wire:model="newLogo" type="file" class="hidden" accept="image/*" />
                    </label>
                    <div wire:loading wire:target="newLogo" class="text-xs text-emerald-500 mt-1 font-bold">Memuat
                        preview...</div>
                </div>

                <div class="md:col-span-2 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kafe /
                            Resto</label>
                        <input wire:model="store_name" type="text"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Pajak / PB1
                            (%)</label>
                        <div class="relative">
                            <input wire:model="tax_percentage" type="number" step="0.01"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Contoh: 11">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <span class="text-slate-500 font-bold">%</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Teks Footer (Hak
                            Cipta)</label>
                        <input wire:model="footer_text" type="text"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Contoh: Hak Cipta © 2026 CaffePOS.">
                    </div>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-8">
            <h3
                class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                2. Kontak & Lokasi Geofencing</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Telepon /
                        WhatsApp</label>
                    <input wire:model="contact_phone" type="text"
                        class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                        placeholder="Contoh: 08123456789">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Email Resmi</label>
                    <input wire:model="contact_email" type="email"
                        class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                        placeholder="hello@caffepos.com">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap
                        Kafe</label>
                    <textarea wire:model="address" rows="2"
                        class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-emerald-500 focus:ring-emerald-500"
                        placeholder="Jl. Sudirman No.1, Jakarta..."></textarea>
                </div>

                <div class="md:col-span-2 border-t border-dashed border-slate-200 dark:border-slate-700 pt-4"
                    x-data="{
                        isLoading: false,
                        getAdminLocation() {
                            this.isLoading = true;
                            if (!navigator.geolocation) {
                                alert('Browser Anda tidak mendukung GPS.');
                                this.isLoading = false;
                                return;
                            }
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    $wire.set('latitude', position.coords.latitude);
                                    $wire.set('longitude', position.coords.longitude);
                                    this.isLoading = false;
                                    alert('Berhasil mengunci titik pusat Kafe!');
                                },
                                (error) => {
                                    this.isLoading = false;
                                    alert('Gagal membaca GPS. Mohon izinkan akses lokasi. Klik ikon (?) di sebelah tombol pelacak untuk panduan lengkap.');
                                }, { enableHighAccuracy: true, timeout: 10000 }
                            );
                        }
                    }">

                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Pengaturan Pembatasan
                            Lokasi Pemesanan (Geofencing)</label>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="getAdminLocation()" :disabled="isLoading"
                                class="text-xs bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 font-bold px-3 py-1.5 rounded-lg hover:bg-emerald-200 flex items-center gap-1 transition-colors disabled:opacity-50">
                                <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <svg x-cloak x-show="isLoading" class="animate-spin w-4 h-4"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="isLoading ? 'Melacak...' : 'Gunakan Lokasi Saya Saat Ini'"></span>
                            </button>

                            <button type="button" @click.prevent="showHelpModal = true"
                                class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-bold text-sm shadow-sm transition-all"
                                title="Bantuan Izin Lokasi">
                                ?
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Titik Latitude</label>
                            <input wire:model="latitude" type="text"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-emerald-500"
                                placeholder="-7.xxxxx">
                            @error('latitude')
                                <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Titik Longitude</label>
                            <input wire:model="longitude" type="text"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-emerald-500"
                                placeholder="110.xxxxx">
                            @error('longitude')
                                <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Toleransi Jarak / Radius</label>
                            <div class="relative">
                                <input wire:model="max_distance" type="number"
                                    class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-emerald-500"
                                    placeholder="150">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-slate-400 text-xs font-bold">Meter</span>
                                </div>
                            </div>
                            @error('max_distance')
                                <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div
                        class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                        <p class="text-[11px] text-blue-600 dark:text-blue-400 font-medium leading-relaxed">
                            <strong class="font-black">💡 Tips Akurasi:</strong> Untuk hasil paling presisi, disarankan
                            menyalin titik Latitude & Longitude langsung dari <b>Google Maps</b>. Jika menggunakan
                            tombol pelacak otomatis, pastikan Anda menekannya melalui <b>Smartphone (HP)</b> saat
                            berdiri di area kafe, karena pelacakan via Komputer/Laptop seringkali kurang akurat.
                        </p>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2">Atur Radius antara 10 - 5000 meter. Pelanggan di luar
                        radius ini tidak akan bisa memindai QR Code Meja.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-8">
                <h3
                    class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                    3. Tautan Sosial Media</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">Instagram
                            (Username/URL)</label>
                        <input wire:model="social_media.instagram" type="text"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">Tiktok
                            (Username/URL)</label>
                        <input wire:model="social_media.tiktok" type="text"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">Facebook</label>
                        <input wire:model="social_media.facebook" type="text"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-400 mb-1">YouTube
                            Channel</label>
                        <input wire:model="social_media.youtube" type="text"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-8">
                <h3
                    class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                    4. SEO Thumbnail Cover</h3>
                <label
                    class="flex flex-col items-center justify-center w-full aspect-[2/1] border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-2xl cursor-pointer bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 transition-colors relative overflow-hidden group">
                    @if ($newSeoThumbnail)
                        <img src="{{ $newSeoThumbnail->temporaryUrl() }}"
                            class="absolute inset-0 w-full h-full object-cover">
                    @elseif ($seo_thumbnail)
                        <img src="{{ asset('storage/' . $seo_thumbnail) }}"
                            class="absolute inset-0 w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center p-4 text-center">
                            <svg class="w-8 h-8 text-slate-400 mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-xs text-slate-500 font-bold">Upload Cover SEO</span>
                        </div>
                    @endif
                    <input wire:model="newSeoThumbnail" type="file" class="hidden" accept="image/*" />
                </label>
                <div wire:loading wire:target="newSeoThumbnail"
                    class="text-xs text-emerald-500 mt-2 font-bold text-center w-full">Memuat gambar...</div>
            </div>
        </div>

        <div class="sticky bottom-6 z-40 flex justify-end">
            <button type="submit"
                class="bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold py-3 px-8 rounded-full transition-all shadow-lg hover:shadow-emerald-500/50 hover:-translate-y-1 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                    </path>
                </svg>
                <span wire:loading.remove wire:target="save">Simpan Semua Pengaturan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </form>

    <div x-cloak x-show="showHelpModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div @click.away="showHelpModal = false"
            class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]"
            x-transition:enter="transition ease-out duration-300 delay-100"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 scale-95">

            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                    <span
                        class="bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400 rounded-full w-6 h-6 inline-flex items-center justify-center text-sm">?</span>
                    Cara Mengizinkan Lokasi
                </h3>
                <button @click="showHelpModal = false"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-0 overflow-y-auto" x-data="{ tab: 'desktop' }">

                <div class="flex border-b border-slate-100 dark:border-slate-700">
                    <button @click="tab = 'desktop'"
                        :class="tab === 'desktop' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-bold' :
                            'text-slate-500 font-medium hover:bg-slate-50 dark:hover:bg-slate-700'"
                        class="flex-1 py-3 text-sm text-center transition-colors">
                        💻 Laptop/PC
                    </button>
                    <button @click="tab = 'android'"
                        :class="tab === 'android' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-bold' :
                            'text-slate-500 font-medium hover:bg-slate-50 dark:hover:bg-slate-700'"
                        class="flex-1 py-3 text-sm text-center transition-colors">
                        🤖 Android
                    </button>
                    <button @click="tab = 'ios'"
                        :class="tab === 'ios' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-bold' :
                            'text-slate-500 font-medium hover:bg-slate-50 dark:hover:bg-slate-700'"
                        class="flex-1 py-3 text-sm text-center transition-colors">
                        🍎 iPhone/iOS
                    </button>
                </div>

                <div class="p-6">
                    <div x-show="tab === 'desktop'" class="space-y-4 text-sm text-slate-600 dark:text-slate-300">
                        <h4 class="font-bold text-slate-800 dark:text-white">Chrome / Edge / Firefox:</h4>
                        <ol class="list-decimal pl-5 space-y-2 mb-4">
                            <li>Klik ikon <b>Gembok 🔒</b> atau ikon pengaturan di sebelah kiri alamat situs (URL) di
                                bagian atas browser.</li>
                            <li>Cari opsi <b>Lokasi (Location)</b>.</li>
                            <li>Ubah pengaturannya dari "Blokir" menjadi <b>Izinkan (Allow)</b>.</li>
                            <li>Muat Ulang (Refresh) halaman ini dan coba lacak kembali.</li>
                        </ol>

                        <h4 class="font-bold text-slate-800 dark:text-white">Safari (Mac):</h4>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Klik menu <b>Safari</b> di bar atas layar, lalu pilih <b>Preferences (Pengaturan)</b>.
                            </li>
                            <li>Pilih tab <b>Websites</b>.</li>
                            <li>Di bilah kiri, klik <b>Location</b>.</li>
                            <li>Cari nama situs ini di sebelah kanan, dan ubah opsinya menjadi <b>Allow (Izinkan)</b>.
                            </li>
                        </ol>
                    </div>

                    <div x-show="tab === 'android'" class="space-y-4 text-sm text-slate-600 dark:text-slate-300"
                        style="display: none;">
                        <h4 class="font-bold text-slate-800 dark:text-white">Jika menggunakan Google Chrome:</h4>
                        <ol class="list-decimal pl-5 space-y-3">
                            <li>Ketuk ikon <b>Gembok 🔒</b> atau ikon pengaturan di sebelah kiri alamat web (URL) di
                                bagian atas layar.</li>
                            <li>Pilih menu <b>Izin (Permissions)</b>.</li>
                            <li>Cari bagian <b>Lokasi (Location)</b>.</li>
                            <li>Aktifkan sakelar atau ubah menjadi <b>Izinkan (Allow)</b>.</li>
                            <li>Tutup menu tersebut, lalu coba tekan lacak kembali.</li>
                        </ol>
                        <p class="mt-4 text-xs italic text-slate-500">Pastikan juga fitur GPS / Lokasi pada panel
                            pengaturan cepat HP Anda dalam keadaan Menyala.</p>
                    </div>

                    <div x-show="tab === 'ios'" class="space-y-4 text-sm text-slate-600 dark:text-slate-300"
                        style="display: none;">
                        <h4 class="font-bold text-slate-800 dark:text-white">Jika menggunakan Safari:</h4>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Buka <b>Pengaturan (Settings)</b> iPhone Anda.</li>
                            <li>Pilih menu <b>Privasi & Keamanan (Privacy & Security)</b>.</li>
                            <li>Pilih <b>Layanan Lokasi (Location Services)</b>.</li>
                            <li>Scroll ke bawah, cari dan pilih <b>Safari Websites</b>.</li>
                            <li>Ubah centang menjadi <b>Saat Menggunakan Aplikasi (While Using the App)</b>.</li>
                            <li>Kembali ke browser dan coba lacak lokasi lagi.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div
                class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-right">
                <button @click="showHelpModal = false" type="button"
                    class="px-5 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold rounded-xl transition-colors shadow-sm text-sm">
                    Mengerti, Tutup Panduan
                </button>
            </div>
        </div>
    </div>
</div>
