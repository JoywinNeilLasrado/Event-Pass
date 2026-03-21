<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight transition-colors">Upcoming Events</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium transition-colors">Discover and book the best exclusive events around you.</p>
            </div>
            @auth
                <a href="{{ route('events.create') }}" class="btn-vercel text-sm px-5 py-2.5">
                    Create New Event
                </a>
            @endauth
        </div>
    </x-slot>

    <!-- Category Carousel -->
    @if(isset($categories) && $categories->count() > 0)
        <div class="pt-6 sm:pt-10">
            <x-category-carousel :categories="$categories" />
        </div>
    @endif

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Search & Filter Form -->
            <form method="GET" action="{{ route('events.index') }}" class="mb-8 relative z-50">
                <div class="flex flex-col sm:flex-row gap-4 bg-white/40 dark:bg-black/40 backdrop-blur-md border border-gray-100 dark:border-white/10 p-2 rounded-2xl shadow-sm transition-colors relative z-50">
                    <div class="flex-grow relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events by title or location..." class="w-full pl-11 pr-4 py-3 bg-transparent border-none focus:ring-0 text-gray-900 dark:text-white placeholder-gray-500 font-medium transition-colors h-full rounded-xl">
                    </div>
                    @if(isset($categories) && $categories->count() > 0)
                        <div class="sm:w-64 border-t sm:border-t-0 sm:border-l border-gray-100 dark:border-white/10 transition-colors relative" x-data="{ open: false }">
                            <input type="hidden" name="category" value="{{ request('category') }}" id="category-filter">
                            <button type="button" @click="open = !open" @click.outside="open = false" class="w-full py-3 px-4 bg-transparent border-none outline-none focus:ring-0 text-gray-600 dark:text-white font-medium cursor-pointer transition-colors h-full rounded-xl flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-white/5">
                                <span>
                                    @php
                                        $selectedCategory = $categories->firstWhere('id', request('category'));
                                    @endphp
                                    {{ $selectedCategory ? $selectedCategory->name : 'All Categories' }}
                                </span>
                                <svg class="w-4 h-4 ml-2 transition-transform duration-200 text-gray-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute z-[100] left-0 right-0 mt-2 bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-white/10 rounded-xl shadow-xl overflow-hidden py-1.5 max-h-60 overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-200 dark:[&::-webkit-scrollbar-thumb]:bg-gray-700 [&::-webkit-scrollbar-thumb]:rounded-full"
                                 style="display: none;">
                                <button type="button" 
                                        @click="document.getElementById('category-filter').value = ''; document.getElementById('category-filter').form.submit()"
                                        class="w-full text-left px-5 py-2.5 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-white/5 transition-colors {{ request('category') == '' ? 'text-black dark:text-white bg-gray-50 dark:bg-white/5' : 'text-gray-500 dark:text-gray-400' }}">
                                    All Categories
                                </button>
                                @foreach($categories as $category)
                                    <button type="button" 
                                            @click="document.getElementById('category-filter').value = '{{ $category->id }}'; document.getElementById('category-filter').form.submit()"
                                            class="w-full text-left px-5 py-2.5 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-white/5 transition-colors {{ request('category') == $category->id ? 'text-black dark:text-white bg-gray-50 dark:bg-white/5' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ ucwords($category->name) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <button type="submit" class="hidden sm:block btn-vercel px-6 py-2">Search</button>
                </div>
            </form>

            @if(session('success'))
                <div class="card bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 text-sm font-medium transition-colors">
                    {{ session('success') }}
                </div>
            @endif

            @if($events->isEmpty() && (!isset($myEvents) || $myEvents->isEmpty()))
                <div class="text-center py-24 card">
                    <span class="text-4xl opacity-50 dark:opacity-30 transition-opacity">🎫</span>
                    <h3 class="mt-4 text-lg font-bold text-gray-900 dark:text-white tracking-tight transition-colors">No events found</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto transition-colors">There are currently no events to display.</p>
                    @auth
                        <a href="{{ route('events.create') }}" class="mt-6 inline-block btn-vercel">Create Event</a>
                    @endauth
                </div>
            @else
                @auth
                @if(Auth::user()->is_organizer)
                    <div class="col-span-full border border-gray-100 dark:border-white/10 rounded-2xl bg-white dark:bg-black/20 p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)]">
                        <div class="relative z-10 flex-1">
                            <span class="inline-block px-3 py-1 bg-black text-white dark:bg-white dark:text-black rounded-full text-[10px] font-bold uppercase tracking-widest mb-4 shadow-sm">Organizer Mode</span>
                            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-2">Create a New Event</h2>
                            <p class="text-gray-500 dark:text-gray-400 text-sm md:text-base font-medium max-w-xl">Host an amazing experience. Set up ticket types, manage attendees, and track your success all in one place.</p>
                        </div>
                        <div class="relative z-10 w-full md:w-auto shrink-0 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('dashboard') }}" class="btn-vercel-secondary text-sm px-6 py-3 shrink-0 flex justify-center">View Analytics</a>
                            <a href="{{ route('events.create') }}" class="btn-vercel text-sm px-6 py-3 shrink-0 flex justify-center items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Create Event
                            </a>
                        </div>
                    </div>
                @endif
            @endauth
                @if(Auth::check() && Auth::user()->is_organizer && isset($myEvents) && $myEvents->isNotEmpty())
                    <div class="mb-12">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">My Created Events</h3>
                            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">View Dashboard &rarr;</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($myEvents as $event)
                                @include('events.partials.event-card', ['event' => $event])
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($events->isNotEmpty())
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight mb-6">
                            @if(isset($myEvents) && $myEvents->isNotEmpty())
                                Other Upcoming Events
                            @else
                                Upcoming Events
                            @endif
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($events as $event)
                                @include('events.partials.event-card', ['event' => $event])
                            @endforeach
                        </div>
                    </div>
    
                    <div class="mt-12">
                        {{ $events->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
