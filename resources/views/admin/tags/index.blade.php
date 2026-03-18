<x-admin.layout>
    <x-slot name="title">Manage Tags</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Add Tag Form --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">➕ Add Tag</h3>
            <form action="{{ route('admin.tags.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <input type="text" name="name" placeholder="Tag name" required
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button class="w-full py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                    Create
                </button>
            </form>
        </div>

        {{-- Tags List --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">🔖 All Tags ({{ $tags->count() }})</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Slug</th>
                        <th class="px-6 py-3 text-center">Events</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tags as $tag)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $tag->name }}</td>
                            <td class="px-6 py-3 text-gray-400 font-mono text-xs">{{ $tag->slug }}</td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ $tag->events_count }}</td>
                            <td class="px-6 py-3 text-right">
                                <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST"
                                      onsubmit="return confirm('Delete tag {{ $tag->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:opacity-80">🗑 Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin.layout>
