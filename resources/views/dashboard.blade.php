<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight transition-colors">
                Welcome, {{ explode(' ', Auth::user()->name)[0] }}
            </h2>
            <a href="{{ route('events.index') }}" class="btn-vercel-secondary text-sm hidden sm:block">Browse Events</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg px-4 py-3 text-sm font-semibold shadow-sm transition-colors">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg px-4 py-3 text-sm font-semibold shadow-sm transition-colors">{{ session('error') }}</div>
            @endif

            {{-- ── STATS ROW ── --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Stat 1 -->
                <div class="card p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center justify-between text-gray-500 dark:text-gray-400 transition-colors">
                        <span class="text-xs font-bold uppercase tracking-widest">Events Created</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight transition-colors">{{ $stats['events_created'] }}</p>
                    </div>
                </div>
                <!-- Stat 2 -->
                <div class="card p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center justify-between text-gray-500 dark:text-gray-400 transition-colors">
                        <span class="text-xs font-bold uppercase tracking-widest">Tickets Booked</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <div>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight transition-colors">{{ $stats['tickets_booked'] }}</p>
                    </div>
                </div>
                <!-- Stat 3 -->
                <div class="card p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center justify-between text-gray-500 dark:text-gray-400 transition-colors">
                        <span class="text-xs font-bold uppercase tracking-widest">Total Attendees</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight transition-colors">{{ $stats['total_attendees'] }}</p>
                    </div>
                </div>
            </div>

            {{-- ── MY EVENTS ── --}}
            <div class="card overflow-hidden" x-data="{ searchEvents: '' }">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/10 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 transition-colors">
                    <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors whitespace-nowrap">My Events</h3>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto sm:justify-end">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="searchEvents" class="form-input text-sm w-full pl-9 py-2 rounded-full border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-black/20 focus:bg-white dark:focus:bg-black/50 focus:ring-black/20 dark:focus:ring-white/20 transition-all shadow-sm" placeholder="Filter events...">
                        </div>
                        <a href="{{ route('events.create') }}" class="btn-vercel text-sm px-4 py-2 text-center whitespace-nowrap">+ New Event</a>
                    </div>
                </div>

                @if($myEvents->isEmpty())
                    <div class="text-center py-16">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-4 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 transition-colors">You haven't created any events yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50 dark:divide-white/5 transition-colors">
                        @foreach($myEvents as $event)
                            <div class="p-6 transition-colors hover:bg-white/30 dark:hover:bg-black/30 @if($event->deleted_at) opacity-60 grayscale @endif"
                                 x-show="searchEvents === '' || {{ json_encode(strtolower($event->title)) }}.includes(searchEvents.toLowerCase())"
                                 x-transition.opacity.duration.300ms>
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 flex-wrap mb-1">
                                            <a href="{{ $event->deleted_at ? '#' : route('events.show', $event->id) }}"
                                               class="font-bold text-lg text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors tracking-tight">
                                                {{ $event->title }}
                                            </a>
                                            @if($event->deleted_at)
                                                <span class="text-[10px] bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest shadow-sm transition-colors">Deleted</span>
                                            @endif
                                            <span class="text-[10px] bg-white/50 dark:bg-black/50 backdrop-blur-sm border border-white/50 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest shadow-sm transition-colors">{{ $event->category->name }}</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-4 text-sm font-medium text-gray-500 dark:text-gray-400 mt-2 transition-colors">
                                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{ $event->date->format('M j, Y') }}</span>
                                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $event->location }}</span>
                                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg> {{ $event->remaining }} seats left</span>
                                        </div>

                                        {{-- Nav to Dedicated Attendees Page --}}
                                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/10 transition-colors">
                                            <div class="flex items-center justify-between mb-0">
                                                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest transition-colors mb-0">
                                                    Attendees ({{ $event->bookings_count }})
                                                </p>
                                                @php $checkedInCount = $event->bookings->where('is_checked_in', true)->count(); @endphp
                                                <p class="text-[10px] font-bold {{ $checkedInCount > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-600' }} uppercase tracking-widest transition-colors mb-0">
                                                    {{ $checkedInCount }} / {{ $event->bookings_count }} Checked In
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @if(!$event->deleted_at)
                                        <div class="flex sm:flex-col gap-3 flex-shrink-0 mt-4 sm:mt-0">
                                            <a href="{{ route('events.attendees', $event->id) }}"
                                               class="btn-vercel text-xs px-4 py-2 text-center flex-1 sm:flex-none">Manage Attendees</a>
                                            <a href="{{ route('events.edit', $event->id) }}"
                                               class="btn-vercel-secondary text-xs px-4 py-2 text-center flex-1 sm:flex-none">Edit Event</a>
                                            <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                                  onsubmit="return confirm('Soft-delete this event? It can be restored later.')">
                                                @csrf @method('DELETE')
                                                <button class="w-full text-xs font-semibold text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 transition-colors py-1 focus:outline-none">Delete</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── MY BOOKINGS ── --}}
            <div class="card overflow-hidden mb-20">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex justify-between items-center transition-colors">
                    <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors">My Tickets</h3>
                </div>

                @if($myBookings->isEmpty())
                    <div class="text-center py-16">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-4 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4 transition-colors">You haven't booked any tickets yet.</p>
                        <a href="{{ route('events.index') }}" class="btn-vercel-secondary text-sm px-6 py-2">Browse Events &rarr;</a>
                    </div>
                @else
                    <div class="divide-y divide-gray-50 dark:divide-white/5 transition-colors">
                        @foreach($myBookings as $booking)
                            @php $event = $booking->event; @endphp
                            <div class="p-6 transition-colors hover:bg-white/30 dark:hover:bg-black/30">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-3 flex-wrap mb-1">
                                            @if($event->deleted_at)
                                                <span class="font-bold text-lg text-gray-400 dark:text-gray-500 line-through tracking-tight transition-colors">{{ $event->title }}</span>
                                                <span class="text-[10px] bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest shadow-sm transition-colors">Event Deleted</span>
                                            @else
                                                <a href="{{ route('events.show', $event->id) }}"
                                                   class="font-bold text-lg text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors tracking-tight">
                                                    {{ $event->title }}
                                                </a>
                                                <span class="text-[10px] bg-white/50 dark:bg-black/50 backdrop-blur-sm border border-white/50 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest shadow-sm transition-colors">{{ $event->category->name }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center gap-4 text-sm font-medium text-gray-500 dark:text-gray-400 mt-2 transition-colors">
                                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{ $event->date->format('M j, Y') }} at {{ $event->time }}</span>
                                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $event->location }}</span>
                                        </div>
                                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 mt-3 uppercase tracking-wide transition-colors">
                                            Ticket Reserved &bull; {{ $booking->created_at->format('M d, Y') }}
                                        </p>
                                    </div>

                                    @if(!$event->deleted_at)
                                        <div class="flex-shrink-0 mt-4 sm:mt-0 flex items-center gap-3">
                                            <a href="{{ route('bookings.ticket', $event->id) }}" class="btn-vercel text-xs px-4 py-2 text-center" target="_blank">
                                                View Ticket
                                            </a>
                                            <form action="{{ route('bookings.destroy', $event->id) }}" method="POST"
                                                  onsubmit="return confirm('Cancel your ticket for this event?')">
                                                @csrf @method('DELETE')
                                                <button class="btn-vercel-secondary text-xs px-4 py-2 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-800 transition-colors focus:outline-none">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
