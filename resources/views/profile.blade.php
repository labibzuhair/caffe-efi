<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-slate-900 dark:text-white tracking-tight">
            {{ __('Pengaturan Profil') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <livewire:profile.update-profile-information-form />

            <livewire:profile.extra-information />

            <livewire:profile.update-password-form />

            <livewire:profile.delete-user-form />

        </div>
    </div>
</x-app-layout>
