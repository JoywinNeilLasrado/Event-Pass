<x-admin.layout>
    <x-slot name="title">Manage Bookings</x-slot>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex justify-between items-center transition-colors">
            <div class="flex items-center gap-3">
                <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors">All Bookings</h3>
                <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 px-2 py-0.5 rounded-md shadow-sm transition-colors">{{ $bookings->total() }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-white/10 text-sm transition-colors">
                <thead class="bg-[#FAFAFA] dark:bg-[#111] transition-colors">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">User</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Event</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Organizer</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Event Date</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Booked</th>
                        <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/5 transition-colors">
                    @foreach($bookings as $booking)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white transition-colors">{{ $booking->user->name }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium transition-colors">{{ $booking->event->title }}</td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-medium transition-colors">{{ $booking->event->user->name }}</td>
                            <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap transition-colors">{{ $booking->event->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 text-[11px] font-medium uppercase tracking-wide whitespace-nowrap transition-colors">{{ $booking->created_at->format('M d, Y g:i A') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST"
                                      onsubmit="return confirm('Cancel this booking and restore seat?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors focus:outline-none">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 transition-colors">{{ $bookings->links() }}</div>
    </div>
</x-admin.layout>
