<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            👋 Welcome back, {{ Auth::user()->name }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 rounded-lg px-4 py-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-300 text-red-800 rounded-lg px-4 py-3">{{ session('error') }}</div>
            @endif

            {{-- ── STATS ROW ── --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-4">
                    <div class="text-4xl">🗓️</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['events_created'] }}</p>
                        <p class="text-sm text-gray-500">Events Created</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-4">
                    <div class="text-4xl">🎫</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['tickets_booked'] }}</p>
                        <p class="text-sm text-gray-500">Tickets Booked by You</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-4">
                    <div class="text-4xl">👥</div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_attendees'] }}</p>
                        <p class="text-sm text-gray-500">Total Attendees on Your Events</p>
                    </div>
                </div>
            </div>

            {{-- ── MY EVENTS (with Attendees) ── --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">🗓️ My Events</h3>
                    <a href="{{ route('events.create') }}" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">+ New Event</a>
                </div>

                @if($myEvents->isEmpty())
                    <p class="text-center text-gray-400 py-10">You haven't created any events yet.</p>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($myEvents as $event)
                            <div class="p-5 @if($event->deleted_at) bg-gray-50 opacity-70 @endif">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="{{ $event->deleted_at ? '#' : route('events.show', $event->id) }}"
                                               class="font-semibold text-gray-900 hover:text-indigo-600 transition">
                                                {{ $event->title }}
                                            </a>
                                            @if($event->deleted_at)
                                                <span class="text-xs bg-red-100 text-red-600 rounded-full px-2 py-0.5">Deleted</span>
                                            @endif
                                            <span class="text-xs bg-indigo-100 text-indigo-600 rounded-full px-2 py-0.5">{{ $event->category->name }}</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-0.5">
                                            📅 {{ $event->date->format('M d, Y') }} &bull; 📍 {{ $event->location }}
                                            &bull; 🎫 {{ $event->available_tickets }} seats left
                                        </p>

                                        {{-- Attendees --}}
                                        @if($event->bookings->isEmpty())
                                            <p class="text-xs text-gray-400 mt-2 italic">No bookings yet.</p>
                                        @else
                                            <div class="mt-3">
                                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                                    Attendees ({{ $event->bookings_count }})
                                                </p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($event->bookings as $booking)
                                                        <div class="flex items-center gap-1.5 bg-gray-100 rounded-full px-3 py-1 text-xs text-gray-700">
                                                            <span class="w-6 h-6 rounded-full bg-indigo-200 text-indigo-700 flex items-center justify-center font-bold text-xs uppercase flex-shrink-0">
                                                                {{ substr($booking->user->name, 0, 1) }}
                                                            </span>
                                                            <span>{{ $booking->user->name }}</span>
                                                            <span class="text-gray-400">&bull;</span>
                                                            <span class="text-gray-400">{{ $booking->created_at->format('M d') }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @if(!$event->deleted_at)
                                        <div class="flex gap-2 flex-shrink-0">
                                            <a href="{{ route('events.edit', $event->id) }}"
                                               class="text-xs px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition font-medium">✏️ Edit</a>
                                            <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                                  onsubmit="return confirm('Soft-delete this event?')">
                                                @csrf @method('DELETE')
                                                <button class="text-xs px-3 py-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition font-medium">🗑️ Delete</button>
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
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">🎟️ My Tickets</h3>
                </div>

                @if($myBookings->isEmpty())
                    <p class="text-center text-gray-400 py-10">You haven't booked any tickets yet.
                        <a href="{{ route('events.index') }}" class="text-indigo-600 hover:underline ml-1">Browse Events →</a>
                    </p>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($myBookings as $booking)
                            @php $event = $booking->event; @endphp
                            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($event->deleted_at)
                                            <span class="font-semibold text-gray-500 line-through">{{ $event->title }}</span>
                                            <span class="text-xs bg-red-100 text-red-600 rounded-full px-2 py-0.5">Event Deleted</span>
                                        @else
                                            <a href="{{ route('events.show', $event->id) }}"
                                               class="font-semibold text-gray-900 hover:text-indigo-600 transition">
                                                {{ $event->title }}
                                            </a>
                                            <span class="text-xs bg-indigo-100 text-indigo-600 rounded-full px-2 py-0.5">{{ $event->category->name }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        📅 {{ $event->date->format('M d, Y') }} at {{ $event->time }}
                                        &bull; 📍 {{ $event->location }}
                                        &bull; Organized by <span class="font-medium">{{ $event->user->name }}</span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">Booked on {{ $booking->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>

                                @if(!$event->deleted_at)
                                    <form action="{{ route('bookings.destroy', $event->id) }}" method="POST"
                                          onsubmit="return confirm('Cancel your ticket for this event?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition font-medium flex-shrink-0">
                                            ❌ Cancel Ticket
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
