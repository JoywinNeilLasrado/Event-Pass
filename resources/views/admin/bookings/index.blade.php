<x-admin.layout>
    <x-slot name="title">Manage Bookings</x-slot>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex flex-col sm:flex-row justify-between sm:items-center gap-4 transition-colors">
            <div class="flex items-center gap-3">
                <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors whitespace-nowrap">Bookings Directory</h3>
                <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 px-2 py-0.5 rounded-md shadow-sm transition-colors">{{ $bookings->total() }}</span>
            </div>
            
            <form action="{{ route('admin.bookings.index') }}" method="GET" class="relative w-full sm:w-64 shrink-0">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users, events or organizers..." class="form-input-vercel w-full pl-9 h-8 text-xs py-1 rounded border border-gray-200 dark:border-white/10 bg-transparent text-gray-900 dark:text-white focus:ring-1 focus:ring-black dark:focus:ring-white">
            </form>
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
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white transition-colors">{{ $booking->user ? $booking->user->name : 'Unknown User' }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium transition-colors">
                                {{ $booking->event ? $booking->event->title : 'Deleted Event' }}
                                @if($booking->event && $booking->event->trashed())
                                    <span class="ml-2 text-[9px] text-red-500 font-bold uppercase tracking-widest">(Trashed)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-medium transition-colors">{{ $booking->event && $booking->event->user ? $booking->event->user->name : 'Unknown' }}</td>
                            <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap transition-colors">{{ $booking->event ? $booking->event->date->format('M d, Y') : 'N/A' }}</td>
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
