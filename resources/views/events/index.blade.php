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

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Search & Filter Form -->
            <form method="GET" action="{{ route('events.index') }}" class="mb-8">
                <div class="flex flex-col sm:flex-row gap-4 bg-white/40 dark:bg-black/40 backdrop-blur-md border border-gray-100 dark:border-white/10 p-2 rounded-2xl shadow-sm transition-colors">
                    <div class="flex-grow relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events by title or location..." class="w-full pl-11 pr-4 py-3 bg-transparent border-none focus:ring-0 text-gray-900 dark:text-white placeholder-gray-500 font-medium transition-colors h-full rounded-xl">
                    </div>
                    @if(isset($categories) && $categories->count() > 0)
                        <div class="sm:w-64 border-t sm:border-t-0 sm:border-l border-gray-100 dark:border-white/10 transition-colors">
                            <select name="category" onchange="this.form.submit()" class="w-full py-3 px-4 bg-transparent border-none focus:ring-0 text-gray-600 dark:text-gray-300 font-medium cursor-pointer transition-colors h-full rounded-xl [color-scheme:light] dark:[color-scheme:dark]">
                                <option value="" class="bg-white dark:bg-neutral-800">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" class="bg-white dark:bg-neutral-800" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
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
                
                @if(isset($myEvents) && $myEvents->isNotEmpty())
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
