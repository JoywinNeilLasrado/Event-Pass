<x-admin.layout>
    <x-slot name="title">Edit Category</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card p-6 sm:p-8 transition-colors">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight transition-colors">Edit Category: {{ $category->name }}</h3>
                <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                    &larr; Back to List
                </a>
            </div>

            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Category Name <span class="text-red-500 dark:text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors">
                    @error('name') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Category Image</label>
                    @if($category->image_path)
                        <div class="mb-4">
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Current Image:</span>
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="w-24 h-24 rounded-full object-cover border border-gray-200 dark:border-white/10 shadow-sm">
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                        class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-gray-200 dark:file:bg-[#333] file:text-gray-700 dark:file:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Leave blank to keep current image.</p>
                    @error('image') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-white/10">
                    <button type="submit" class="btn-vercel w-full py-3 text-sm">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin.layout>
