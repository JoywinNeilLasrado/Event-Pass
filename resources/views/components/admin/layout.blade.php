<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – EventPass</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ── --}}
    <aside class="w-64 flex-shrink-0 bg-gray-900 flex flex-col">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-700">
            <span class="text-2xl">🎟️</span>
            <div>
                <p class="text-white font-bold text-sm">EventPass</p>
                <p class="text-indigo-400 text-xs font-semibold uppercase tracking-widest">Admin Panel</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            @php
                $nav = [
                    ['route' => 'admin.dashboard',         'icon' => '📊', 'label' => 'Dashboard'],
                    ['route' => 'admin.users.index',       'icon' => '👥', 'label' => 'Users'],
                    ['route' => 'admin.events.index',      'icon' => '🗓️', 'label' => 'Events'],
                    ['route' => 'admin.bookings.index',    'icon' => '🎫', 'label' => 'Bookings'],
                    ['route' => 'admin.categories.index',  'icon' => '🏷️', 'label' => 'Categories'],
                    ['route' => 'admin.tags.index',        'icon' => '🔖', 'label' => 'Tags'],
                ];
            @endphp

            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs($item['route']) ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="text-base">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="px-4 py-4 border-t border-gray-700 space-y-1">
            <a href="{{ route('events.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-800 transition">
                <span>🌐</span> View Site
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:text-red-400 hover:bg-gray-800 transition">
                    <span>🚪</span> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <h1 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Admin' }}</h1>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-sm font-bold uppercase">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                <span class="text-xs bg-indigo-100 text-indigo-700 rounded-full px-2 py-0.5 font-semibold">Admin</span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-gray-50 px-8 py-6">

            @if(session('success'))
                <div class="mb-5 bg-green-100 border border-green-300 text-green-800 rounded-lg px-4 py-3 text-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 bg-red-100 border border-red-300 text-red-800 rounded-lg px-4 py-3 text-sm">
                    ❌ {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
