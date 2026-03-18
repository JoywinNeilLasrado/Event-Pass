<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('events.index') }}" class="text-gray-400 dark:text-gray-500 hover:text-black dark:hover:text-white transition-colors flex items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight transition-colors">Create Event</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-8 sm:p-12 shadow-sm border border-gray-100 dark:border-white/10 transition-colors">

                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight transition-colors">New Experience</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 transition-colors">Configure exactly what your attendees will experience.</p>
                </div>

                @if($errors->any())
                    <div class="mb-8 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-lg p-4 transition-colors">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-semibold text-sm">Please correct the following errors:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 ml-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <!-- Basic Info Group -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Event Title <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors" placeholder="e.g. Developer Conference 2026">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Description <span class="text-red-500 dark:text-red-400">*</span></label>
                            <textarea name="description" rows="5" required
                                class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors placeholder-gray-400 dark:placeholder-gray-600" placeholder="Provide a detailed overview of what to expect...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-white/10 transition-colors">

                    <!-- Logistics Group -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Date <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="date" name="date" value="{{ old('date') }}" required
                                class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Time <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="time" name="time" value="{{ old('time') }}" required
                                class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Location <span class="text-red-500 dark:text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <input type="text" name="location" value="{{ old('location') }}" required
                                class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] pl-10 pr-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors placeholder-gray-400 dark:placeholder-gray-600" placeholder="Venue name or address">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Total Capacity <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="number" name="available_tickets" value="{{ old('available_tickets', 50) }}" min="1" required
                                class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Category <span class="text-red-500 dark:text-red-400">*</span></label>
                            <select name="category_id" required class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors cursor-pointer">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 transition-colors">Tags & Labels</label>
                        <div class="flex flex-wrap gap-3 p-5 bg-gray-50 dark:bg-[#1A1A1A] rounded-xl border border-gray-100 dark:border-white/10 transition-colors">
                            @foreach($tags as $tag)
                                <label class="flex items-center gap-2.5 text-sm cursor-pointer hover:bg-white dark:hover:bg-[#222] px-3 py-1.5 rounded-md border border-transparent hover:border-gray-200 dark:hover:border-white/10 shadow-none hover:shadow-sm transition-all duration-200">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                        {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-gray-300 dark:border-white/20 bg-white dark:bg-[#111] text-black shadow-sm focus:ring-black dark:focus:ring-white dark:focus:ring-offset-[#111] transition-colors cursor-pointer">
                                    <span class="font-medium text-gray-700 dark:text-gray-300 transition-colors">{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-white/10 transition-colors">

                    <!-- Media Group -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 transition-colors">Event Poster</label>
                        <div class="flex-grow w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-200 dark:border-white/20 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-[#1A1A1A] hover:bg-gray-100 dark:hover:bg-[#222] transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-3 text-gray-400 dark:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <p class="mb-1 text-sm text-gray-500 dark:text-gray-400 font-semibold transition-colors">Click to upload or drag and drop</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 transition-colors">SVG, PNG, JPG (MAX. 2MB)</p>
                                </div>
                                <input type="file" name="poster_image" accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row items-center gap-4 pt-6 mt-8 border-t border-gray-100 dark:border-white/10 transition-colors">
                        <button type="submit" class="btn-vercel w-full sm:w-auto px-8 py-3 text-base">
                            Publish Event
                        </button>
                        <a href="{{ route('events.index') }}" class="btn-vercel-secondary w-full sm:w-auto px-8 py-3 text-base text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
