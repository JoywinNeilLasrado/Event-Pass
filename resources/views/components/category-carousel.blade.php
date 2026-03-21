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

<div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-2">
    <div class="flex overflow-x-auto gap-6 sm:gap-8 pb-4 scrollbar-hide snap-x pt-2">
        @foreach($categories as $category)
            @php
                $slug = strtolower($category->slug);
                // Priority: Uploaded Image -> Map Fallback -> Default Placeholder
                if ($category->image_path) {
                    $image = \Illuminate\Support\Facades\Storage::url($category->image_path);
                } else {
                    $image = $categoryImages[$slug] ?? 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80&w=200&h=200';
                }
                $isActive = request('category') == $category->id || request('category_slug') == $slug;
            @endphp
            
            <a href="{{ route('events.index', ['category' => $category->id]) }}" 
               class="flex flex-col items-center gap-3 group min-w-max shrink-0 snap-start outline-none">
                
                <div class="relative w-[88px] h-[88px] sm:w-[104px] sm:h-[104px] rounded-full p-[3px] transition-transform duration-300 group-hover:scale-105 group-focus-visible:ring-4 group-focus-visible:ring-red-500/50 {{ $isActive ? 'bg-gradient-to-tr from-rose-500 to-red-600 shadow-lg shadow-red-500/30' : 'bg-gradient-to-tr from-gray-200 to-gray-300 dark:from-white/10 dark:to-white/20 hover:from-rose-500 hover:to-red-600' }}">
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
        @endforeach
    </div>
</div>

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>
