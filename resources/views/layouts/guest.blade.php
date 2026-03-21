<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Passage') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
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
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0">
            <div class="z-10 relative flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                <!-- Back to Welcome Page -->
                <a href="{{ url('/') }}" class="shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-white/5 hover:bg-white/10 dark:bg-black/20 dark:hover:bg-black/40 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors backdrop-blur-sm border border-gray-200/50 dark:border-white/10 shadow-sm" title="Back to Home">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                
                <a href="/">
                    <h1 class="font-extrabold text-5xl tracking-tighter text-gray-900 dark:text-white transition-colors duration-300 drop-shadow-md">Passage.</h1>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-10 py-10 card z-10 relative">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
