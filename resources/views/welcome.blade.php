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
<body class="antialiased bg-[#FAFAFA] dark:bg-black bg-mesh-light dark:bg-mesh-dark text-[#111827] dark:text-gray-100 flex flex-col min-h-screen transition-colors relative overflow-x-hidden">

    <header class="sticky top-0 z-50 bg-white/40 dark:bg-black/40 backdrop-blur-2xl border-b border-white/40 dark:border-white/10 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🎟️</span>
                <span class="font-extrabold text-xl tracking-tighter text-gray-900 dark:text-white transition-colors">EventPass.</span>
            </div>
            
            <nav class="flex items-center gap-6">
                <!-- Theme Toggle -->
                <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors focus:outline-none hidden sm:block">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>

                @if (Route::has('login'))
                    @auth
                        @if(Auth::user()->is_organizer)
                            <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('bookings.index') }}" class="text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">My Tickets</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-vercel text-sm px-5 py-2">Sign up free</a>
                        @endif
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center pt-24 pb-16 px-6 relative z-10 mt-10">
        <!-- Decorative glowing orbs behind the hero -->
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] sm:w-[800px] sm:h-[800px] bg-gradient-to-tr from-purple-500/20 to-blue-500/20 dark:from-purple-600/30 dark:to-blue-600/30 blur-[120px] rounded-full pointer-events-none -z-10"></div>
        
        <div class="max-w-5xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/50 dark:bg-white/5 backdrop-blur-md border border-gray-200/50 dark:border-white/10 text-sm font-semibold mb-8 text-black dark:text-white shadow-sm transition-colors cursor-default hover:bg-white/80 dark:hover:bg-white/10">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                EventPass 2.0 is now live
            </div>
            
            <h1 class="text-6xl sm:text-7xl md:text-8xl font-extrabold tracking-tighter text-[#111827] dark:text-white leading-[1.05] mb-8 transition-colors">
                Host, discover, and <br class="hidden sm:block" /> experience the
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">extraordinary.</span>
            </h1>
            
            <p class="mt-8 text-xl md:text-2xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-14 leading-relaxed font-light transition-colors">
                The ultimate premium platform to create, manage, and book events effortlessly. Immerse yourself in a world-class ticketing experience built for the modern creator.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 relative z-20">
                @auth
                    @if(Auth::user()->is_organizer)
                        <a href="{{ route('events.create') }}" class="btn-vercel text-lg px-10 py-4 w-full sm:w-auto shadow-xl shadow-purple-500/20 hover:shadow-purple-500/40 transition-all duration-300">Create an Event</a>
                        <a href="{{ route('dashboard') }}" class="btn-vercel-secondary text-lg px-10 py-4 w-full sm:w-auto bg-white/50 dark:bg-white/5 backdrop-blur-md border-gray-200/50 dark:border-white/10 hover:bg-white/80 dark:hover:bg-white/15 transition-all duration-300 text-gray-900 dark:text-white">My Dashboard</a>
                    @else
                        <a href="{{ route('events.index') }}" class="btn-vercel text-lg px-10 py-4 w-full sm:w-auto shadow-xl shadow-purple-500/20 hover:shadow-purple-500/40 transition-all duration-300">Explore Events</a>
                        <a href="{{ route('bookings.index') }}" class="btn-vercel-secondary text-lg px-10 py-4 w-full sm:w-auto bg-white/50 dark:bg-white/5 backdrop-blur-md border-gray-200/50 dark:border-white/10 hover:bg-white/80 dark:hover:bg-white/15 transition-all duration-300 text-gray-900 dark:text-white">My Tickets</a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn-vercel text-lg px-10 py-4 w-full sm:w-auto shadow-xl shadow-purple-500/20 hover:shadow-purple-500/40 transition-all duration-300">Start Building Events</a>
                    <a href="{{ route('events.index') }}" class="btn-vercel-secondary text-lg px-10 py-4 w-full sm:w-auto bg-white/50 dark:bg-white/5 backdrop-blur-md border-gray-200/50 dark:border-white/10 hover:bg-white/80 dark:hover:bg-white/15 transition-all duration-300 text-gray-900 dark:text-white">Explore Events</a>
                @endauth
            </div>
            
            <!-- Beautiful Dashboard Mockup -->
            <div class="mt-28 relative mx-auto max-w-5xl group perspective-1000">
                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[#FAFAFA] dark:from-black to-transparent z-20 pointer-events-none"></div>
                
                <div class="card p-2 sm:p-4 rounded-3xl bg-white/40 dark:bg-white/5 backdrop-blur-3xl border border-white/60 dark:border-white/10 shadow-2xl relative transform transition-transform duration-700 hover:-translate-y-2 hover:shadow-[0_20px_60px_-15px_rgba(168,85,247,0.3)]">
                    <div class="bg-gray-50/90 dark:bg-[#0A0A0A]/90 rounded-2xl overflow-hidden border border-gray-200/50 dark:border-white/5 aspect-[16/10] sm:aspect-[16/9] flex items-center justify-center relative shadow-inner">
                        
                        <!-- Header Bar Mockup -->
                        <div class="absolute top-0 left-0 right-0 h-10 sm:h-12 border-b border-gray-200/50 dark:border-white/5 flex items-center justify-between px-4 sm:px-6 bg-white/50 dark:bg-black/50 backdrop-blur-md z-10">
                            <div class="flex gap-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
                            </div>
                            <div class="w-20 sm:w-24 h-3 sm:h-4 bg-gray-200/50 dark:bg-white/10 rounded-full"></div>
                        </div>

                        <!-- Sidebar + Content Mockup -->
                        <div class="absolute inset-0 top-10 sm:top-12 flex">
                            <!-- Sidebar -->
                            <div class="w-1/4 border-r border-gray-200/50 dark:border-white/5 p-4 sm:p-6 space-y-4 hidden md:block bg-white/30 dark:bg-white/5">
                                <div class="w-full h-8 bg-gray-200/50 dark:bg-white/10 rounded-lg mb-8"></div>
                                <div class="w-3/4 h-3 sm:h-4 bg-gray-200/50 dark:bg-white/10 rounded-full"></div>
                                <div class="w-5/6 h-3 sm:h-4 bg-gray-200/50 dark:bg-white/10 rounded-full"></div>
                                <div class="w-1/2 h-3 sm:h-4 bg-gray-200/50 dark:bg-white/10 rounded-full"></div>
                            </div>
                            <!-- Main View -->
                            <div class="flex-1 p-4 sm:p-6 relative overflow-hidden">
                                <!-- Title -->
                                <div class="w-1/2 md:w-1/3 h-6 sm:h-8 bg-gradient-to-r from-gray-200/80 to-gray-100/50 dark:from-white/10 dark:to-white/5 rounded-lg mb-6 sm:mb-8"></div>
                                
                                <!-- Cards Row -->
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8">
                                    <div class="h-20 sm:h-24 bg-white/50 dark:bg-white/5 border border-gray-200/50 dark:border-white/5 rounded-2xl shadow-sm flex flex-col justify-center p-3 sm:p-4">
                                        <div class="w-1/2 h-2 sm:h-3 bg-gray-200/50 dark:bg-white/10 rounded-full mb-3"></div>
                                        <div class="w-3/4 h-4 sm:h-6 bg-gray-300/50 dark:bg-white/20 rounded-md"></div>
                                    </div>
                                    <div class="h-20 sm:h-24 bg-white/50 dark:bg-white/5 border border-gray-200/50 dark:border-white/5 rounded-2xl shadow-sm flex flex-col justify-center p-3 sm:p-4">
                                        <div class="w-1/2 h-2 sm:h-3 bg-gray-200/50 dark:bg-white/10 rounded-full mb-3"></div>
                                        <div class="w-2/3 h-4 sm:h-6 bg-gray-300/50 dark:bg-white/20 rounded-md"></div>
                                    </div>
                                    <div class="hidden md:flex h-24 bg-white/50 dark:bg-white/5 border border-gray-200/50 dark:border-white/5 rounded-2xl shadow-sm flex-col justify-center p-4">
                                        <div class="w-1/2 h-3 bg-gray-200/50 dark:bg-white/10 rounded-full mb-3"></div>
                                        <div class="w-5/6 h-6 bg-gray-300/50 dark:bg-white/20 rounded-md"></div>
                                    </div>
                                </div>

                                <!-- Large Graph/Chart area -->
                                <div class="w-full h-32 sm:h-48 bg-gradient-to-b from-purple-500/10 to-transparent dark:from-purple-500/5 border border-gray-200/50 dark:border-white/5 rounded-2xl relative overflow-hidden">
                                    <!-- Fake graph line -->
                                    <svg class="absolute bottom-0 w-full h-full preserve-aspect-none opacity-50 dark:opacity-30" viewBox="0 0 100 100" preserveAspectRatio="none">
                                        <path d="M0,100 L0,50 Q25,20 50,60 T100,30 L100,100 Z" fill="currentColor" class="text-purple-500/20"></path>
                                        <path d="M0,50 Q25,20 50,60 T100,30" fill="none" stroke="currentColor" stroke-width="2" class="text-purple-500"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                         <div class="z-30 absolute backdrop-blur-xl bg-white/60 dark:bg-black/60 px-5 sm:px-6 py-2.5 sm:py-3 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-white/80 dark:border-white/10 flex items-center gap-2 sm:gap-3 transform -translate-y-6 sm:-translate-y-8 animate-bounce" style="animation-duration: 3s;">
                             <span class="text-base sm:text-lg">✨</span>
                             <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white tracking-wide">Beautiful interface</span>
                         </div>
                    </div>
                </div>
            </div>
            
        </div>
    </main>

    <footer class="border-t border-gray-200 dark:border-white/10 bg-white/50 dark:bg-[#0A0A0A]/50 backdrop-blur-md pt-16 pb-8 mt-auto transition-colors relative z-20">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="text-xl">🎟️</span>
                <span class="font-bold text-lg tracking-tight text-gray-900 dark:text-white transition-colors">EventPass.</span>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 font-medium transition-colors">
                &copy; {{ date('Y') }} EventPass. Built with Laravel and Tailwind CSS.
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                if(themeToggleLightIcon) themeToggleLightIcon.classList.remove('hidden');
            } else {
                if(themeToggleDarkIcon) themeToggleDarkIcon.classList.remove('hidden');
            }

            var themeToggleBtn = document.getElementById('theme-toggle');

            if (themeToggleBtn) {
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
