<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('events.show', $event) }}" class="text-gray-400 dark:text-gray-500 hover:text-black dark:hover:text-white transition-colors flex items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight transition-colors">Edit Event</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-8 sm:p-12 shadow-sm border border-gray-100 dark:border-white/10 transition-colors">

                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight transition-colors">Event Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 transition-colors">Update the fundamental details of your occurrence.</p>
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

                <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Basic Info Group -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Event Title <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $event->title) }}" required
                                class="form-input-vercel px-4 py-3" placeholder="e.g. Developer Conference 2026">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Description <span class="text-red-500 dark:text-red-400">*</span></label>
                            <textarea name="description" rows="5" required
                                class="form-input-vercel px-4 py-3 placeholder-gray-400 dark:placeholder-gray-600" placeholder="Provide a detailed overview of what to expect...">{{ old('description', $event->description) }}</textarea>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-white/10 transition-colors">

                    <!-- Logistics Group -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Date <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="date" name="date" value="{{ old('date', $event->date->format('Y-m-d')) }}" required
                                class="form-input-vercel px-4 py-3 [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Time <span class="text-red-500 dark:text-red-400">*</span></label>
                            <input type="time" name="time" value="{{ old('time', $event->time) }}" required
                                class="form-input-vercel px-4 py-3 [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                    </div>

                    <div x-data="locationAutocomplete()" @click.away="open = false" x-init="initMap()">
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Location <span class="text-red-500 dark:text-red-400">*</span></label>
                        
                        <!-- Map Container with Integrated Search Bar -->
                        <div class="relative w-full h-[350px] rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden shadow-sm z-0">
                            <!-- Map div -->
                            <div id="location-map" class="w-full h-full relative z-0"></div>
                            
                            <!-- Floating Search Bar Overlay -->
                            <div class="absolute top-4 left-1/2 -translate-x-1/2 w-11/12 max-w-lg z-[1000]">
                                <div class="relative shadow-xl rounded-lg backdrop-blur-md bg-white/90 dark:bg-black/80">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 dark:text-gray-400">
                                        <svg class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" name="location" required
                                        x-model="query"
                                        @input.debounce.300ms="fetchLocations"
                                        @focus="open = results.length > 0"
                                        class="w-full pl-11 pr-4 py-3.5 bg-transparent border-0 ring-1 ring-black/5 dark:ring-white/10 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm" placeholder="Search venue name or pick on map...">
                                </div>
                                    
                                <!-- Autocomplete Dropdown -->
                                <div x-show="open && results.length > 0" x-cloak
                                     class="absolute w-full mt-2 bg-white dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl shadow-xl max-h-64 overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                                    <template x-for="(result, index) in results" :key="index">
                                        <button type="button" @click="selectLocation(result)"
                                                class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-white/5 border-b border-gray-100 dark:border-white/5 last:border-0 transition-colors flex items-start gap-3 focus:outline-none focus:bg-gray-50 dark:focus:bg-white/5">
                                            <svg class="w-4 h-4 mt-0.5 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="result.name"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="result.details"></p>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Leaflet Resources -->
                            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
                            <!-- Fix for Leaflet z-index overlapping dropdowns -->
                            <style> .leaflet-pane { z-index: 10 !important; } .leaflet-top, .leaflet-bottom { z-index: 10 !important; } </style>
                            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('alpine:init', () => {
                            Alpine.data('locationAutocomplete', () => ({
                                query: '{{ old('location', $event->location) }}',
                                results: [],
                                open: false,
                                map: null,
                                marker: null,
                                
                                initMap() {
                                    const checkL = setInterval(() => {
                                        if (window.L) {
                                            clearInterval(checkL);
                                            this.setupMap();
                                        }
                                    }, 100);
                                },
                                
                                setupMap() {
                                    let defaultLat = 12.9141; // Mangalore
                                    let defaultLng = 74.8560;
                                    let defaultZoom = 2;
                                    
                                    this.map = L.map('location-map').setView([defaultLat, defaultLng], defaultZoom);
                                    
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '&copy; OpenStreetMap contributors'
                                    }).addTo(this.map);
                                    
                                    this.map.on('click', (e) => {
                                        this.setMarker(e.latlng.lat, e.latlng.lng);
                                        this.reverseGeocode(e.latlng.lat, e.latlng.lng);
                                    });
                                    
                                    if(this.query) {
                                        this.geocode(this.query);
                                    } else {
                                        if(navigator.geolocation) {
                                            navigator.geolocation.getCurrentPosition(pos => {
                                                if(!this.query) {
                                                    this.map.setView([pos.coords.latitude, pos.coords.longitude], 10);
                                                }
                                            }, () => {});
                                        }
                                    }
                                },
                                
                                setMarker(lat, lng) {
                                    if (this.marker) {
                                        this.marker.setLatLng([lat, lng]);
                                    } else {
                                        this.marker = L.marker([lat, lng]).addTo(this.map);
                                    }
                                    this.map.setView([lat, lng], 15);
                                },
                                
                                async reverseGeocode(lat, lng) {
                                    try {
                                        const response = await fetch(`https://photon.komoot.io/reverse?lon=${lng}&lat=${lat}`);
                                        const data = await response.json();
                                        if (data.features && data.features.length > 0) {
                                            const props = data.features[0].properties;
                                            let name = props.name || props.street || props.city || 'Unknown Location';
                                            let details = [];
                                            if (name !== props.city && props.city) details.push(props.city);
                                            if (name !== props.street && props.street) details.push(props.street);
                                            if (props.state) details.push(props.state);
                                            if (props.country) details.push(props.country);
                                            
                                            this.query = details.length > 0 ? `${name}, ${details.join(', ')}` : name;
                                        }
                                    } catch(e) {}
                                },
                                
                                async geocode(searchQuery) {
                                    try {
                                        const response = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(searchQuery)}&limit=1`);
                                        const data = await response.json();
                                        if (data.features && data.features.length > 0) {
                                            const coords = data.features[0].geometry.coordinates;
                                            this.setMarker(coords[1], coords[0]);
                                        }
                                    } catch(e) {}
                                },
                                
                                async fetchLocations() {
                                    if (this.query.length < 3) {
                                        this.results = [];
                                        this.open = false;
                                        return;
                                    }
                                    
                                    this.geocode(this.query);
                                    
                                    try {
                                        const response = await fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(this.query)}&limit=5`);
                                        const data = await response.json();
                                        
                                        this.results = data.features.map(f => {
                                            const props = f.properties;
                                            let details = [];
                                            if (props.street) details.push(props.street);
                                            if (props.city) details.push(props.city);
                                            if (props.state) details.push(props.state);
                                            if (props.country) details.push(props.country);
                                            
                                            let name = props.name || props.street || props.city || 'Unknown Location';
                                            
                                            if (name === props.city) details = details.filter(d => d !== props.city);
                                            else if (name === props.street) details = details.filter(d => d !== props.street);
                                            
                                            return {
                                                name: name,
                                                details: details.join(', '),
                                                fullName: details.length > 0 ? `${name}, ${details.join(', ')}` : name,
                                                coords: f.geometry.coordinates
                                            };
                                        });
                                        this.open = this.results.length > 0;
                                    } catch (error) {
                                        this.results = [];
                                    }
                                },
                                
                                selectLocation(result) {
                                    this.query = result.fullName;
                                    this.open = false;
                                    if (result.coords) {
                                        this.setMarker(result.coords[1], result.coords[0]);
                                    }
                                }
                            }));
                        });
                    </script>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Category <span class="text-red-500 dark:text-red-400">*</span></label>
                            <div x-data="{ 
                                open: false, 
                                value: '{{ old('category_id', $event->category_id) }}', 
                                label: 'Select a category',
                                options: [
                                    @foreach($categories as $category)
                                    { id: '{{ $category->id }}', name: '{{ addslashes($category->name) }}' },
                                    @endforeach
                                ],
                                init() {
                                    if (this.value) {
                                        let selected = this.options.find(o => o.id == this.value);
                                        if (selected) this.label = selected.name;
                                    }
                                },
                                selectOption(id, name) {
                                    this.value = id;
                                    this.label = name;
                                    this.open = false;
                                }
                            }" @click.away="open = false" class="relative">
                                
                                <input type="hidden" name="category_id" x-model="value">
                                
                                <button type="button" @click="open = !open" 
                                        class="form-input-vercel px-4 py-3 w-full text-left flex justify-between items-center transition-all">
                                    <span x-text="label" :class="{ 'text-gray-500 dark:text-gray-400': !value, 'text-gray-900 dark:text-white': value }"></span>
                                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            
                                <div x-show="open" x-cloak
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute z-50 w-full mt-2 bg-white/90 dark:bg-[#111]/95 backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-xl shadow-xl overflow-hidden">
                                    
                                    <div class="max-h-60 overflow-y-auto py-2 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                                        <template x-for="option in options" :key="option.id">
                                            <button type="button" @click="selectOption(option.id, option.name)"
                                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-white/10 transition-colors flex items-center justify-between"
                                                    :class="{ 'bg-gray-50 dark:bg-white/5': value == option.id }">
                                                <span x-text="option.name" :class="{ 'font-semibold text-indigo-600 dark:text-indigo-400': value == option.id, 'text-gray-900 dark:text-gray-100': value != option.id }"></span>
                                                <svg x-show="value == option.id" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-white/10 transition-colors my-8">

                    <!-- Ticket Packages Group -->
                    <div x-data="ticketPackages()">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 transition-colors">Ticket Packages <span class="text-red-500 dark:text-red-400">*</span></h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage pricing tiers. Warning: Tiers with existing bookings cannot be deleted safely.</p>
                            </div>
                            <button type="button" @click="addPackage()" class="btn-vercel-secondary text-xs px-4 py-2 flex items-center gap-1.5 border border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-black/50 shadow-sm transition-colors hover:bg-gray-50 dark:hover:bg-gray-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Tier
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <template x-for="(pkg, index) in packages" :key="index">
                                <div class="p-6 bg-white/30 dark:bg-white/5 backdrop-blur-sm rounded-xl border border-gray-100 dark:border-white/10 relative shadow-sm transition-colors group">
                                    <button type="button" @click="removePackage(index)" x-show="packages.length > 1" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors bg-white dark:bg-neutral-800 rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 focus:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                    
                                    <template x-if="pkg.id">
                                        <input type="hidden" :name="`tickets[${index}][id]`" :value="pkg.id">
                                    </template>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-5 pr-8">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-1.5">Package Name</label>
                                            <input type="text" :name="`tickets[${index}][name]`" x-model="pkg.name" required class="form-input-vercel px-4 py-2.5 text-sm" placeholder="e.g. VIP">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-1.5">Price ($)</label>
                                            <input type="number" step="0.01" :name="`tickets[${index}][price]`" x-model="pkg.price" required class="form-input-vercel px-4 py-2.5 text-sm" placeholder="0.00 for Free">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-1.5">Capacity</label>
                                            <input type="number" :name="`tickets[${index}][capacity]`" x-model="pkg.capacity" required class="form-input-vercel px-4 py-2.5 text-sm" placeholder="e.g. 50">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-1.5">Description (Optional)</label>
                                        <input type="text" :name="`tickets[${index}][description]`" x-model="pkg.description" class="form-input-vercel px-4 py-2.5 text-sm" placeholder="What does this ticket include?">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    @php
                        $defaultPkg = [['name' => 'General Admission', 'price' => '0', 'capacity' => '50', 'description' => '']];
                        $ticketPackages = $event->ticketTypes->isEmpty() ? $defaultPkg : $event->ticketTypes;
                    @endphp
                    <script>
                        document.addEventListener('alpine:init', () => {
                            Alpine.data('ticketPackages', () => ({
                                packages: {!! json_encode($ticketPackages) !!},
                                addPackage() {
                                    this.packages.push({ name: '', price: '', capacity: '', description: '' });
                                },
                                removePackage(index) {
                                    this.packages.splice(index, 1);
                                }
                            }))
                        })
                    </script>


                    <div>
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 transition-colors">Tags & Labels</label>
                        <div class="flex flex-wrap gap-3 p-5 bg-white/30 dark:bg-white/5 backdrop-blur-sm rounded-xl border border-gray-100 dark:border-white/10 transition-colors">
                            @foreach($tags as $tag)
                                <label class="flex items-center gap-2.5 text-sm cursor-pointer hover:bg-white dark:hover:bg-[#222] px-3 py-1.5 rounded-md border border-transparent hover:border-gray-200 dark:hover:border-white/10 shadow-none hover:shadow-sm transition-all duration-200">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                        {{ in_array($tag->id, old('tags', $selectedTags)) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-gray-300 dark:border-white/20 bg-white/50 dark:bg-white/10 text-black shadow-sm focus:ring-black dark:focus:ring-white dark:focus:ring-offset-[#111] transition-colors cursor-pointer">
                                    <span class="font-medium text-gray-700 dark:text-gray-300 transition-colors">{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-white/10 transition-colors">

                    <!-- Media Group -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Poster Image (Single) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 transition-colors">Event Poster (Vertical)</label>
                            @if($event->poster_image)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-widest font-bold">Current Poster</p>
                                <div class="relative w-full max-w-xs mx-auto aspect-[4/5] rounded-xl overflow-hidden border border-gray-200 dark:border-white/10 shadow-sm bg-gray-50 dark:bg-white/5 transition-colors mb-6">
                                    <img src="/storage/{{ $event->poster_image }}" class="object-cover w-full h-full">
                                </div>
                            @endif
                            <div x-data="singleImagePreviewer()" class="flex flex-col gap-4 w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-200 dark:border-white/20 border-dashed rounded-lg cursor-pointer bg-white/30 dark:bg-white/5 backdrop-blur-sm hover:bg-white/40 dark:hover:bg-white/10 transition-colors relative overflow-hidden">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-3 text-indigo-400 dark:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="mb-1 text-sm text-gray-500 dark:text-gray-400 font-semibold"><span x-text="fileName ? 'Click to change' : 'Replace Poster'"></span></p>
                                    </div>
                                    <input type="file" name="poster_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="fileChosen">
                                </label>
                                <div class="relative group aspect-[4/5] w-full max-w-xs mx-auto rounded-lg overflow-hidden border border-gray-200 dark:border-white/10 shadow-sm bg-gray-50 dark:bg-white/5 transition-colors" x-show="imageUrl" x-cloak>
                                    <img :src="imageUrl" class="object-cover w-full h-full">
                                    <div class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-center py-2 text-xs font-bold uppercase tracking-widest backdrop-blur-md">New Poster Preview</div>
                                </div>
                            </div>
                        </div>

                        <!-- Banner Images (Multiple) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 transition-colors">Banner Carousel (Landscape)</label>
                            
                            @if(is_array($event->images) && count($event->images) > 0)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-widest font-bold">Current Banners</p>
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    @foreach($event->images as $imgPath)
                                        <div class="relative aspect-[16/9] rounded-lg overflow-hidden border border-gray-200 dark:border-white/10 shadow-sm bg-gray-50 dark:bg-white/5 transition-colors">
                                            <img src="/storage/{{ $imgPath }}" class="object-cover w-full h-full">
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div x-data="multiImagePreviewer()" class="flex flex-col gap-4 w-full">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-widest font-bold">Add More Banners</p>
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-200 dark:border-white/20 border-dashed rounded-lg cursor-pointer bg-white/30 dark:bg-white/5 backdrop-blur-sm hover:bg-white/40 dark:hover:bg-white/10 transition-colors relative overflow-hidden">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 pointer-events-none">
                                        <svg class="w-8 h-8 mb-3 text-purple-400 dark:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <p class="mb-1 text-sm text-gray-500 dark:text-gray-400 font-semibold">Upload Additional Banners</p>
                                    </div>
                                    <input type="file" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="fileChosen" x-ref="multiInput">
                                </label>
                                
                                <div class="grid grid-cols-2 gap-4" x-show="imageUrls.length > 0" x-cloak>
                                    <template x-for="(url, index) in imageUrls" :key="index">
                                        <div class="relative group aspect-[16/9] rounded-lg overflow-hidden border border-gray-200 dark:border-white/10 shadow-sm bg-gray-50 dark:bg-white/5 transition-colors">
                                            <img :src="url" class="object-cover w-full h-full">
                                            <button type="button" @click.stop="removeImage(index)" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white p-1 rounded-full shadow-md transition-colors opacity-0 group-hover:opacity-100 z-10 focus:outline-none focus:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <div class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-center py-2 text-xs font-bold uppercase tracking-widest backdrop-blur-md pointer-events-none">Preview</div>
                                        </div>
                                    </template>
                                </div>
                                <!-- Hidden actual input to hold all accumulated files -->
                                <input type="file" name="images[]" multiple class="hidden" x-ref="hiddenUploadInput">
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        document.addEventListener('alpine:init', () => {
                            Alpine.data('singleImagePreviewer', () => ({
                                imageUrl: '',
                                fileName: '',
                                fileChosen(event) {
                                    const file = event.target.files[0];
                                    if(file) {
                                        this.imageUrl = URL.createObjectURL(file);
                                        this.fileName = file.name;
                                    }
                                }
                            }));
                            Alpine.data('multiImagePreviewer', () => ({
                                imageUrls: [],
                                dt: new DataTransfer(),
                                fileChosen(event) {
                                    const newFiles = event.target.files;
                                    for (let i = 0; i < newFiles.length; i++) {
                                        this.dt.items.add(newFiles[i]);
                                        this.imageUrls.push(URL.createObjectURL(newFiles[i]));
                                    }
                                    this.$refs.hiddenUploadInput.files = this.dt.files;
                                },
                                removeImage(index) {
                                    const newDt = new DataTransfer();
                                    for (let i = 0; i < this.dt.files.length; i++) {
                                        if (i !== index) newDt.items.add(this.dt.files[i]);
                                    }
                                    this.dt = newDt;
                                    this.imageUrls.splice(index, 1);
                                    this.$refs.hiddenUploadInput.files = this.dt.files;
                                }
                            }));
                        });
                    </script>

                    <!-- Feature Toggle -->
                    <div>
                        <hr class="border-gray-100 dark:border-white/10 transition-colors my-8">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $event->is_featured) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-indigo-600 dark:text-indigo-500 bg-white/50 dark:bg-black/50 border-gray-300 dark:border-white/20 rounded-md focus:ring-black dark:focus:ring-white transition-colors cursor-pointer">
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 transition-colors">Feature this event on the homepage carousel</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row items-center gap-4 pt-6 mt-8 border-t border-gray-100 dark:border-white/10 transition-colors">
                        <button type="submit" class="btn-vercel w-full sm:w-auto px-8 py-3 text-base">
                            Save Changes
                        </button>
                        <a href="{{ route('events.show', $event) }}" class="btn-vercel-secondary w-full sm:w-auto px-8 py-3 text-base text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
