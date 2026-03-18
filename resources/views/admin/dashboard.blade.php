<x-admin.layout>
    <x-slot name="title">Overview</x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach([
            ['label' => 'Total Users',     'value' => $stats['total_users'],    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>'],
            ['label' => 'Active Events',   'value' => $stats['active_events'],  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'],
            ['label' => 'Deleted Events',  'value' => $stats['deleted_events'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>'],
            ['label' => 'Total Bookings',  'value' => $stats['total_bookings'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>'],
        ] as $s)
            <div class="card bg-white p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition-transform duration-300">
                <div class="flex items-center justify-between text-gray-500">
                    <span class="text-xs font-bold uppercase tracking-widest">{{ $s['label'] }}</span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $s['icon'] !!}</svg>
                </div>
                <div>
                    <p class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ $s['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Recent Bookings --}}
        <div class="card overflow-hidden bg-white">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-900 tracking-tight">Recent Bookings</h3>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs font-semibold text-gray-500 hover:text-black transition-colors uppercase tracking-wider hidden sm:block">View all →</a>
            </div>
            <div class="divide-y divide-gray-50 bg-white">
                @forelse($recentBookings as $booking)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $booking->user->name }}</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5">{{ $booking->event->title }}</p>
                        </div>
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 border border-gray-200 px-2.5 py-1 rounded-md">{{ $booking->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-sm font-medium">No bookings yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Events --}}
        <div class="card overflow-hidden bg-white">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="font-bold text-gray-900 tracking-tight">Recent Events</h3>
                <a href="{{ route('admin.events.index') }}" class="text-xs font-semibold text-gray-500 hover:text-black transition-colors uppercase tracking-wider hidden sm:block">View all →</a>
            </div>
            <div class="divide-y divide-gray-50 bg-white">
                @forelse($recentEvents as $event)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $event->title }}</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5">by {{ $event->user->name }} · <span class="text-gray-400">{{ $event->category->name }}</span></p>
                        </div>
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 border border-gray-200 px-2.5 py-1 rounded-md">{{ $event->date->format('M j') }}</span>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-medium">No events yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin.layout>
