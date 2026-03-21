<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-gray-400 dark:text-gray-500 hover:text-black dark:hover:text-white transition-colors flex items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight transition-colors">
                Manage Attendees
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="card p-6 border border-gray-100 dark:border-white/10 shadow-sm transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-100 dark:border-white/10 transition-colors">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight transition-colors">{{ $event->title }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 transition-colors">{{ $event->date->format('l, F j, Y') }} at {{ $event->time }} &bull; {{ $event->location }}</p>
                    </div>
                    @php $checkedInCount = $event->bookings->where('is_checked_in', true)->count(); @endphp
                    <div class="flex flex-wrap sm:flex-nowrap gap-4 border-t sm:border-t-0 border-gray-100 dark:border-white/10 pt-4 sm:pt-0 mt-2 sm:mt-0 transition-colors">
                        
                        @if($event->bookings->count() > 0)
                        <div x-data="{ open: false }">
                            <button @click="open = true" type="button" class="h-full flex flex-col items-center justify-center px-6 py-2 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors shadow-sm cursor-pointer group text-center outline-none">
                                <svg class="w-6 h-6 mb-1 text-indigo-600 dark:text-indigo-400 opacity-80 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 opacity-90 group-hover:opacity-100">Broadcast</span>
                            </button>

                            <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="open" @click="open = false" x-transition.opacity class="fixed inset-0 bg-gray-900/75 dark:bg-black/80 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    
                                    <div x-show="open" x-transition class="relative inline-block align-bottom bg-white dark:bg-neutral-900 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 dark:border-white/10">
                                        <div class="px-6 py-6 border-b border-gray-100 dark:border-white/10">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-xl leading-6 font-black text-gray-900 dark:text-white tracking-tight" id="modal-title">Broadcast Message</h3>
                                                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Send an email notification via the Event-Pass cloud to all <b>{{ $event->bookings->unique('user_id')->count() }}</b> verified ticket holders. When attendees reply, the message will route directly to your personal email inbox.</p>
                                        </div>
                                        <form action="{{ route('events.message_attendees', $event) }}" method="POST">
                                            @csrf
                                            <div class="px-6 py-5 space-y-4">
                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Subject Label</label>
                                                    <input type="text" name="subject" required class="form-input-vercel w-full" placeholder="e.g. Venue Change Alert! 📍">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Message Body</label>
                                                    <textarea name="message" required rows="5" class="form-input-vercel w-full" placeholder="Type your broadcast message here..."></textarea>
                                                </div>
                                            </div>
                                            <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-100 dark:border-white/10 flex flex-row-reverse gap-3">
                                                <button type="submit" class="btn-vercel border-0 bg-indigo-600 hover:bg-indigo-700 text-white shadow-md text-sm px-6 py-2 transition-colors">
                                                    Send Broadcast
                                                </button>
                                                <button type="button" @click="open = false" class="btn-vercel-secondary text-sm px-6 py-2 transition-colors">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-lg px-4 py-2 text-center transition-colors">
                            <span class="block text-2xl font-black text-gray-900 dark:text-white">{{ $event->bookings->count() }}</span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Booked</span>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-lg px-4 py-2 text-center transition-colors">
                            <span class="block text-2xl font-black text-green-700 dark:text-green-400">{{ $checkedInCount }}</span>
                            <span class="text-[10px] font-bold text-green-700 dark:text-green-400 uppercase tracking-widest">Checked In</span>
                        </div>
                    </div>
                </div>

                <!-- Search bar -->
                <form method="GET" action="{{ route('events.attendees', $event) }}" class="mb-6">
                    <div class="relative max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or booking ID..." onchange="this.form.submit()" class="form-input-vercel w-full pl-10 pr-4 py-2 text-sm bg-white/50 dark:bg-black/50 shadow-sm transition-colors rounded-lg">
                    </div>
                </form>

                @if($event->bookings->isEmpty())
                    <div class="text-center py-16">
                        <svg class="w-12 h-12 text-gray-200 dark:text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">No attendees have booked tickets for this event yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 sm:mx-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-white/5 border-b border-t sm:border-t-0 border-gray-100 dark:border-white/10 uppercase tracking-wider text-[10px] font-bold text-gray-500 dark:text-gray-400 transition-colors">
                                    <th class="py-3 px-6 whitespace-nowrap">Attendee</th>
                                    <th class="py-3 px-6 whitespace-nowrap">Contact Email</th>
                                    <th class="py-3 px-6 whitespace-nowrap">Ticket Details</th>
                                    <th class="py-3 px-6 text-center whitespace-nowrap">Status</th>
                                    <th class="py-3 px-6 text-right whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-white/5 transition-colors">
                                @foreach($event->bookings as $booking)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors group">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <span class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 flex items-center justify-center font-bold text-xs uppercase flex-shrink-0 border border-indigo-200 dark:border-indigo-800 shadow-sm transition-colors">{{ mb_substr($booking->user->name, 0, 1) }}</span>
                                                <span class="font-bold text-sm text-gray-900 dark:text-white transition-colors">{{ $booking->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400 transition-colors">{{ $booking->user->email }}</span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex flex-col gap-1.5 items-start">
                                                <span class="text-[10px] text-center bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded shadow-sm font-bold uppercase tracking-widest border border-indigo-100 dark:border-indigo-800 transition-colors">{{ $booking->ticketType?->name ?? 'Standard' }}</span>
                                                <span class="text-[11px] font-mono text-gray-400 dark:text-gray-500 transition-colors">#EVT-{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }}-B{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            @if($booking->is_checked_in)
                                                <span class="inline-flex items-center gap-1.5 text-[10px] bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-md px-2.5 py-1 font-bold uppercase tracking-widest shadow-sm transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Checked In
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-[10px] bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 rounded-md px-2.5 py-1 font-bold uppercase tracking-widest shadow-sm transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            @if(!$booking->is_checked_in)
                                                <form action="{{ route('tickets.checkin', $booking) }}" method="POST" class="inline-block" onsubmit="return confirm('Manually check in {{ $booking->user->name }}?')">
                                                    @csrf
                                                    <button type="submit" class="btn-vercel text-xs px-3 py-1.5 border border-black dark:border-white shadow-sm transition-colors opacity-80 hover:opacity-100">Check In</button>
                                                </form>
                                            @else
                                                <div class="flex flex-col items-end gap-0.5">
                                                    <span class="text-xs text-green-600 dark:text-green-500 font-bold transition-colors">✓ Complete</span>
                                                    <span class="text-[9px] text-gray-400 dark:text-gray-500 font-semibold uppercase tracking-wider transition-colors">{{ $booking->updated_at->format('M j, g:i A') }}</span>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
