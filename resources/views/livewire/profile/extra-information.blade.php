<section
    class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6 sm:p-8">
    <header class="mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">
            Tautan Login Cepat (OAuth)
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Kelola integrasi akun Google atau GitHub Anda untuk mempermudah proses login di masa mendatang.
        </p>
    </header>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-between">
            <span class="font-medium text-emerald-800 dark:text-emerald-300">{{ session('message') }}</span>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    @endif

    <div class="space-y-4">

        @if ($isLinked)
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border border-emerald-200 dark:border-emerald-800/50 rounded-2xl bg-emerald-50/50 dark:bg-emerald-900/10 gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white dark:bg-slate-800 rounded-full shadow-sm">
                        @if ($currentProvider === 'google')
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.333.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z" />
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-slate-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ ucfirst($currentProvider) }}
                        </p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Terhubung (Aktif)</p>
                    </div>
                </div>

                <button wire:click="unlink"
                    class="text-xs font-bold px-4 py-2 bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 rounded-xl hover:bg-red-200 transition-colors w-full sm:w-auto text-center">Putuskan
                    Tautan</button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('social.redirect', 'google') }}"
                    class="flex items-center justify-between p-4 border border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white dark:bg-slate-800 rounded-full shadow-sm">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.333.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Google</p>
                    </div>
                    <span
                        class="text-xs font-bold text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity">Tautkan
                        &rarr;</span>
                </a>

                <a href="{{ route('social.redirect', 'github') }}"
                    class="flex items-center justify-between p-4 border border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white dark:bg-slate-800 rounded-full shadow-sm">
                            <svg class="w-6 h-6 text-slate-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200">GitHub</p>
                    </div>
                    <span
                        class="text-xs font-bold text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity">Tautkan
                        &rarr;</span>
                </a>
            </div>
        @endif
    </div>
</section>
