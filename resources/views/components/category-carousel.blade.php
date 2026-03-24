@props(['categories'])

@php
// Map of category slugs to placeholder image URLs
$categoryImages = [
    'music' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&q=80&w=200&h=200',
    'art' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&q=80&w=200&h=200',
    'business' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&q=80&w=200&h=200',
    'competition' => 'https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?auto=format&fit=crop&q=80&w=200&h=200',
    'culture' => 'https://images.unsplash.com/photo-1544928147-79a2dbc1f389?auto=format&fit=crop&q=80&w=200&h=200',
    'education' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=200&h=200',
    'lifestyle' => 'https://images.unsplash.com/photo-1511988617509-a57c8a288659?auto=format&fit=crop&q=80&w=200&h=200',
    'sports' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&q=80&w=200&h=200',
];
@endphp

<div class="relative w-full overflow-hidden py-6 pb-2 group mask-edges">
    <!-- Extremely wide container that holds 2 identical sets of items to scroll seamlessly -->
    <div class="flex w-max flex-shrink-0 animate-scrollX hover:[animation-play-state:paused] pt-2 pb-4">
        
        <!-- Render multiple sets for seamless infinite scrolling even on ultra-wide monitors -->
        @for($i = 0; $i < 8; $i++)
            <div class="flex flex-nowrap" {!! $i > 0 ? 'aria-hidden="true"' : '' !!}>
                @foreach($categories as $category)
                    @php
                        $slug = strtolower($category->slug);
                        if ($category->image_path) {
                            $image = \Illuminate\Support\Facades\Storage::url($category->image_path);
                        } else {
                            $image = $categoryImages[$slug] ?? 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80&w=200&h=200';
                        }
                        $isActive = request('category') == $category->id || request('category_slug') == $slug;
                    @endphp
                    
                    <div class="pr-6 sm:pr-8">
                        <a href="{{ route('events.index', ['category' => $category->id]) }}" 
                           class="flex flex-col items-center gap-3 group min-w-max shrink-0 outline-none" {!! $i > 0 ? 'tabindex="-1"' : '' !!}>
                            
                            <div class="relative w-[88px] h-[88px] sm:w-[104px] sm:h-[104px] rounded-full p-[3px] transition-transform duration-300 group-hover:scale-105 {{ $isActive ? 'bg-gradient-to-tr from-rose-500 to-red-600 shadow-lg shadow-red-500/30' : 'bg-gradient-to-tr from-gray-200 to-gray-300 dark:from-white/10 dark:to-white/20 hover:from-rose-500 hover:to-red-600' }} group-focus-visible:ring-4 group-focus-visible:ring-red-500/50">
                                <div class="w-full h-full rounded-full border-[3px] border-white dark:border-[#0a0a0a] overflow-hidden bg-gray-100 dark:bg-gray-800">
                                    <img src="{{ $image }}" 
                                         alt="{{ ucwords($category->name) }}" 
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 {{ $isActive ? '' : 'filter contrast-[0.95]' }}"
                                         loading="lazy">
                                </div>
                            </div>

                            <span class="text-xs sm:text-[13px] font-semibold tracking-wide transition-colors {{ $isActive ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-600 dark:text-gray-300 group-hover:text-black dark:group-hover:text-white' }}">
                                {{ ucwords($category->name) }}
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        @endfor

    </div>
</div>

<style>
@keyframes scrollX {
    0% { transform: translateX(0); }
    100% { transform: translateX(-12.5%); }
}
.animate-scrollX {
    animation: scrollX 35s linear infinite;
}
.mask-edges {
    mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
}
</style>
