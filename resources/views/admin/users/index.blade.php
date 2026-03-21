<x-admin.layout>
    <x-slot name="title">Manage Users</x-slot>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex flex-col xl:flex-row justify-between xl:items-center gap-4 transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors whitespace-nowrap">Users Directory</h3>
                    <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 px-2 py-0.5 rounded-md shadow-sm transition-colors">{{ $users->total() }}</span>
                </div>
                <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full sm:w-64">
                    @if(request('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search names or emails..." class="form-input-vercel w-full pl-9 h-8 text-xs py-1 rounded border border-gray-200 dark:border-white/10 bg-transparent text-gray-900 dark:text-white focus:ring-1 focus:ring-black dark:focus:ring-white">
                </form>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.users.index', ['search' => request('search')]) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ !request('role') ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">All</a>
                <a href="{{ route('admin.users.index', ['role' => 'organizer', 'search' => request('search')]) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ request('role') === 'organizer' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Organizers</a>
                <a href="{{ route('admin.users.index', ['role' => 'staff', 'search' => request('search')]) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ request('role') === 'staff' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Staff Scanner</a>
                <a href="{{ route('admin.users.index', ['role' => 'user', 'search' => request('search')]) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ request('role') === 'user' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Base Users</a>
                <a href="{{ route('admin.users.index', ['role' => 'admin', 'search' => request('search')]) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ request('role') === 'admin' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Admins</a>
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
                                @elseif($user->is_organizer)
                                    <span class="text-[10px] bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/50 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest transition-colors">Organizer</span>
                                @elseif($user->employer_id)
                                    <span class="text-[10px] bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest transition-colors">Staff</span>
                                    <div class="mt-1.5 text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">For: {{ explode(' ', $user->employer->name ?? '')[0] }}</div>
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
