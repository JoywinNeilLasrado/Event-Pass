<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Passage') }}</title>
        
        @stack('meta')

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
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Global Flash Toast Notifications -->
            @if(session('success') || session('error'))
                <div x-data="{ show: true, type: '{{ session('success') ? 'success' : 'error' }}', message: '{{ addslashes(session('success') ?? session('error')) }}' }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed top-20 right-4 sm:top-24 sm:right-6 z-[9999] max-w-sm w-full bg-white dark:bg-[#111] border border-gray-200 dark:border-white/10 shadow-2xl rounded-xl overflow-hidden glass">
                    <div class="p-4 flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <template x-if="type === 'success'">
                                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                            <template x-if="type === 'error'">
                                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                        </div>
                        <div class="w-0 flex-1 pt-0.5">
                            <p class="text-sm font-black tracking-tight text-gray-900 dark:text-white" x-text="type === 'success' ? 'Success' : 'Action Required'"></p>
                            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400" x-text="message"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="show = false" class="bg-transparent rounded-md inline-flex text-gray-400 hover:text-gray-500 transition-colors">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-transparent border-b border-gray-200/60 pt-8 pb-4">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow">
                {{ $slot }}
            </main>
            
            <footer class="border-t border-white/20 dark:border-white/10 bg-white/30 dark:bg-black/30 backdrop-blur-xl py-12 mt-auto text-center text-sm text-gray-700 dark:text-gray-400">
                <div class="max-w-7xl mx-auto px-6">
                    &copy; {{ date('Y') }} Passage. Built with premium glassmorphism design.
                </div>
            </footer>
        </div>
    </body>
</html>
