@php
    $setting = \App\Models\Setting::first();
    $storeName = $setting->store_name ?? 'CaffePOS';
@endphp
<div x-data="{ bgStatus: 'checking', showHelpModal: false }" @gps-status.window="bgStatus = $event.detail"
    class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 p-4 relative overflow-hidden transition-colors duration-500">

    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-3xl h-[500px] opacity-30 pointer-events-none">
        <div class="absolute inset-0 rounded-full blur-[120px] mix-blend-multiply dark:mix-blend-screen transform -translate-y-1/2 transition-opacity duration-700 bg-gradient-to-b from-amber-400 to-yellow-500 animate-pulse"
            :class="bgStatus === 'checking' ? 'opacity-100 z-10' : 'opacity-0 z-0'"></div>
        <div class="absolute inset-0 rounded-full blur-[120px] mix-blend-multiply dark:mix-blend-screen transform -translate-y-1/2 transition-opacity duration-700 bg-gradient-to-b from-emerald-400 to-teal-500"
            :class="bgStatus === 'success' ? 'opacity-100 z-10' : 'opacity-0 z-0'"></div>
        <div class="absolute inset-0 rounded-full blur-[120px] mix-blend-multiply dark:mix-blend-screen transform -translate-y-1/2 transition-opacity duration-700 bg-gradient-to-b from-rose-500 to-red-600"
            :class="bgStatus === 'error' ? 'opacity-100 z-10' : 'opacity-0 z-0'"></div>
    </div>

    <div
        class="w-full max-w-md bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50 dark:border-slate-700/50 p-8 relative z-10 transform transition-all">

        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 mb-6 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-2">
                {{ $table->table_number }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Selamat datang! Masukkan nama panggilan
                Anda untuk bergabung dalam pesanan meja ini.</p>
        </div>

        <form wire:submit.prevent="joinTable" class="space-y-6">

            @if (count($existingNames) > 0)
                <div class="mb-4 animate-fade-in-up">
                    <label
                        class="block text-xs font-black text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Lanjutkan
                        sebagai:</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($existingNames as $name)
                            <button type="button" wire:click="$set('displayName', '{{ $name }}')"
                                class="px-4 py-2 rounded-xl text-sm font-bold transition-all border-2"
                                :class="$wire.displayName === '{{ $name }}' ?
                                    'bg-emerald-50 border-emerald-500 text-emerald-700 dark:bg-emerald-900/40 dark:border-emerald-400 dark:text-emerald-300 shadow-sm' :
                                    'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100 dark:bg-slate-800/50 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800'">
                                {{ $name }}
                            </button>
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center">
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                        <span class="shrink-0 px-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Atau
                            Ketik Baru</span>
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    </div>
                </div>
            @endif

            <div>
                <label for="displayName" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama
                    Panggilan</label>
                <input wire:model="displayName" type="text" id="displayName"
                    class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm transition-shadow py-3 px-4"
                    placeholder="Contoh: Budi, Andi, Kak Siti..." autocomplete="off">
                @error('displayName')
                    <span class="text-xs text-red-500 mt-2 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex flex-col gap-2" x-data="{
                status: 'checking',
                isBlocked: false,

                init() {
                    if (this.$wire.get('latitude') && this.$wire.get('longitude')) {
                        this.setStatus('success');
                    } else {
                        this.getLocation();
                    }
                },

                setStatus(newStatus) {
                    this.status = newStatus;
                    this.$dispatch('gps-status', newStatus);
                },

                retry() {
                    this.setStatus('checking');
                    this.$wire.set('locationError', '');
                    setTimeout(() => { this.getLocation(); }, 800);
                },

                getLocation() {
                    if (!navigator.geolocation) {
                        this.setStatus('error');
                        this.$wire.set('locationError', 'Browser Anda tidak mendukung GPS.');
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.setStatus('success');
                            this.isBlocked = false;
                            this.$wire.set('latitude', position.coords.latitude);
                            this.$wire.set('longitude', position.coords.longitude);
                        },
                        (error) => {
                            this.setStatus('error');
                            this.isBlocked = false;
                            let errorMsg = 'Akses lokasi ditolak atau bermasalah.';

                            if (error.code === 1) {
                                this.isBlocked = true;
                                errorMsg = 'Akses GPS Ditolak. Klik ikon tanda tanya (?) di bawah untuk panduan membuka blokir.';
                            } else if (error.code === 2) {
                                errorMsg = 'Sinyal satelit tidak ditemukan. Pastikan GPS/Lokasi HP Anda menyala.';
                            } else if (error.code === 3) {
                                errorMsg = 'Pencarian terlalu lama. Sinyal mungkin terhalang atap ruangan.';
                            }

                            this.$wire.set('locationError', errorMsg);
                        }, { enableHighAccuracy: false, timeout: 10000, maximumAge: 60000 }
                    );
                }
            }" @location-rejected.window="setStatus('error')">

                <div class="rounded-xl p-4 text-sm font-medium border transition-colors duration-300"
                    :class="{
                        'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-900/30 dark:border-amber-800/50 dark:text-amber-400': status === 'checking',
                        'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-800/50 dark:text-emerald-400': status === 'success',
                        'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/30 dark:border-red-800/50 dark:text-red-400': status === 'error'
                    }">

                    <div class="flex items-start gap-3">
                        <svg x-show="status === 'checking'" class="animate-spin w-5 h-5 text-amber-500 shrink-0 mt-0.5"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg x-cloak x-show="status === 'success'" class="w-5 h-5 shrink-0 mt-0.5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <svg x-cloak x-show="status === 'error'" class="w-5 h-5 shrink-0 mt-0.5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>

                        <div class="flex flex-col">
                            <span x-show="status === 'checking'">Mendeteksi lokasi Anda...</span>
                            <span x-cloak x-show="status === 'success'">Lokasi terverifikasi. Anda di area Kafe.</span>
                            <span x-cloak x-show="status === 'error'" class="leading-relaxed">
                                @if ($errors->has('latitude'))
                                    {{ $errors->first('latitude') }}
                                @else
                                    <span
                                        x-text="$wire.locationError || 'Akses lokasi ditolak atau bermasalah.'"></span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div x-cloak x-show="status === 'error'" class="flex justify-end items-center gap-3 mt-1 relative z-20">
                    <button type="button" x-show="!isBlocked" @click.prevent="retry()"
                        class="text-xs font-bold text-red-600 underline hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 py-1 transition-all">
                        Coba deteksi ulang
                    </button>
                    <button type="button" @click.prevent="showHelpModal = true"
                        class="flex items-center justify-center w-6 h-6 rounded-full bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 font-bold text-sm shadow-sm transition-all"
                        title="Bantuan Izin Lokasi">
                        ?
                    </button>
                </div>
            </div>

            <button type="submit" wire:loading.attr="disabled"
                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:scale-[1.02] disabled:opacity-50 disabled:hover:scale-100 flex items-center justify-center gap-2 mt-4">
                <span wire:loading.remove wire:target="joinTable">Mulai Memesan</span>
                <span wire:loading wire:target="joinTable">Memproses...</span>
                <svg wire:loading.remove wire:target="joinTable" class="w-5 h-5" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>

        <p class="text-center text-xs font-medium text-slate-400 mt-8">
            {{ $setting->footer_text ?? 'Hak cipta dilindungi' }}
        </p>
    </div>

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

            <div class="p-0 overflow-y-auto" x-data="{ tab: 'ios' }">
                <div class="flex border-b border-slate-100 dark:border-slate-700">
                    <button @click="tab = 'ios'"
                        :class="tab === 'ios' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-bold' :
                            'text-slate-500 font-medium hover:bg-slate-50 dark:hover:bg-slate-700'"
                        class="flex-1 py-3 text-sm text-center transition-colors">
                        🍎 iPhone/iOS
                    </button>
                    <button @click="tab = 'android'"
                        :class="tab === 'android' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-bold' :
                            'text-slate-500 font-medium hover:bg-slate-50 dark:hover:bg-slate-700'"
                        class="flex-1 py-3 text-sm text-center transition-colors">
                        🤖 Android
                    </button>
                    <button @click="tab = 'desktop'"
                        :class="tab === 'desktop' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 font-bold' :
                            'text-slate-500 font-medium hover:bg-slate-50 dark:hover:bg-slate-700'"
                        class="flex-1 py-3 text-sm text-center transition-colors">
                        💻 Laptop/PC
                    </button>
                </div>

                <div class="p-6">
                    <div x-show="tab === 'ios'" class="space-y-4 text-sm text-slate-600 dark:text-slate-300">
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800/50 mb-4">
                            <p class="font-bold text-blue-800 dark:text-blue-300 mb-1">🚨 Scan dari Instagram / TikTok
                                / LINE?</p>
                            <p>Browser bawaan aplikasi biasanya memblokir GPS. Ketik ikon <b>Kompas / Titik Tiga /
                                    AA</b> di pojok layar, lalu pilih <b>"Buka di Safari" (Open in System Browser)</b>.
                            </p>
                        </div>
                        <h4 class="font-bold text-slate-800 dark:text-white">Jika menggunakan Safari:</h4>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Buka <b>Pengaturan (Settings)</b> iPhone Anda.</li>
                            <li>Pilih menu <b>Privasi & Keamanan (Privacy & Security)</b>.</li>
                            <li>Pilih <b>Layanan Lokasi (Location Services)</b>.</li>
                            <li>Scroll ke bawah, cari dan pilih <b>Safari Websites</b>.</li>
                            <li>Ubah centang menjadi <b>Saat Menggunakan Aplikasi (While Using the App)</b>.</li>
                            <li>Kembali ke browser dan Muat Ulang (Refresh) halaman ini.</li>
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
                            <li>Tutup menu tersebut, lalu Muat Ulang (Refresh) halaman ini.</li>
                        </ol>
                        <p class="mt-4 text-xs italic text-slate-500">Pastikan juga fitur GPS / Lokasi pada panel
                            pengaturan cepat HP Anda dalam keadaan Menyala.</p>
                    </div>

                    <div x-show="tab === 'desktop'" class="space-y-4 text-sm text-slate-600 dark:text-slate-300"
                        style="display: none;">
                        <h4 class="font-bold text-slate-800 dark:text-white">Chrome / Edge / Firefox:</h4>
                        <ol class="list-decimal pl-5 space-y-2 mb-4">
                            <li>Klik ikon <b>Gembok 🔒</b> atau ikon pengaturan di sebelah kiri alamat situs (URL) di
                                bagian atas browser.</li>
                            <li>Cari opsi <b>Lokasi (Location)</b>.</li>
                            <li>Ubah pengaturannya dari "Blokir" menjadi <b>Izinkan (Allow)</b>.</li>
                            <li>Muat Ulang (Refresh) halaman ini.</li>
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
                </div>
            </div>

            <div
                class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-right">
                <button @click="showHelpModal = false"
                    class="px-5 py-2 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold rounded-xl transition-colors shadow-sm text-sm">
                    Mengerti, Tutup Panduan
                </button>
            </div>
        </div>
    </div>

</div>
