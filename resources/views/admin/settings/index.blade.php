<x-admin.layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Platform Settings') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-black/40 backdrop-blur-xl border border-gray-200 dark:border-white/10 overflow-hidden shadow-sm rounded-2xl">
            <div class="p-8 text-gray-900 dark:text-gray-100">
                <h3 class="text-2xl font-black mb-6 tracking-tight">Monetization Fees</h3>
                
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-8 mb-8">
                        @foreach($settings as $setting)
                            <div class="bg-gray-50 dark:bg-white/5 p-6 rounded-xl border border-gray-100 dark:border-white/10">
                                <label class="block text-base font-bold text-gray-900 dark:text-white mb-1">
                                    {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                    <span class="block text-sm font-normal text-gray-500 mt-1">{{ $setting->description }}</span>
                                </label>
                                <div class="mt-3 relative rounded-md shadow-sm">
                                    @if(str_contains($setting->key, 'percent'))
                                        <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="block w-full pr-8 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base font-semibold py-3 transition-colors">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">%</span>
                                        </div>
                                    @else
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">₹</span>
                                        </div>
                                        <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="pl-8 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base font-semibold py-3 transition-colors">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <button type="submit" class="btn-vercel w-full px-6 py-4 text-sm tracking-wide font-bold">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</x-admin.layout>
