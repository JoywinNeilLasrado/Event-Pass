<x-admin.layout>
    <x-slot name="title">Admin Dashboard</x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        @foreach([
            ['label' => 'Total Users',     'value' => $stats['total_users'],    'icon' => '👥', 'color' => 'bg-blue-500'],
            ['label' => 'Active Events',   'value' => $stats['active_events'],  'icon' => '🗓️', 'color' => 'bg-indigo-500'],
            ['label' => 'Deleted Events',  'value' => $stats['deleted_events'], 'icon' => '🗑️', 'color' => 'bg-red-400'],
            ['label' => 'Total Bookings',  'value' => $stats['total_bookings'], 'icon' => '🎫', 'color' => 'bg-green-500'],
        ] as $s)
            <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 {{ $s['color'] }} rounded-xl flex items-center justify-center text-2xl flex-shrink-0">{{ $s['icon'] }}</div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $s['value'] }}</p>
                    <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent Bookings --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">🎫 Recent Bookings</h3>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs text-indigo-600 hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentBookings as $booking)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $booking->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $booking->event->title }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ $booking->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="px-6 py-4 text-sm text-gray-400">No bookings yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Events --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">🗓️ Recent Events</h3>
                <a href="{{ route('admin.events.index') }}" class="text-xs text-indigo-600 hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentEvents as $event)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $event->title }}</p>
                            <p class="text-xs text-gray-400">by {{ $event->user->name }} · {{ $event->category->name }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ $event->date->format('M d') }}</span>
                    </div>
                @empty
                    <p class="px-6 py-4 text-sm text-gray-400">No events yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin.layout>
