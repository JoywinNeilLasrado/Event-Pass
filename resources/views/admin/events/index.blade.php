<x-admin.layout>
    <x-slot name="title">Manage Events</x-slot>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">🗓️ All Events (including deleted)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Owner</th>
                        <th class="px-6 py-3 text-left">Category</th>
                        <th class="px-6 py-3 text-center">Date</th>
                        <th class="px-6 py-3 text-center">Tickets</th>
                        <th class="px-6 py-3 text-center">Bookings</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($events as $event)
                        <tr class="hover:bg-gray-50 transition {{ $event->deleted_at ? 'opacity-60' : '' }}">
                            <td class="px-6 py-3 font-medium text-gray-900 max-w-xs truncate">{{ $event->title }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $event->user->name }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $event->category->name }}</td>
                            <td class="px-6 py-3 text-center text-gray-500">{{ $event->date->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-center text-gray-500">{{ $event->available_tickets }}</td>
                            <td class="px-6 py-3 text-center text-gray-600 font-medium">{{ $event->bookings_count }}</td>
                            <td class="px-6 py-3 text-center">
                                @if($event->deleted_at)
                                    <span class="text-xs bg-red-100 text-red-600 rounded-full px-2 py-0.5">Deleted</span>
                                @else
                                    <span class="text-xs bg-green-100 text-green-700 rounded-full px-2 py-0.5">Active</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right space-x-1">
                                @if($event->deleted_at)
                                    <form action="{{ route('admin.events.restore', $event->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:opacity-80 transition">♻️ Restore</button>
                                    </form>
                                    <form action="{{ route('admin.events.force-destroy', $event->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('PERMANENTLY delete this event? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs px-3 py-1 bg-red-600 text-white rounded-lg hover:opacity-80 transition">💥 Purge</button>
                                    </form>
                                @else
                                    <a href="{{ route('events.show', $event->id) }}" target="_blank"
                                       class="text-xs px-3 py-1 bg-gray-100 text-gray-600 rounded-lg hover:opacity-80 transition">👁 View</a>
                                    <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Soft delete this event?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:opacity-80 transition">🗑 Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $events->links() }}</div>
    </div>
</x-admin.layout>
