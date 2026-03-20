<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-white tracking-tight transition-colors">
            {{ __('Profile Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(!Auth::user()->is_organizer)
            <div class="card p-6 sm:p-8 bg-gradient-to-r from-indigo-500/5 to-purple-500/5 border border-indigo-100 dark:border-indigo-500/20">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Upgrade to Organizer Account</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Unlock the ability to host your own events, scan tickets, and access analytics.</p>
                    </div>
                    <a href="{{ route('upgrade.index') }}" class="btn-vercel px-6 py-2 text-sm font-bold whitespace-nowrap">Upgrade Now</a>
                </div>
            </div>
            @endif

            <div class="card p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card p-6 sm:p-8 mb-20">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
