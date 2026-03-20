<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-6">
            <a href="{{ route('events.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Events
            </a>
        </div>
        <div class="bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-white/10 rounded-2xl p-8 mb-12 text-center shadow-sm">
            <div class="h-24 w-24 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 mx-auto mb-4 flex items-center justify-center text-white text-3xl font-bold">
                {{ substr($user->name, 0, 1) }}
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $user->name }}</h1>
            <p class="text-gray-500 dark:text-gray-400">Discover upcoming events hosted by {{ $user->name }}.</p>
            
            @if(auth()->check() && auth()->id() === $user->id)
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-100 dark:bg-white/10 border border-transparent rounded-xl font-bold text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-white/20 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Edit Profile
                    </a>
                </div>
            @endif
        </div>

        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Upcoming Events ({{ $events->count() }})</h2>

        @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <a href="{{ route('events.show', $event) }}" class="group block bg-white dark:bg-[#0a0a0a] border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden hover:border-black/20 dark:hover:border-white/20 transition-all hover:shadow-md hover:-translate-y-1">
                        <div class="aspect-video w-full bg-gray-100 dark:bg-white/5 relative overflow-hidden">
                            @if($event->poster_image)
                                <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-600">
                                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 group-hover:text-black dark:group-hover:text-gray-300 transition-colors">{{ $event->title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $event->date->format('M j, Y') }} &bull; {{ date('g:i A', strtotime($event->time)) }}</p>
                            <span class="inline-flex items-center text-xs font-semibold text-gray-900 dark:text-white bg-gray-100 dark:bg-white/10 px-2.5 py-1 rounded-full">
                                {{ $event->location }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 dark:bg-white/5 border border-dashed border-gray-200 dark:border-white/10 rounded-xl p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400 mb-4">No upcoming events right now.</p>
            </div>
        @endif
    </div>
</x-app-layout>
