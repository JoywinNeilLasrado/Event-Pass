<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight line-clamp-1 truncate transition-colors">{{ $event->title }}</h2>
            <a href="{{ route('events.index') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Events
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="card bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 text-sm font-medium transition-colors">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="card bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 text-sm font-medium transition-colors">{{ session('error') }}</div>
            @endif

            <div class="card overflow-hidden shadow-md">
                <!-- Hero Image Section -->
                <div class="w-full h-72 sm:h-[400px] relative bg-white/30 dark:bg-white/5 backdrop-blur-sm flex items-center justify-center border-b border-gray-100 dark:border-white/10 transition-colors">
                    @if($event->poster_image)
                        <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 bg-gradient-to-tr from-white/20 dark:from-white/10 to-transparent"></div>
                        <span class="relative text-7xl opacity-40 mix-blend-overlay transition-opacity">🎟️</span>
                    @endif

                    <div class="absolute top-6 left-6 flex flex-wrap gap-2">
                        <span class="bg-white/90 dark:bg-black/90 backdrop-blur-sm border border-black/5 dark:border-white/5 text-gray-800 dark:text-gray-200 text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-sm transition-colors">
                            {{ $event->category->name }}
                        </span>
                        @foreach($event->tags as $tag)
                            <span class="bg-black/40 dark:bg-white/10 backdrop-blur-sm text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md transition-colors">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="p-8 sm:p-12">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-16">
                        <!-- Main Content (Left Column) -->
                        <div class="lg:col-span-2">
                            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight mb-8 transition-colors">{{ $event->title }}</h1>
                            
                            <div class="prose prose-lg text-gray-600 dark:text-gray-400 prose-headings:text-black dark:prose-headings:text-white prose-a:text-indigo-600 dark:prose-a:text-indigo-400 font-light leading-relaxed transition-colors">
                                {!! nl2br(e($event->description)) !!}
                            </div>
                        </div>

                        <!-- Sidebar Metadata (Right Column) -->
                        <div class="space-y-8">
                            <div class="card border border-gray-100 dark:border-white/10 p-6 shadow-sm transition-colors">
                                <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-6 transition-colors">Event Details</h3>
                                
                                <div class="space-y-5">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/50 dark:bg-white/10 backdrop-blur-sm border border-gray-200 dark:border-white/10 flex items-center justify-center text-gray-500 dark:text-gray-400 mr-4 shadow-sm transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div class="pt-0.5">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white transition-colors">{{ $event->date->format('F j, Y') }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 transition-colors">{{ date('g:i A', strtotime($event->time)) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/50 dark:bg-white/10 backdrop-blur-sm border border-gray-200 dark:border-white/10 flex items-center justify-center text-gray-500 dark:text-gray-400 mr-4 shadow-sm transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                        <div class="pt-0.5">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white leading-snug transition-colors">{{ $event->location }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/50 dark:bg-white/10 backdrop-blur-sm border border-gray-200 dark:border-white/10 flex items-center justify-center text-gray-500 dark:text-gray-400 mr-4 shadow-sm transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <div class="pt-0.5">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white transition-colors">Organized by</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 transition-colors">{{ $event->user->name }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/50 dark:bg-white/10 backdrop-blur-sm border border-gray-200 dark:border-white/10 flex items-center justify-center text-gray-500 dark:text-gray-400 mr-4 shadow-sm transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                        </div>
                                        <div class="pt-0.5">
                                            <p class="text-sm font-semibold {{ $event->remaining > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }} transition-colors">
                                                {{ $event->remaining }} Tickets Available
                                            </p>
                                            @if($event->bookings->count() > 0)
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 transition-colors">{{ $event->bookings->count() }} people attending</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Area -->
                            <div class="flex flex-col gap-3">
                                @auth
                                    @if($event->user_id === auth()->id())
                                        <div class="flex gap-3">
                                            <a href="{{ route('events.edit', $event) }}" class="btn-vercel-secondary flex-1 text-center">Edit Event</a>
                                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="flex" onsubmit="return confirm('Soft-delete this event? It can be restored later.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center bg-white/40 dark:bg-white/5 backdrop-blur-md text-red-600 dark:text-red-500 border border-red-200 dark:border-red-900/50 hover:border-red-300 dark:hover:border-red-800 hover:bg-red-50 dark:hover:bg-white/10 transition-colors duration-200 px-4 rounded-lg font-medium shadow-sm active:translate-y-px focus:outline-none">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($hasBooked ?? false)
                                        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg p-4 text-center transition-colors">
                                            <p class="font-bold mb-1 flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                You're going!
                                            </p>
                                            <p class="text-xs">Your ticket is confirmed.</p>
                                        </div>
                                        <form action="{{ route('bookings.destroy', $event) }}" method="POST"
                                              onsubmit="return confirm('Cancel your ticket? The seat will be returned.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 mt-2 transition-colors focus:outline-none">
                                                Cancel reservation
                                            </button>
                                        </form>
                                    @elseif($event->remaining > 0)
                                        <div class="space-y-4 mt-2">
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 tracking-tight">Select Ticket Package</h4>
                                            @foreach($event->ticketTypes as $tier)
                                                <div class="p-5 bg-white/40 dark:bg-black/40 backdrop-blur-md border {{ $tier->remaining > 0 ? 'border-gray-200 dark:border-white/10 hover:border-indigo-300 dark:hover:border-indigo-500/50' : 'border-gray-100 dark:border-white/5 opacity-60' }} rounded-xl shadow-sm transition-all group relative overflow-hidden">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <div>
                                                            <h5 class="font-bold text-gray-900 dark:text-white tracking-tight text-lg">{{ $tier->name }}</h5>
                                                            @if($tier->description)
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 leading-relaxed">{{ $tier->description }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="text-right flex-shrink-0 ml-4">
                                                            <p class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight">{{ $tier->price > 0 ? '$' . number_format($tier->price, 2) : 'FREE' }}</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-5 flex items-center justify-between border-t border-gray-100 dark:border-white/5 pt-4">
                                                        @if($tier->remaining > 0)
                                                            <span class="text-[10px] font-bold text-green-600 dark:text-green-400 uppercase tracking-widest bg-green-50 dark:bg-green-900/30 px-2.5 py-1 rounded-md shadow-sm">
                                                                {{ $tier->remaining }} Tickets Left
                                                            </span>
                                                            <form action="{{ route('bookings.store', $event) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="ticket_type_id" value="{{ $tier->id }}">
                                                                <button type="submit" class="btn-vercel text-sm px-6 py-2 shadow-md hover:-translate-y-0.5 transition-transform">Select</button>
                                                            </form>
                                                        @else
                                                            <span class="text-[10px] font-bold text-red-500 dark:text-red-400 uppercase tracking-widest bg-red-50 dark:bg-red-900/30 px-2.5 py-1 rounded-md shadow-sm">
                                                                Sold Out
                                                            </span>
                                                            <button disabled type="button" class="btn-vercel-secondary text-sm px-6 py-2 opacity-50 cursor-not-allowed">Unavailable</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>>
                                    @else
                                        <div class="bg-white/40 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 rounded-lg p-4 text-center font-semibold transition-colors">
                                            Sold Out
                                        </div>
                                    @endif
                                @else
                                    <div class="card border-gray-100 dark:border-white/10 p-6 text-center shadow-none mb-0 transition-colors">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-4 transition-colors">Join the experience.</p>
                                        <a href="{{ route('login') }}" class="btn-vercel w-full">Sign in to Reserve</a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
