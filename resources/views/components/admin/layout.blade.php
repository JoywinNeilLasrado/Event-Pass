<!DOCTYPE html>
<html lang="en" class="h-full bg-[#FAFAFA] dark:bg-black">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Passage</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dark Mode Checker -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="h-full font-sans antialiased">
<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ── --}}
    <aside class="w-64 flex-shrink-0 bg-white dark:bg-[#0A0A0A] border-r border-gray-200 dark:border-white/10 flex flex-col z-20 transition-colors">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 dark:border-white/10 transition-colors">
            <span class="text-2xl">🎟️</span>
            <div>
                <p class="text-gray-900 dark:text-white font-extrabold text-sm tracking-tight transition-colors">Passage</p>
                <p class="text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-0.5 transition-colors">Admin Panel</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            @php
                $nav = [
                    ['route' => 'admin.dashboard',         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>', 'label' => 'Overview'],
                    ['route' => 'admin.users.index',       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>', 'label' => 'Users'],
                    ['route' => 'admin.events.index',      'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>', 'label' => 'Events'],
                    ['route' => 'admin.bookings.index',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>', 'label' => 'Bookings'],
                    ['route' => 'admin.categories.index',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>', 'label' => 'Categories'],
                    ['route' => 'admin.tags.index',        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>', 'label' => 'Tags'],
                    ['route' => 'admin.settings.index',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>', 'label' => 'Settings'],
                ];
            @endphp

            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors
                          {{ request()->routeIs($item['route']) ? 'bg-gray-100 dark:bg-[#1A1A1A] text-black dark:text-white font-semibold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-[#1A1A1A] hover:text-gray-900 dark:hover:text-white font-medium' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs($item['route']) ? 'text-black dark:text-white' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="px-4 py-4 border-t border-gray-100 dark:border-white/10 space-y-1 transition-colors">
            <a href="{{ route('events.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-[#1A1A1A] transition-colors border border-transparent hover:border-gray-200 dark:hover:border-white/5">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg> 
                <span class="font-medium">View Public Site</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors border border-transparent hover:border-red-100 dark:hover:border-transparent">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:text-red-500 dark:group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span class="font-medium">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex-1 flex flex-col overflow-hidden bg-[#FAFAFA] dark:bg-black transition-colors">
        <header class="bg-white/80 dark:bg-[#0A0A0A]/80 backdrop-blur-md border-b border-gray-200 dark:border-white/10 px-8 py-4 flex items-center justify-between flex-shrink-0 z-10 sticky top-0 transition-colors">
            <h1 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight transition-colors">{{ $title ?? 'Admin' }}</h1>
            <div class="flex items-center gap-4">
                <!-- Theme Toggle -->
                <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors focus:outline-none">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>
                <div class="w-8 h-8 rounded-full bg-black dark:bg-white text-white dark:text-black flex items-center justify-center text-sm font-bold uppercase shadow-sm transition-colors">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:block transition-colors">{{ auth()->user()->name }}</span>
                <span class="text-[10px] bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest shadow-sm transition-colors">Admin</span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-8 py-8">

            @if(session('success'))
                <div class="mb-6 card bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 text-sm font-medium transition-colors">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 card bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 text-sm font-medium transition-colors">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        if(themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                themeToggleDarkIcon.classList.toggle('hidden');
                themeToggleLightIcon.classList.toggle('hidden');

                if (localStorage.theme) {
                    if (localStorage.theme === 'light') {
                        document.documentElement.classList.add('dark');
                        localStorage.theme = 'dark';
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.theme = 'light';
                    }
                } else {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.theme = 'light';
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.theme = 'dark';
                    }
                }
            });
        }
    });
</script>
</body>
</html>
