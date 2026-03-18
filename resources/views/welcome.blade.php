<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EventPass') }}</title>
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
<body class="antialiased bg-[#FAFAFA] dark:bg-black text-[#111827] dark:text-gray-100 flex flex-col min-h-screen transition-colors">

    <header class="sticky top-0 z-50 bg-white/80 dark:bg-[#0A0A0A]/80 backdrop-blur-md border-b border-gray-200 dark:border-white/10 transition-colors">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🎟️</span>
                <span class="font-bold text-xl tracking-tight text-gray-900 dark:text-white transition-colors">EventPass</span>
            </div>
            
            <nav class="flex items-center gap-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-vercel text-sm px-4 py-2">Sign up free</a>
                        @endif
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center pt-24 pb-32 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 text-sm font-medium mb-8 text-black dark:text-white transition-colors">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> 
                Eventpass 2.0 is now live
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-[#111827] dark:text-white leading-tight mb-6 transition-colors">
                Ticketing infrastructure <br class="hidden sm:block" /> for the <span class="text-transparent bg-clip-text bg-gradient-to-r from-gray-900 dark:from-white to-gray-500 dark:to-gray-500">modern web.</span>
            </h1>
            <p class="mt-6 text-xl text-gray-500 dark:text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed font-light transition-colors">
                A premium, beautifully designed platform to create, manage, and book events effortlessly. Experience the difference of a truly professional interface.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="btn-vercel text-lg px-8 py-3.5 w-full sm:w-auto">Start Building Events</a>
                <a href="{{ route('events.index') }}" class="btn-vercel-secondary text-lg px-8 py-3.5 w-full sm:w-auto">Explore Events</a>
            </div>
        </div>
    </main>

    <footer class="border-t border-gray-200 dark:border-white/10 bg-white dark:bg-[#0A0A0A] py-12 mt-auto transition-colors">
        <div class="max-w-7xl mx-auto px-6 text-center text-sm text-gray-500 dark:text-gray-400 transition-colors">
            &copy; {{ date('Y') }} Eventpass. Built with Laravel and Tailwind CSS.
        </div>
    </footer>

</body>
</html>
