<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EventPass') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#FAFAFA] text-[#111827]">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

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
            
            <footer class="border-t border-gray-200 bg-white py-12 mt-auto">
                <div class="max-w-7xl mx-auto px-6 text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} Eventpass. Built with premium design standards.
                </div>
            </footer>
        </div>
    </body>
</html>
