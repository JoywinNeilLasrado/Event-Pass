<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight line-clamp-1 truncate">{{ $event->title }}</h2>
            <a href="{{ route('events.index') }}" class="text-sm font-medium text-gray-500 hover:text-black transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Events
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-8 card bg-green-50 border-green-200 text-green-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-8 card bg-red-50 border-red-200 text-red-800 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
            @endif

            <div class="card bg-white overflow-hidden shadow-md">
                <!-- Hero Image Section -->
                <div class="w-full h-72 sm:h-[400px] relative bg-[#f3f4f6] flex items-center justify-center border-b border-gray-100">
                    @if($event->poster_image)
                        <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 bg-gradient-to-tr from-[#f3f4f6] to-[#e5e7eb]"></div>
                        <span class="relative text-7xl opacity-20 mix-blend-multiply">🎟️</span>
                    @endif

                    <div class="absolute top-6 left-6 flex flex-wrap gap-2">
                        <span class="bg-white/90 backdrop-blur-sm border border-black/5 text-gray-800 text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md shadow-sm">
                            {{ $event->category->name }}
                        </span>
                        @foreach($event->tags as $tag)
                            <span class="bg-black/40 backdrop-blur-sm text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-md">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="p-8 sm:p-12">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-16">
                        <!-- Main Content (Left Column) -->
                        <div class="lg:col-span-2">
                            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-8">{{ $event->title }}</h1>
                            
                            <div class="prose prose-lg text-gray-600 prose-headings:text-black prose-a:text-indigo-600 font-light leading-relaxed">
                                {!! nl2br(e($event->description)) !!}
                            </div>
                        </div>

                        <!-- Sidebar Metadata (Right Column) -->
                        <div class="space-y-8">
                            <div class="card bg-[#FAFAFA] border border-gray-100 p-6 shadow-sm">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Event Details</h3>
                                
                                <div class="space-y-5">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 mr-4 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div class="pt-0.5">
                                            <p class="text-sm font-semibold text-gray-900">{{ $event->date->format('F j, Y') }}</p>
                                            <p class="text-sm text-gray-500">{{ date('g:i A', strtotime($event->time)) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 mr-4 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                        <div class="pt-0.5">
                                            <p class="text-sm font-semibold text-gray-900 leading-snug">{{ $event->location }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 mr-4 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <div class="pt-0.5">
                                            <p class="text-sm font-semibold text-gray-900">Organized by</p>
                                            <p class="text-sm text-gray-500">{{ $event->user->name }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 mr-4 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                        </div>
                                        <div class="pt-0.5">
                                            <p class="text-sm font-semibold {{ $event->available_tickets > 0 ? 'text-green-600' : 'text-red-500' }}">
                                                {{ $event->available_tickets }} Tickets Available
                                            </p>
                                            @if($event->bookings->count() > 0)
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $event->bookings->count() }} people attending</p>
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
                                                <button type="submit" class="inline-flex items-center justify-center bg-white text-red-600 border border-red-200 hover:border-red-300 hover:bg-red-50 transition-all duration-200 px-4 rounded-lg font-medium shadow-sm active:translate-y-px">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($hasBooked ?? false)
                                        <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 text-center">
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
                                            <button type="submit" class="w-full text-center text-sm font-medium text-gray-500 hover:text-red-600 mt-2 transition-colors">
                                                Cancel reservation
                                            </button>
                                        </form>
                                    @elseif($event->available_tickets > 0)
                                        <form action="{{ route('bookings.store', $event) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-vercel w-full text-lg py-4 shadow-lg shadow-black/10">Reserve a Spot</button>
                                        </form>
                                    @else
                                        <div class="bg-gray-100 border border-gray-200 text-gray-600 rounded-lg p-4 text-center font-semibold">
                                            Sold Out
                                        </div>
                                    @endif
                                @else
                                    <div class="card bg-[#FAFAFA] border-gray-100 p-6 text-center shadow-none mb-0">
                                        <p class="text-sm font-medium text-gray-900 mb-4">Join the experience.</p>
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
