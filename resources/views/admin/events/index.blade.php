<x-admin.layout>
    <x-slot name="title">Manage Events</x-slot>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex justify-between items-center transition-colors">
            <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors">All Events</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-white/10 text-sm transition-colors">
                <thead class="bg-[#FAFAFA] dark:bg-[#111] transition-colors">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Title</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Owner</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Category</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Date</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Tickets</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Bookings</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Status</th>
                        <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/5 transition-colors">
                    @foreach($events as $event)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors {{ $event->deleted_at ? 'opacity-60 grayscale' : '' }}">
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white max-w-xs truncate transition-colors">{{ $event->title }}</td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-medium transition-colors">{{ $event->user->name }}</td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap transition-colors">{{ $event->category->name }}</td>
                            <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 text-[11px] font-bold uppercase tracking-wide whitespace-nowrap transition-colors">{{ $event->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 font-medium transition-colors">{{ $event->available_tickets }}</td>
                            <td class="px-6 py-4 text-center text-gray-900 dark:text-white font-bold transition-colors">{{ $event->bookings_count }}</td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($event->deleted_at)
                                    <span class="text-[10px] bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest transition-colors">Deleted</span>
                                @else
                                    <span class="text-[10px] bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest transition-colors">Active</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                                @if($event->deleted_at)
                                    <form action="{{ route('admin.events.restore', $event->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors focus:outline-none">Restore</button>
                                    </form>
                                    <form action="{{ route('admin.events.force-destroy', $event->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('PERMANENTLY delete this event? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors focus:outline-none">Purge</button>
                                    </form>
                                @else
                                    <a href="{{ route('events.show', $event->id) }}" target="_blank"
                                       class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors">View</a>
                                    <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Soft delete this event?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors focus:outline-none">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 transition-colors">{{ $events->links() }}</div>
    </div>
</x-admin.layout>
