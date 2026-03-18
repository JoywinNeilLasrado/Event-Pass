<x-admin.layout>
    <x-slot name="title">Manage Bookings</x-slot>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">🎫 All Bookings ({{ $bookings->total() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-3 text-left">User</th>
                        <th class="px-6 py-3 text-left">Event</th>
                        <th class="px-6 py-3 text-left">Organizer</th>
                        <th class="px-6 py-3 text-center">Event Date</th>
                        <th class="px-6 py-3 text-center">Booked</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $booking->user->name }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $booking->event->title }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $booking->event->user->name }}</td>
                            <td class="px-6 py-3 text-center text-gray-500">{{ $booking->event->date->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-center text-gray-400 text-xs">{{ $booking->created_at->format('M d, Y g:i A') }}</td>
                            <td class="px-6 py-3 text-right">
                                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST"
                                      onsubmit="return confirm('Cancel this booking and restore seat?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:opacity-80 transition">❌ Cancel</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $bookings->links() }}</div>
    </div>
</x-admin.layout>
