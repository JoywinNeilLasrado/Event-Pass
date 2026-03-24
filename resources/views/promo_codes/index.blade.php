<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors flex items-center mb-1 sm:mb-0 mr-2">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight line-clamp-1 truncate transition-colors">
                Promo Codes: {{ $event->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg px-4 py-3 text-sm font-semibold shadow-sm transition-colors">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg px-4 py-3 text-sm font-semibold shadow-sm transition-colors">{{ session('error') }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/10">
                    <h3 class="font-bold text-gray-900 dark:text-white tracking-tight">Active Promo Codes</h3>
                </div>
                
                @if($promoCodes->isEmpty())
                    <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                        No promo codes found for this event. Create one below!
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold text-[10px]">
                                <tr>
                                    <th class="px-6 py-3">Code</th>
                                    <th class="px-6 py-3">Discount</th>
                                    <th class="px-6 py-3">Usage</th>
                                    <th class="px-6 py-3">Expires At</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                @foreach($promoCodes as $code)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                            <span class="bg-gray-100 dark:bg-black/50 border border-gray-200 dark:border-white/10 px-2 py-1 rounded font-mono">{{ $code->code }}</span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            @if($code->discount_type === 'percentage')
                                                {{ $code->discount_amount }}% OFF
                                            @else
                                                ₹{{ number_format($code->discount_amount, 2) }} OFF
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-gray-900 dark:text-white font-medium">{{ $code->uses }}</span>
                                            <span class="text-gray-400 text-xs">/ {{ $code->max_uses ?? '∞' }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">
                                            {{ $code->expires_at ? $code->expires_at->format('M j, Y g:i A') : 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('promo_codes.destroy', [$event, $code]) }}" method="POST" onsubmit="return confirm('Delete this promo code?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-xs uppercase tracking-wider">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Create Form -->
            <div class="card p-6 sm:p-8">
                <h3 class="font-bold text-gray-900 dark:text-white tracking-tight mb-6">Create New Promo Code</h3>
                <form action="{{ route('promo_codes.store', $event) }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code Name</label>
                            <input type="text" name="code" class="form-input w-full uppercase bg-white dark:bg-[#0A0A0A] border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg focus:ring-purple-500 focus:border-purple-500 placeholder-gray-400 dark:placeholder-gray-500" placeholder="e.g. EARLYBIRD" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Discount</label>
                                <input type="number" step="0.01" name="discount_amount" class="form-input w-full bg-white dark:bg-[#0A0A0A] border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="20" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                <select name="discount_type" class="form-input w-full bg-white dark:bg-[#0A0A0A] border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    <option value="percentage">% Percentage</option>
                                    <option value="fixed">$ Fixed Amount</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usage Limit (Optional)</label>
                            <input type="number" name="max_uses" class="form-input w-full bg-white dark:bg-[#0A0A0A] border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg focus:ring-purple-500 focus:border-purple-500 placeholder-gray-400 dark:placeholder-gray-500" placeholder="e.g. 50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expiration Date (Optional)</label>
                            <input type="datetime-local" name="expires_at" class="form-input w-full bg-white dark:bg-[#0A0A0A] border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg focus:ring-purple-500 focus:border-purple-500 [color-scheme:light] dark:[color-scheme:dark]">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn-vercel w-full sm:w-auto">Create Promo Code</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
