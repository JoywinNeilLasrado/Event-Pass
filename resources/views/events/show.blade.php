<x-app-layout>
    @push('meta')
        <meta property="og:title" content="{{ $event->title }}" />
        <meta property="og:description" content="{{ Str::limit($event->description, 150) }}" />
        <meta property="og:url" content="{{ route('events.show', $event) }}" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $event->title }}" />
        <meta name="twitter:description" content="{{ Str::limit($event->description, 150) }}" />
        @if($event->poster_image)
            <meta property="og:image" content="{{ asset(Storage::url($event->poster_image)) }}" />
            <meta name="twitter:image" content="{{ asset(Storage::url($event->poster_image)) }}" />
        @endif
    @endpush

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <a href="{{ route('events.index') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors flex items-center mb-1 sm:mb-0 mr-2">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Events
            </a>
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight line-clamp-1 truncate transition-colors">{{ $event->title }}</h2>
        </div>
    </x-slot>

    <!-- BookMyShow-Style Layout -->
    <div class="py-8 sm:py-12 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg text-sm font-medium">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg text-sm font-medium">{{ session('error') }}</div>
            @endif

            <!-- Event Title -->
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-6 transition-colors">{{ $event->title }}</h1>

            <!-- Two-Column: Banner + Sidebar -->
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">

                <!-- Left: Banner Carousel -->
                <div class="flex-1 min-w-0"
                     x-data="{
                         active: 0,
                         banners: {{ json_encode(collect(is_array($event->images) && count($event->images) > 0 ? $event->images : ($event->poster_image ? [$event->poster_image] : []))->map(fn($img) => Storage::url($img))->filter()->values()->all()) }},
                         timer: null,
                         startTimer() {
                             if(this.banners.length > 1) {
                                 this.timer = setInterval(() => {
                                     this.active = this.active === this.banners.length - 1 ? 0 : this.active + 1;
                                 }, 2000);
                             }
                         },
                         stopTimer() { clearInterval(this.timer); },
                         goTo(i) { this.stopTimer(); this.active = i; this.startTimer(); },
                         prev() { this.goTo(this.active === 0 ? this.banners.length - 1 : this.active - 1); },
                         next() { this.goTo(this.active === this.banners.length - 1 ? 0 : this.active + 1); }
                     }"
                     x-init="startTimer()"
                     @mouseenter="stopTimer()"
                     @mouseleave="startTimer()">

                    <!-- Banner Image Area -->
                    <div class="relative w-full aspect-[16/9] rounded-2xl overflow-hidden bg-gray-100 dark:bg-[#111] border border-gray-200 dark:border-white/10 shadow-sm group transition-colors">
                        <template x-if="banners.length > 0">
                            <div class="w-full h-full relative">
                                <template x-for="(banner, index) in banners" :key="index">
                                    <div class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                                         x-show="active === index"
                                         x-transition:enter="transition-opacity duration-700"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition-opacity duration-700"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0">
                                        <img :src="banner" class="w-full h-full object-cover" :alt="'Banner ' + (index + 1)">
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="banners.length === 0">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center">
                                <span class="text-6xl opacity-30">🎟️</span>
                            </div>
                        </template>

                        <!-- Left/Right Arrows -->
                        <button x-show="banners.length > 1" @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 hover:bg-black/70 backdrop-blur-sm text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 z-10 focus:outline-none" x-cloak>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button x-show="banners.length > 1" @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 hover:bg-black/70 backdrop-blur-sm text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 z-10 focus:outline-none" x-cloak>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>

                        <!-- Dots -->
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10" x-show="banners.length > 1" x-cloak>
                            <template x-for="(banner, index) in banners" :key="index">
                                <button @click="goTo(index)"
                                        class="w-2.5 h-2.5 rounded-full transition-all duration-300 shadow-sm"
                                        :class="active === index ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/80'"></button>
                            </template>
                        </div>
                    </div>

                    <!-- Tags Below Banner -->
                    <div class="flex flex-wrap items-center gap-2 mt-4">
                        <span class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-md border border-indigo-200 dark:border-indigo-800/50 transition-colors">
                            {{ $event->category->name }}
                        </span>
                        @foreach($event->tags as $tag)
                            <span class="bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-md border border-gray-200 dark:border-white/10 transition-colors">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Center: Event Poster -->
                @if($event->poster_image)
                <div class="hidden lg:block w-[220px] xl:w-[260px] flex-shrink-0">
                    <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#111] relative group transition-colors">
                        <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->title }} poster" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    </div>
                </div>
                @endif

                <!-- Right: Details Sidebar -->
                <div class="w-full lg:w-[340px] xl:w-[380px] flex-shrink-0">
                    <div class="card p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-white/10 lg:sticky lg:top-24 transition-colors space-y-0">

                        <!-- Date & Time -->
                        <div class="flex items-start gap-3 pb-4 border-b border-gray-100 dark:border-white/10">
                            <div class="w-9 h-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white transition-colors">{{ $event->date->format('D, j M Y') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ date('g:i A', strtotime($event->time)) }}</p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-start gap-3 py-4 border-b border-gray-100 dark:border-white/10">
                            <div class="w-9 h-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate transition-colors">{{ $event->location }}</p>
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($event->location) }}" target="_blank" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium transition-colors">Get Directions &rarr;</a>
                            </div>
                        </div>

                        <!-- Organizer -->
                        <div class="flex items-center gap-3 py-4 border-b border-gray-100 dark:border-white/10">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ substr($event->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">Organized by</p>
                                <a href="{{ route('organizers.show', $event->user) }}" class="text-sm font-bold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ $event->user->name }}</a>
                            </div>
                        </div>

                        <!-- Price & Availability -->
                        <div class="py-4 border-b border-gray-100 dark:border-white/10">
                            @php
                                $minPrice = $event->ticketTypes->min('price');
                                $remaining = $event->remaining;
                            @endphp
                            <div class="flex items-baseline justify-between">
                                <p class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight transition-colors">
                                    {{ $minPrice > 0 ? '₹' . number_format($minPrice, 0) . ' onwards' : 'FREE' }}
                                </p>
                                @if($remaining > 0)
                                    <span class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wider">Available</span>
                                @else
                                    <span class="text-xs font-bold text-red-500 dark:text-red-400 uppercase tracking-wider">Sold Out</span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Area -->
                        <div class="pt-4">
                            <div class="flex flex-col gap-3">
                                @auth
                                    @if($event->user_id === auth()->id())
                                        @if(!$event->is_published && $event->payment_status === 'pending')
                                            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-2">
                                                <p class="text-sm text-yellow-800 dark:text-yellow-300 font-medium mb-3">This event is currently unpublished pending the publishing fee payment. It is only visible to you.</p>
                                                <a href="{{ route('events.retry_publish_payment', $event) }}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold uppercase tracking-widest rounded-md transition-all shadow hover:shadow-md">
                                                    Pay Now to Publish &rarr;
                                                </a>
                                            </div>
                                        @endif
                                        <div class="flex gap-3">
                                            <a href="{{ route('events.edit', $event) }}" class="btn-vercel-secondary flex-1 text-center">Edit Event</a>
                                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="flex" onsubmit="return confirm('Soft-delete this event? It can be restored later.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center bg-white/40 dark:bg-white/5 backdrop-blur-md text-red-600 dark:text-red-500 border border-red-200 dark:border-red-900/50 hover:border-red-300 dark:hover:border-red-800 hover:bg-red-50 dark:hover:bg-white/10 transition-colors duration-200 px-4 rounded-lg font-medium shadow-sm active:translate-y-px focus:outline-none">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        @if($hasBooked ?? false)
                                            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg p-4 text-center transition-colors mb-2">
                                                <p class="font-bold mb-1 flex items-center justify-center">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    You're going!
                                                </p>
                                                <a href="{{ route('bookings.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">View/Manage my tickets &rarr;</a>
                                            </div>
                                        @endif

                                        @if($event->remaining > 0)
                                            <a href="#tickets" class="btn-vercel w-full text-center text-base py-3">Book Now</a>
                                        @else
                                            <div class="bg-white/40 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 rounded-lg p-4 text-center font-semibold transition-colors">
                                                Sold Out
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-3 transition-colors">Join the experience.</p>
                                        <a href="{{ route('login') }}" class="btn-vercel w-full">Sign in to Reserve</a>
                                    </div>
                                @endauth
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- About The Event -->
            <div class="mt-12 grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-16">
                <!-- Left: Description & Map -->
                <div class="lg:col-span-2 space-y-12">
                    <div class="card p-8 sm:p-10 shadow-sm border border-gray-100 dark:border-white/10 transition-colors">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight mb-6">About The Event</h2>
                        <div class="prose prose-lg text-gray-600 dark:text-gray-400 prose-headings:text-black dark:prose-headings:text-white prose-a:text-indigo-600 dark:prose-a:text-indigo-400 font-light leading-relaxed max-w-none transition-colors">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>

                    <div class="card p-8 sm:p-10 shadow-sm border border-gray-100 dark:border-white/10 transition-colors">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight mb-6">Location Map</h2>
                        <div class="w-full h-80 sm:h-96 rounded-2xl overflow-hidden border border-gray-200 dark:border-white/10 shadow-sm relative group bg-gray-50 dark:bg-[#111]">
                            <iframe width="100%" height="100%" frameborder="0" style="border:0;" src="https://maps.google.com/maps?q={{ urlencode($event->location) }}&t=&z=14&ie=UTF8&iwloc=&output=embed" allowfullscreen></iframe>
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($event->location) }}" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-sm">
                                <span class="bg-white text-black text-sm font-bold uppercase tracking-widest px-6 py-3 rounded-full shadow-xl flex items-center gap-2 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                    Get Directions
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right: Tickets Section -->
                <div id="tickets" class="space-y-6 lg:sticky lg:top-24 h-max scroll-mt-24">
                    @auth
                        @if($event->user_id !== auth()->id())
                            @if($event->remaining > 0)
                                <div class="space-y-4" x-data="promoCheckout()">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">Select Ticket Package</h4>

                                    <!-- Promo Code Input -->
                                    <div class="flex items-center justify-between bg-white/40 dark:bg-black/40 backdrop-blur-md border border-gray-200 dark:border-white/10 p-2 pl-4 rounded-lg shadow-sm">
                                        <input type="text" x-model="code" @keydown.enter.prevent="applyPromo()" id="master_promo_code" placeholder="HAVE A PROMO CODE?" class="bg-transparent border-none focus:ring-0 text-sm font-bold tracking-widest uppercase font-mono text-gray-900 dark:text-white w-full placeholder-gray-400 dark:placeholder-gray-500 p-0">
                                        <button type="button" @click="applyPromo()" :disabled="isLoading" class="bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 hover:text-indigo-600 p-2 rounded-md transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                            <svg x-show="isLoading" style="display: none;" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </button>
                                    </div>
                                    <p x-show="message" x-text="message" x-transition :class="isValid ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'" style="display: none;" class="text-xs font-bold mb-4 ml-2"></p>

                                    @foreach($event->ticketTypes as $tier)
                                        <div class="p-5 mt-2 bg-white/40 dark:bg-black/40 backdrop-blur-md border {{ $tier->remaining > 0 ? 'border-gray-200 dark:border-white/10 hover:border-indigo-300 dark:hover:border-indigo-500/50' : 'border-gray-100 dark:border-white/5 opacity-60' }} rounded-xl shadow-sm transition-all group relative overflow-hidden">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <h5 class="font-bold text-gray-900 dark:text-white tracking-tight text-lg">{{ $tier->name }}</h5>
                                                    @if($tier->description)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 leading-relaxed">{{ $tier->description }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right flex-shrink-0 ml-4">
                                                    <div class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight relative">
                                                        {{-- Default price - always visible, no Alpine dependency --}}
                                                        <span :class="{ 'line-through text-sm text-gray-400 font-medium': isValid && {{ $tier->price }} > 0 }">{{ $tier->price > 0 ? '$' . number_format($tier->price, 2) : 'FREE' }}</span>
                                                        {{-- Discounted price - only shows after valid promo --}}
                                                        @if($tier->price > 0)
                                                        <div x-show="isValid" x-cloak class="text-green-500 dark:text-green-400">
                                                            $<span x-text="calculatePrice({{ $tier->price }})"></span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-5 border-t border-gray-100 dark:border-white/5 pt-4">
                                                @if($tier->remaining > 0)
                                                    <div class="mb-4">
                                                        <span class="inline-block text-[10px] font-bold text-green-600 dark:text-green-400 uppercase tracking-widest bg-green-50 dark:bg-green-900/30 px-3 py-1.5 rounded-md shadow-sm whitespace-nowrap">
                                                            {{ $tier->remaining }} Tickets Left
                                                        </span>
                                                    </div>
                                                    <form action="{{ route('bookings.store', $event) }}" method="POST" class="flex flex-row items-center gap-3 w-full">
                                                        @csrf
                                                        <input type="hidden" name="ticket_type_id" value="{{ $tier->id }}">
                                                        <input type="hidden" name="promo_code" :value="isValid ? code : ''">

                                                        <div class="relative flex-1">
                                                            <select name="quantity" class="w-full shadow-sm appearance-none border border-gray-200 dark:border-white/10 dark:bg-black/40 rounded-lg py-2.5 pl-4 pr-8 text-sm text-gray-900 dark:text-white font-medium focus:ring-indigo-500 transition-colors cursor-pointer">
                                                                @for($i = 1; $i <= min(10, $tier->remaining); $i++)
                                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                            </div>
                                                        </div>

                                                        <button type="submit" class="flex-1 btn-vercel text-sm px-6 py-2.5 shadow-md hover:-translate-y-0.5 transition-transform shrink-0">Select</button>
                                                    </form>
                                                @else
                                                    @php
                                                        $isOnWaitlist = in_array($tier->id, $userWaitlistTiers ?? []);
                                                    @endphp
                                                    <div class="flex items-center justify-between w-full">
                                                        @if($isOnWaitlist)
                                                            <span class="text-[10px] font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-widest bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1 rounded-md shadow-sm">
                                                                On Waitlist
                                                            </span>
                                                            <button disabled type="button" class="btn-vercel-secondary text-sm px-6 py-2 opacity-50 cursor-not-allowed">Pending</button>
                                                        @else
                                                            <span class="text-[10px] font-bold text-red-500 dark:text-red-400 uppercase tracking-widest bg-red-50 dark:bg-red-900/30 px-2.5 py-1 rounded-md shadow-sm">
                                                                Sold Out
                                                            </span>
                                                            <form action="{{ route('waitlists.store', $event) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="ticket_type_id" value="{{ $tier->id }}">
                                                                <button type="submit" class="inline-flex items-center justify-center bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 hover:border-black/20 dark:hover:border-white/20 text-gray-900 dark:text-white px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm focus:outline-none">Join Waitlist</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-white/40 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 rounded-lg p-4 text-center font-semibold transition-colors">
                                    Sold Out
                                </div>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('promoCheckout', () => ({
                code: '',
                discountAmount: 0,
                discountType: '',
                message: '',
                isValid: false,
                isLoading: false,

                async applyPromo() {
                    if (!this.code.trim()) {
                        this.message = 'Please enter a code.';
                        this.isValid = false;
                        this.discountAmount = 0;
                        return;
                    }

                    this.isLoading = true;
                    this.message = '';

                    try {
                        const response = await fetch(`{{ route('promo_codes.validate', $event) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ code: this.code })
                        });

                        const data = await response.json();

                        if (response.ok && data.valid) {
                            this.isValid = true;
                            this.discountAmount = parseFloat(data.discount_amount);
                            this.discountType = data.discount_type;
                            this.message = data.message;
                        } else {
                            this.isValid = false;
                            this.discountAmount = 0;
                            this.message = data.message || 'Invalid code.';
                        }
                    } catch (error) {
                        this.isValid = false;
                        this.discountAmount = 0;
                        this.message = 'Error validating code.';
                    } finally {
                        this.isLoading = false;
                    }
                },

                calculatePrice(originalPrice) {
                    if (!this.isValid || originalPrice <= 0) return originalPrice;

                    let newPrice = parseFloat(originalPrice);
                    if (this.discountType === 'percentage') {
                        newPrice = newPrice - (newPrice * (this.discountAmount / 100));
                    } else if (this.discountType === 'fixed') {
                        newPrice = newPrice - this.discountAmount;
                    }

                    return Math.max(0, newPrice).toFixed(2);
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
