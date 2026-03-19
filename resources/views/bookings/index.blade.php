<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight transition-colors">
                My Tickets
            </h2>
            <a href="{{ route('events.index') }}" class="btn-vercel-secondary text-sm hidden sm:block">Browse Events</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg px-4 py-3 text-sm font-semibold shadow-sm transition-colors">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg px-4 py-3 text-sm font-semibold shadow-sm transition-colors">{{ session('error') }}</div>
            @endif

            {{-- ── MY BOOKINGS ── --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex justify-between items-center transition-colors">
                    <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors">Purchased Tickets</h3>
                </div>

                @if($myBookings->isEmpty())
                    <div class="text-center py-16">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-4 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4 transition-colors">You haven't booked any tickets yet.</p>
                        <a href="{{ route('events.index') }}" class="btn-vercel-secondary text-sm px-6 py-2">Discover Events &rarr;</a>
                    </div>
                @else
                    <div class="divide-y divide-gray-50 dark:divide-white/5 transition-colors">
                        @foreach($myBookings as $booking)
                            @php $event = $booking->event; @endphp
                            <div class="p-6 transition-colors hover:bg-white/30 dark:hover:bg-black/30">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-3 flex-wrap mb-1">
                                            @if($event->deleted_at)
                                                <span class="font-bold text-lg text-gray-400 dark:text-gray-500 line-through tracking-tight transition-colors">{{ $event->title }}</span>
                                                <span class="text-[10px] bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest shadow-sm transition-colors">Event Cancelled</span>
                                            @else
                                                <a href="{{ route('events.show', $event->id) }}"
                                                   class="font-bold text-lg text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors tracking-tight">
                                                    {{ $event->title }}
                                                </a>
                                                <span class="text-[10px] bg-white/50 dark:bg-black/50 backdrop-blur-sm border border-white/50 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest shadow-sm transition-colors">{{ $event->category->name }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center gap-4 text-sm font-medium text-gray-500 dark:text-gray-400 mt-2 transition-colors">
                                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{ $event->date->format('M j, Y') }} at {{ date('g:i A', strtotime($event->time)) }}</span>
                                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $event->location }}</span>
                                        </div>
                                        <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 mt-3 uppercase tracking-wide transition-colors">
                                            Ticket Reserved &bull; Booked on {{ $booking->created_at->format('M d, Y') }} 
                                            @if($booking->amount_paid > 0)
                                                &bull; Paid ${{ number_format($booking->amount_paid, 2) }}
                                            @elseif($booking->ticketType && $booking->ticketType->price > 0 && $booking->amount_paid == 0)
                                                &bull; <span class="text-green-500">100% OFF Code Applied</span>
                                            @else
                                                &bull; FREE
                                            @endif
                                        </p>
                                    </div>

                                    @if(!$event->deleted_at)
                                        <div class="flex-shrink-0 mt-4 sm:mt-0 flex items-center gap-3">
                                            <a href="{{ route('bookings.ticket', $event->id) }}" class="btn-vercel text-xs px-4 py-2 text-center" target="_blank">
                                                View E-Ticket
                                            </a>
                                            <form action="{{ route('bookings.destroy', $event->id) }}" method="POST"
                                                  onsubmit="return confirm('Cancel your ticket for this event?')">
                                                @csrf @method('DELETE')
                                                <button class="btn-vercel-secondary text-xs px-4 py-2 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-800 transition-colors focus:outline-none">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
