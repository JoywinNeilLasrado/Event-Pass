<x-admin.layout>
    <x-slot name="title">Manage Users</x-slot>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">👥 All Users ({{ $users->total() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-center">Role</th>
                        <th class="px-6 py-3 text-center">Events</th>
                        <th class="px-6 py-3 text-center">Bookings</th>
                        <th class="px-6 py-3 text-center">Joined</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 font-medium text-gray-900">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold uppercase flex-shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-3 text-center">
                                @if($user->is_admin)
                                    <span class="text-xs bg-indigo-100 text-indigo-700 rounded-full px-2 py-0.5 font-semibold">Admin</span>
                                @else
                                    <span class="text-xs bg-gray-100 text-gray-500 rounded-full px-2 py-0.5">User</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ $user->events_count }}</td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ $user->bookings_count }}</td>
                            <td class="px-6 py-3 text-center text-gray-400">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-xs px-3 py-1 {{ $user->is_admin ? 'bg-yellow-100 text-yellow-700' : 'bg-indigo-100 text-indigo-700' }} rounded-lg hover:opacity-80 transition">
                                        {{ $user->is_admin ? '⬇ Revoke Admin' : '⬆ Make Admin' }}
                                    </button>
                                </form>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Delete user {{ $user->name }}?')">
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
        <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
    </div>
</x-admin.layout>
