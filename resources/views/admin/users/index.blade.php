<x-admin.layout>
    <x-slot name="title">Manage Users</x-slot>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex justify-between items-center transition-colors">
            <div class="flex items-center gap-3">
                <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors">All Users</h3>
                <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 px-2 py-0.5 rounded-md shadow-sm transition-colors">{{ $users->total() }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-white/10 text-sm transition-colors">
                <thead class="bg-[#FAFAFA] dark:bg-[#111] transition-colors">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Name</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Email</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Role</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Events</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Bookings</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Joined</th>
                        <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/5 transition-colors">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 flex items-center justify-center text-xs font-bold uppercase shadow-sm flex-shrink-0 transition-colors">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-medium transition-colors">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($user->is_admin)
                                    <span class="text-[10px] bg-gray-900 dark:bg-white text-white dark:text-black rounded-md px-2 py-0.5 font-bold uppercase tracking-widest shadow-sm transition-colors">Admin</span>
                                @else
                                    <span class="text-[10px] bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest transition-colors">User</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400 font-medium transition-colors">{{ $user->events_count }}</td>
                            <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400 font-medium transition-colors">{{ $user->bookings_count }}</td>
                            <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-500 text-[11px] font-medium uppercase tracking-wide transition-colors">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right space-x-4">
                                <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors focus:outline-none">
                                        {{ $user->is_admin ? 'Revoke Admin' : 'Make Admin' }}
                                    </button>
                                </form>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Delete user {{ $user->name }}?')">
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
        <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 transition-colors">{{ $users->links() }}</div>
    </div>
</x-admin.layout>
