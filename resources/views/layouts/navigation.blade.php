<div x-data="{ open: false }" class="sticky top-4 z-50 px-4 sm:px-6 w-full max-w-7xl mx-auto transition-all duration-300">
    <nav class="relative bg-white dark:bg-[#1a1a1a] shadow-[0_4px_20px_rgba(0,0,0,0.08)] dark:shadow-[0_4px_20px_rgba(0,0,0,0.4)] rounded-full border border-gray-200/60 dark:border-white/10 transition-colors duration-300">
        <!-- Primary Navigation Menu -->
        <div class="px-5 sm:px-8">
            <div class="flex justify-between h-14 items-center">
            <div class="flex items-center gap-6 sm:gap-8">
                <!-- Back to Welcome Page -->
                <a href="{{ url('/') }}" class="shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 text-gray-600 dark:text-gray-300 transition-colors" title="Back to Home">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="text-black dark:text-white transition-colors duration-300 font-extrabold text-xl tracking-tighter">
                        Passage.
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:flex">
                    <a href="{{ route('events.index') }}" class="text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('events.index') ? 'text-black dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
                        Events
                    </a>
                    @auth
                        <a href="{{ route('bookings.index') }}" class="text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('bookings.index') ? 'text-black dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
                            My Tickets
                        </a>
                        @if(Auth::user()->is_organizer)
                            <a href="{{ route('dashboard') }}" class="text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-black dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
                                Dashboard
                            </a>
                        @endif
                        @if(Auth::user()->is_organizer || Auth::user()->employer_id)
                            <a href="{{ route('scan') }}" class="text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('scan') ? 'text-black dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
                                Scan Tickets
                            </a>
                        @endif
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold transition-colors duration-200 {{ request()->routeIs('admin.*') ? 'text-black dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
                                Admin
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-6">
                <!-- Theme Toggle -->
                <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors focus:outline-none">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>

                @auth
                    <!-- Settings Dropdown (authenticated) -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors focus:outline-none">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="fill-current h-4 w-4 opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if(!Auth::user()->is_organizer)
                                <x-dropdown-link :href="route('upgrade.index')" class="!text-sm !font-bold !text-indigo-600 dark:!text-indigo-400 hover:!bg-indigo-50 dark:hover:!bg-indigo-900/20 transition-colors">
                                    {{ __('Become an Organizer') }}
                                </x-dropdown-link>
                                <div class="border-t border-gray-100 dark:border-white/10 my-1"></div>
                            @endif

                            <x-dropdown-link :href="route('profile.edit')" class="!text-sm !font-semibold !text-gray-700 dark:!text-gray-300 hover:!bg-gray-50 dark:hover:!bg-[#111] hover:!text-black dark:hover:!text-white transition-colors">
                                {{ __('Profile Settings') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="!text-sm !font-semibold !text-gray-700 dark:!text-gray-300 hover:!bg-gray-50 dark:hover:!bg-[#111] hover:!text-black dark:hover:!text-white transition-colors">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <!-- Guest Links -->
                    <div class="flex items-center gap-6">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">Log in</a>
                        <a href="{{ route('register') }}" class="btn-vercel px-4 py-2 text-sm">Register</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 focus:outline-none focus:bg-gray-100 dark:focus:bg-white/5 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="absolute top-full left-0 right-0 mt-3 hidden sm:hidden bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-xl border border-gray-200/60 dark:border-white/10 overflow-hidden z-50 origin-top animate-in ease-out duration-200">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('events.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('events.index') ? 'border-black dark:border-white text-black dark:text-white font-bold bg-gray-50 dark:bg-white/10' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 hover:border-gray-300 dark:hover:border-gray-600' }} text-base font-medium transition duration-150 ease-in-out">
                Events
            </a>
            @auth
                <a href="{{ route('bookings.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('bookings.index') ? 'border-black dark:border-white text-black dark:text-white font-bold bg-gray-50 dark:bg-white/10' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 hover:border-gray-300 dark:hover:border-gray-600' }} text-base font-medium transition duration-150 ease-in-out">
                    My Tickets
                </a>
                @if(Auth::user()->is_organizer)
                    <a href="{{ route('dashboard') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-black dark:border-white text-black dark:text-white font-bold bg-gray-50 dark:bg-white/10' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 hover:border-gray-300 dark:hover:border-gray-600' }} text-base font-medium transition duration-150 ease-in-out">
                        Dashboard
                    </a>
                @endif
                @if(Auth::user()->is_organizer || Auth::user()->employer_id)
                    <a href="{{ route('scan') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('scan') ? 'border-black dark:border-white text-black dark:text-white font-bold bg-gray-50 dark:bg-white/10' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 hover:border-gray-300 dark:hover:border-gray-600' }} text-base font-medium transition duration-150 ease-in-out">
                        Scan Tickets
                    </a>
                @endif
                @if(Auth::user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('admin.*') ? 'border-black dark:border-white text-black dark:text-white font-bold bg-gray-50 dark:bg-white/10' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 hover:border-gray-300 dark:hover:border-gray-600' }} text-base font-medium transition duration-150 ease-in-out">
                        Admin
                    </a>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-white/10">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    @if(!Auth::user()->is_organizer)
                        <a href="{{ route('upgrade.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-indigo-600 dark:text-indigo-400 font-bold hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition duration-150 ease-in-out">
                            Become an Organizer
                        </a>
                    @endif

                    <a href="{{ route('profile.edit') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 hover:border-gray-300 dark:hover:border-gray-600 text-base font-medium transition duration-150 ease-in-out">
                        Profile
                    </a>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 hover:border-gray-300 dark:hover:border-gray-600 text-base font-medium transition duration-150 ease-in-out">
                            Log Out
                        </a>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-3 border-t border-gray-200 dark:border-white/10 space-y-1">
                <a href="{{ route('login') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 hover:border-gray-300 dark:hover:border-gray-600 text-base font-medium transition duration-150 ease-in-out">Log in</a>
                <a href="{{ route('register') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-white/5 hover:border-gray-300 dark:hover:border-gray-600 text-base font-medium transition duration-150 ease-in-out">Register</a>
            </div>
        @endauth
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Initial icon state
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            // toggle icons
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set previously
            if (localStorage.theme) {
                if (localStorage.theme === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                }
            } else {
                // not set
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                }
            }
        });
    });
</script>
