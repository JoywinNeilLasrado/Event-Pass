<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $event->title }}</h2>
            <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:underline">← Back to Events</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 rounded-lg px-4 py-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 rounded-lg px-4 py-3">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                @if($event->poster_image)
                    <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover">
                @else
                    <div class="w-full h-64 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <span class="text-white text-7xl">🎟️</span>
                    </div>
                @endif

                <div class="p-8">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="text-sm font-semibold bg-indigo-100 text-indigo-700 rounded-full px-3 py-1">
                            {{ $event->category->name }}
                        </span>
                        @foreach($event->tags as $tag)
                            <span class="text-sm bg-gray-100 text-gray-600 rounded-full px-3 py-1">{{ $tag->name }}</span>
                        @endforeach
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->title }}</h1>

                    <div class="grid grid-cols-2 gap-4 text-gray-600 text-sm my-4">
                        <p>📅 {{ $event->date->format('F j, Y') }} at {{ $event->time }}</p>
                        <p>📍 {{ $event->location }}</p>
                        <p>🎫 <span class="{{ $event->available_tickets > 0 ? 'text-green-600 font-semibold' : 'text-red-500 font-semibold' }}">{{ $event->available_tickets }} tickets available</span></p>
                        <p>👤 Organized by <span class="font-medium">{{ $event->user->name }}</span></p>
                    </div>

                    <p class="text-gray-700 leading-relaxed mt-4">{{ $event->description }}</p>

                    <div class="mt-8 flex items-center gap-4">
                        @auth
                            @if($event->user_id === auth()->id())
                                <a href="{{ route('events.edit', $event) }}" class="px-5 py-2.5 bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600 transition">✏️ Edit Event</a>
                                <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Soft-delete this event? It can be recovered from the database.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">🗑️ Delete</button>
                                </form>
                            @elseif($hasBooked)
                                <div class="flex items-center gap-3">
                                    <div class="px-5 py-2.5 bg-green-100 text-green-700 font-semibold rounded-lg">✅ You have a ticket!</div>
                                    <form action="{{ route('bookings.destroy', $event) }}" method="POST"
                                          onsubmit="return confirm('Cancel your ticket? The seat will be returned.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-5 py-2.5 bg-red-100 text-red-700 font-semibold rounded-lg hover:bg-red-200 transition">
                                            ❌ Cancel Ticket
                                        </button>
                                    </form>
                                </div>
                            @elseif($event->available_tickets > 0)
                                <form action="{{ route('bookings.store', $event) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">🎟️ Book a Ticket</button>
                                </form>
                            @else
                                <div class="px-5 py-2.5 bg-red-100 text-red-700 font-semibold rounded-lg">❌ Sold Out</div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">Login to Book a Ticket</a>
                        @endauth
                    </div>

                    <p class="mt-6 text-xs text-gray-400">Total Bookings: {{ $event->bookings->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
