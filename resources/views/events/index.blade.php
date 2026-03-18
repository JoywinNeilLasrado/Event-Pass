<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">🗓️ Upcoming Events</h2>
            @auth
                <a href="{{ route('events.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                    + Create Event
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 rounded-lg px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if($events->isEmpty())
                <div class="text-center py-20 text-gray-500">
                    <p class="text-2xl font-light">No events yet. Be the first to create one!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($events as $event)
                        <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                            @if($event->poster_image)
                                <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-4xl">🎟️</span>
                                </div>
                            @endif
                            <div class="p-5">
                                <span class="text-xs font-semibold bg-indigo-100 text-indigo-700 rounded-full px-3 py-1">
                                    {{ $event->category->name }}
                                </span>
                                <h3 class="mt-3 text-lg font-bold text-gray-900 truncate">{{ $event->title }}</h3>
                                <p class="mt-1 text-sm text-gray-500">📍 {{ $event->location }}</p>
                                <p class="text-sm text-gray-500">📅 {{ $event->date->format('M d, Y') }} at {{ $event->time }}</p>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-sm font-medium {{ $event->available_tickets > 0 ? 'text-green-600' : 'text-red-500' }}">
                                        🎫 {{ $event->available_tickets }} tickets left
                                    </span>
                                    <div class="flex gap-2">
                                        @foreach($event->tags->take(2) as $tag)
                                            <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-2 py-0.5">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <a href="{{ route('events.show', $event) }}" class="mt-4 block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 rounded-lg transition">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
