<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Passage') }}</title>
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
                <span class="font-extrabold text-xl tracking-tighter text-gray-900 dark:text-white transition-colors">Passage.</span>
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
                Passage 2.0 is now live
            </div>
            
            <h1 class="text-6xl sm:text-7xl md:text-8xl font-extrabold tracking-tighter text-[#111827] dark:text-white leading-[1.05] mb-8 transition-colors">
                Host, discover, and <br class="hidden sm:block" /> experience the
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">extraordinary.</span>
            </h1>
            
            <p class="mt-8 text-xl md:text-2xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-14 leading-relaxed font-light transition-colors">
                The ultimate premium platform to create, manage, and book events effortlessly. Immerse yourself in a world-class ticketing experience built for the modern creator.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 relative z-20 mb-16">
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
            
            <!-- Category Carousel -->
            <div class="relative z-30 mb-8 w-screen relative -left-[50vw] ml-[50%]">
                <x-category-carousel :categories="$categories" />
            </div>
            
            @if(isset($featuredEvents) && $featuredEvents->count() > 0)
                <!-- Featured Events Carousel -->
                <div class="mt-24 sm:mt-32 w-full max-w-[100vw] overflow-hidden" 
                     x-data="{
                        activeSlide: 0,
                        slides: {{ $featuredEvents->count() }},
                        timer: null,
                        next() { this.activeSlide = this.activeSlide === this.slides - 1 ? 0 : this.activeSlide + 1 },
                        prev() { this.activeSlide = this.activeSlide === 0 ? this.slides - 1 : this.activeSlide - 1 },
                        startTimer() {
                            this.timer = setInterval(() => { this.next() }, 2000);
                        },
                        stopTimer() {
                            clearInterval(this.timer);
                        }
                     }"
                     x-init="startTimer()"
                     @mouseenter="stopTimer()"
                     @mouseleave="startTimer()">
                    <div class="flex items-center justify-between mb-8 px-4 sm:px-0 max-w-7xl mx-auto">
                        <div class="text-left">
                            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight">Featured Events</h2>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">Discover premium experiences curated just for you.</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="prev()" class="p-2 sm:p-3 rounded-full bg-white dark:bg-white/10 hover:bg-gray-50 dark:hover:bg-white/20 border border-gray-200 dark:border-white/10 shadow-sm transition-all focus:outline-none">
                                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <button @click="next()" class="p-2 sm:p-3 rounded-full bg-white dark:bg-white/10 hover:bg-gray-50 dark:hover:bg-white/20 border border-gray-200 dark:border-white/10 shadow-sm transition-all focus:outline-none">
                                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-0">
                        <div class="overflow-hidden rounded-2xl sm:rounded-3xl shadow-2xl relative bg-gray-100 dark:bg-gray-900 aspect-[16/9] sm:aspect-[21/9]">
                            <div class="flex w-full h-full transition-transform duration-700 ease-in-out" :style="`transform: translateX(-${activeSlide * 100}%);`">
                                @foreach($featuredEvents as $index => $event)
                                    <div class="w-full h-full flex-shrink-0 relative group cursor-pointer" onclick="window.location='{{ route('events.show', $event) }}'">
                                        <!-- Background Image -->
                                        @if($event->poster_image)
                                            <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                        @else
                                            <div class="absolute inset-0 w-full h-full bg-gradient-to-tr from-indigo-500 to-purple-600"></div>
                                        @endif
                                        
                                        <!-- Gradient Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                                        
                                        <!-- Content -->
                                        <div class="absolute inset-0 p-6 sm:p-12 flex flex-col justify-end text-left pb-12 sm:pb-16 text-white">
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider">{{ $event->category ? $event->category->name : 'Event' }}</span>
                                                <span class="px-3 py-1 bg-emerald-500/80 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider">Featured</span>
                                                <span class="px-3 py-1 bg-black/40 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider"><svg class="w-3 h-3 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>{{ $event->date->format('M j, Y') }}</span>
                                            </div>
                                            <h3 class="text-3xl sm:text-5xl md:text-6xl font-black mb-2 sm:mb-4 tracking-tight drop-shadow-md">{{ $event->title }}</h3>
                                            <p class="text-sm sm:text-lg text-white/80 line-clamp-2 max-w-3xl drop-shadow mb-6">{{ $event->description }}</p>
                                            
                                            <span class="inline-flex items-center gap-2 text-sm sm:text-base font-bold group-hover:text-purple-300 transition-colors">
                                                Get Tickets
                                                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Slide Indicators -->
                        <div class="absolute bottom-6 sm:bottom-8 left-1/2 -translate-x-1/2 flex items-center justify-center gap-2 sm:gap-3 z-20">
                            <template x-for="(slide, index) in slides" :key="index">
                                <button @click="activeSlide = index" class="w-12 sm:w-16 h-1 sm:h-1.5 rounded-full transition-all duration-300" :class="activeSlide === index ? 'bg-white' : 'bg-white/30 hover:bg-white/50'"></button>
                            </template>
                        </div>
                    </div>
                </div>
            @endif
            

            
        </div>
    </main>

    <footer class="border-t border-gray-200 dark:border-white/10 bg-white/50 dark:bg-[#0A0A0A]/50 backdrop-blur-md pt-16 pb-8 mt-auto transition-colors relative z-20">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="text-xl">🎟️</span>
                <span class="font-bold text-lg tracking-tight text-gray-900 dark:text-white transition-colors">Passage.</span>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 font-medium transition-colors">
                &copy; {{ date('Y') }} Passage. Built with Laravel and Tailwind CSS.
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
