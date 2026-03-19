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

            @if($events->isEmpty())
                <div class="text-center py-24 card">
                    <span class="text-4xl opacity-50 dark:opacity-30 transition-opacity">🎫</span>
                    <h3 class="mt-4 text-lg font-bold text-gray-900 dark:text-white tracking-tight transition-colors">No events found</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto transition-colors">There are currently no upcoming events. Be the first to create an amazing experience.</p>
                    @auth
                        <a href="{{ route('events.create') }}" class="mt-6 inline-block btn-vercel">Create Event</a>
                    @endauth
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($events as $event)
                        <div class="group relative card flex flex-col h-full hover:shadow-xl hover:shadow-black/5 dark:hover:shadow-white/5 hover:-translate-y-1 transition-all duration-300">
                            <!-- Image Header -->
                            <div class="w-full aspect-[16/9] relative overflow-hidden border-b border-gray-100 dark:border-white/10 bg-white/30 dark:bg-white/5 flex items-center justify-center transition-colors">
                                @if($event->poster_image)
                                    <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-tr from-white/20 dark:from-white/10 to-transparent transition-colors"></div>
                                    <span class="relative text-5xl opacity-40 mix-blend-overlay transition-transform duration-500 group-hover:scale-110">🎟️</span>
                                @endif
                                
                                <!-- Category Badge Overlay -->
                                <div class="absolute top-4 left-4">
                                    <span class="bg-white/90 dark:bg-black/90 backdrop-blur-sm border border-black/5 dark:border-white/5 text-gray-800 dark:text-gray-200 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-md shadow-sm transition-colors">
                                        {{ $event->category->name }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug group-hover:text-black dark:group-hover:text-gray-200 transition-colors line-clamp-2">
                                    <a href="{{ route('events.show', $event) }}">
                                        <span class="absolute inset-0"></span>
                                        {{ $event->title }}
                                    </a>
                                </h3>
                                
                                <div class="mt-4 space-y-2">
                                    <div class="flex items-start text-sm text-gray-500 dark:text-gray-400 font-medium transition-colors">
                                        <svg class="w-4 h-4 mr-2.5 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span>{{ $event->date->format('M j, Y') }} at {{ date('g:i A', strtotime($event->time)) }}</span>
                                    </div>
                                    <div class="flex items-start text-sm text-gray-500 dark:text-gray-400 font-medium transition-colors">
                                        <svg class="w-4 h-4 mr-2.5 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span class="line-clamp-1">{{ $event->location }}</span>
                                    </div>
                                </div>
                                
                                <!-- Footer -->
                                <div class="mt-6 pt-5 flex items-center justify-between border-t border-gray-100 dark:border-white/10 transition-colors">
                                    <div class="flex items-center gap-2 {{ $event->remaining > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }} transition-colors">
                                        <div class="w-1.5 h-1.5 rounded-full bg-current {{ $event->remaining > 0 ? 'animate-pulse' : '' }}"></div>
                                        <span class="text-[11px] font-bold uppercase tracking-widest">
                                            {{ $event->remaining > 0 ? $event->remaining . ' Tickets Left' : 'Sold Out' }}
                                        </span>
                                    </div>
                                    
                                    <span class="text-sm font-semibold text-gray-400 dark:text-gray-500 group-hover:text-black dark:group-hover:text-white transition-colors flex items-center">
                                        Details <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
