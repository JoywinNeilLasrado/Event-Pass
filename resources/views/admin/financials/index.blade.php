<x-admin.layout>
    <x-slot name="title">Financials &amp; Vendor Payouts</x-slot>

    <!-- Global Math Metric Blocks -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="card p-6 flex flex-col justify-between border border-gray-100 dark:border-white/10">
            <div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-gray-500 dark:text-gray-400 mb-1 transition-colors">Gross Ticket Sales</p>
                <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight transition-colors">₹{{ number_format($globalSales, 2) }}</h3>
            </div>
            <div class="mt-4 flex items-center text-xs text-gray-500 dark:text-gray-400 font-medium">
                <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Total volume across platform
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between">
            <div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-gray-500 dark:text-gray-400 mb-1 transition-colors">Lifetime Platform Fees</p>
                <h3 class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400 tracking-tight transition-colors">₹{{ number_format($globalFees, 2) }}</h3>
            </div>
            <div class="mt-4 flex items-center text-xs text-indigo-600/70 dark:text-indigo-400/70 font-medium">
                Retaining {{ $feePercent }}% of total volume
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between border border-gray-100 dark:border-white/10">
            <div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-indigo-500 dark:text-indigo-400 mb-1 transition-colors">Event Listing Fees</p>
                <h3 class="text-3xl font-extrabold text-blue-600 dark:text-blue-500 tracking-tight transition-colors">₹{{ number_format($eventListingRevenue, 2) }}</h3>
            </div>
            <div class="mt-4 flex items-center text-xs text-blue-600/70 dark:text-blue-500/70 font-medium">
                {{ $paidEventsCount }} distinct events natively published
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between border border-gray-100 dark:border-white/10">
            <div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-indigo-500 dark:text-indigo-400 mb-1 transition-colors">Pro Upgrades Revenue</p>
                <h3 class="text-3xl font-extrabold text-blue-600 dark:text-blue-500 tracking-tight transition-colors">₹{{ number_format($proRevenue, 2) }}</h3>
            </div>
            <div class="mt-4 flex items-center text-xs text-blue-600/70 dark:text-blue-500/70 font-medium">
                {{ $proSubscribersCount }} active organizer accounts mapped
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between bg-green-50 dark:bg-green-900/10 border border-green-100 dark:border-green-800/30">
            <div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-green-600 dark:text-green-500 mb-1 transition-colors">Settled Vendor Payouts</p>
                <h3 class="text-3xl font-extrabold text-green-600 dark:text-green-500 tracking-tight transition-colors">₹{{ number_format($settledPayouts, 2) }}</h3>
            </div>
            <div class="mt-4 flex items-center text-xs text-green-600/70 dark:text-green-500/70 font-medium">
                Successfully routed to organizers
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/30">
            <div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-amber-600 dark:text-amber-500 mb-1 transition-colors">Outstanding Balances</p>
                <h3 class="text-3xl font-extrabold text-amber-600 dark:text-amber-500 tracking-tight transition-colors">₹{{ number_format($outstandingBalance, 2) }}</h3>
            </div>
            <div class="mt-4 flex items-center text-xs text-amber-600/70 dark:text-amber-500/70 font-medium">
                Pending future disbursement sweeps
            </div>
        </div>
    </div>

    <!-- Settlement Datatables -->
    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex flex-col sm:flex-row justify-between sm:items-center gap-4 transition-colors">
            <div class="flex items-center gap-3">
                <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors">Event Payout Ledgers</h3>
                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-[#1A1A1A] rounded-full transition-colors">{{ $events->total() }} recorded</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10 transition-colors">
                <thead class="bg-gray-50 dark:bg-[#111] transition-colors">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Event Details</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Gross Sales</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-widest transition-colors">Platform Cut</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Vendor Remit</th>
                        <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#0A0A0A] divide-y divide-gray-100 dark:divide-white/5 transition-colors">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#111] transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($event->poster_image)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-md object-cover border border-gray-200 dark:border-white/10" src="{{ Storage::url($event->poster_image) }}" alt="">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10 rounded-md bg-gray-100 dark:bg-[#1A1A1A] border border-gray-200 dark:border-white/10 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white transition-colors">{{ $event->title }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-medium transition-colors">Org: {{ $event->user->name }}</div>
                                        @if($event->payout_reference_id)
                                            <div class="text-[10px] text-gray-400 font-mono mt-0.5">Ref: {{ $event->payout_reference_id }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold text-gray-900 dark:text-white transition-colors">₹{{ number_format($event->calculated_sales, 2) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 transition-colors">{{ $event->bookings->where('payment_status', 'paid')->count() }} Tickets</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-indigo-600 dark:text-indigo-400 transition-colors">
                                ₹{{ number_format($event->calculated_platform_cut, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-white transition-colors">
                                ₹{{ number_format($event->calculated_vendor_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($event->calculated_sales == 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold text-gray-700 bg-gray-100 dark:text-gray-300 dark:bg-white/10 transition-colors">No Sales</span>
                                @elseif($event->payout_status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold text-green-800 bg-green-100 dark:text-green-300 dark:bg-green-900/30 transition-colors border border-green-200 dark:border-green-800/50">Settled</span>
                                @elseif($event->payout_status === 'failed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold text-red-800 bg-red-100 dark:text-red-300 dark:bg-red-900/30 transition-colors border border-red-200 dark:border-red-800/50">Failed</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold text-amber-800 bg-amber-100 dark:text-amber-300 dark:bg-amber-900/30 transition-colors border border-amber-200 dark:border-amber-800/50">Pending Sweep</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm font-medium text-gray-500 dark:text-gray-400 transition-colors">
                                No sales data has been recorded against platform events natively yet!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($events->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 transition-colors">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>
