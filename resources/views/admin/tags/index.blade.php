<x-admin.layout>
    <x-slot name="title">Manage Tags</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Add Tag Form --}}
        <div>
            <div class="card p-6 sm:p-8 transition-colors">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight mb-6 transition-colors">Add Tag</h3>
                <form action="{{ route('admin.tags.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Tag Name <span class="text-red-500 dark:text-red-400">*</span></label>
                        <input type="text" name="name" placeholder="e.g. VIP" required
                            class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors placeholder-gray-400 dark:placeholder-gray-600">
                        @error('name') <p class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <button class="btn-vercel w-full py-3 text-sm mt-3">
                        Create Tag
                    </button>
                </form>
            </div>
        </div>

        {{-- Tags List --}}
        <div class="lg:col-span-2 card overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex justify-between items-center transition-colors">
                <div class="flex items-center gap-3">
                    <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors">All Tags</h3>
                    <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 px-2 py-0.5 rounded-md shadow-sm transition-colors">{{ $tags->count() }}</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-white/10 text-sm transition-colors">
                    <thead class="bg-[#FAFAFA] dark:bg-[#111] transition-colors">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Name</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Slug</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Events</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5 transition-colors">
                        @foreach($tags as $tag)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white transition-colors">{{ $tag->name }}</td>
                                <td class="px-6 py-4 text-gray-400 dark:text-gray-500 font-mono text-xs transition-colors">{{ $tag->slug }}</td>
                                <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400 font-medium transition-colors">{{ $tag->events_count }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST"
                                          onsubmit="return confirm('Delete tag {{ $tag->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors focus:outline-none">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin.layout>
