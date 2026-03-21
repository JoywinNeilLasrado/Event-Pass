<x-admin.layout>
    <x-slot name="title">Manage Events</x-slot>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex flex-col xl:flex-row justify-between xl:items-center gap-4 transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors whitespace-nowrap">Events Directory</h3>
                <form action="{{ route('admin.events.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2 w-full">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search titles or organizers..." class="form-input-vercel w-full pl-9 h-8 text-xs py-1 rounded border border-gray-200 dark:border-white/10 bg-transparent text-gray-900 dark:text-white focus:ring-1 focus:ring-black dark:focus:ring-white">
                    </div>
                    
                    <div class="relative w-full sm:w-40" x-data="{ open: false }">
                        <input type="hidden" name="category" value="{{ request('category') }}" id="admin-category-filter">
                        <button type="button" @click="open = !open" @click.outside="open = false" class="form-input-vercel w-full h-8 flex items-center justify-between text-xs py-1 pl-3 pr-2 text-left rounded border border-gray-200 dark:border-white/10 bg-transparent text-gray-900 dark:text-white focus:ring-1 focus:ring-black dark:focus:ring-white cursor-pointer transition-colors hover:bg-gray-50 dark:hover:bg-white/5">
                            <span class="truncate">
                                @php
                                    $selectedCategory = collect($categories)->firstWhere('id', request('category'));
                                @endphp
                                {{ $selectedCategory ? $selectedCategory->name : 'All Categories' }}
                            </span>
                            <svg class="w-3.5 h-3.5 ml-1 flex-shrink-0 transition-transform duration-200 text-gray-500" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute z-[100] left-0 right-0 mt-1 bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-white/10 rounded-lg shadow-xl overflow-hidden py-1 max-h-60 overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-200 dark:[&::-webkit-scrollbar-thumb]:bg-gray-700 [&::-webkit-scrollbar-thumb]:rounded-full"
                             style="display: none;">
                            <button type="button" 
                                    @click="document.getElementById('admin-category-filter').value = ''; document.getElementById('admin-category-filter').form.submit()"
                                    class="w-full text-left px-3 py-2 text-xs font-semibold hover:bg-gray-50 dark:hover:bg-white/5 transition-colors {{ request('category') == '' ? 'text-black dark:text-white bg-gray-50 dark:bg-white/5' : 'text-gray-500 dark:text-gray-400' }}">
                                All Categories
                            </button>
                            @foreach($categories as $category)
                                <button type="button" 
                                        @click="document.getElementById('admin-category-filter').value = '{{ $category->id }}'; document.getElementById('admin-category-filter').form.submit()"
                                        class="w-full text-left px-3 py-2 text-xs font-semibold hover:bg-gray-50 dark:hover:bg-white/5 transition-colors {{ request('category') == $category->id ? 'text-black dark:text-white bg-gray-50 dark:bg-white/5' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ ucwords($category->name) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.events.index', ['search' => request('search'), 'category' => request('category')]) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ !request('status') ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">All</a>
                <a href="{{ route('admin.events.index', ['status' => 'active', 'search' => request('search'), 'category' => request('category')]) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ request('status') === 'active' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Active</a>
                <a href="{{ route('admin.events.index', ['status' => 'upcoming', 'search' => request('search'), 'category' => request('category')]) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ request('status') === 'upcoming' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Upcoming</a>
                <a href="{{ route('admin.events.index', ['status' => 'past', 'search' => request('search'), 'category' => request('category')]) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ request('status') === 'past' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Past</a>
                <a href="{{ route('admin.events.index', ['status' => 'deleted', 'search' => request('search'), 'category' => request('category')]) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ request('status') === 'deleted' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Deleted</a>
            </div>
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
