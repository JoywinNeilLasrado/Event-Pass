<!DOCTYPE html>
<html lang="en" class="{{ session('theme', 'dark') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Verify Ticket</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 dark:bg-[#0A0A0A] text-gray-900 dark:text-gray-100 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="w-full max-w-md bg-white dark:bg-[#111] rounded-2xl shadow-2xl overflow-hidden border border-gray-200 dark:border-white/10">
        
        <!-- Header -->
        <div class="p-6 text-center border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
            <h1 class="text-2xl font-bold tracking-tight mb-1">Ticket Verification</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Event: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $event->title }}</span></p>
        </div>

        <!-- Ticket Body -->
        <div class="p-8 pb-10">
            
            <!-- Status Badge -->
            <div class="flex justify-center mb-8">
                @if($booking->is_checked_in)
                    <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/50 px-6 py-3 rounded-xl flex items-center gap-3 w-full justify-center shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="font-bold text-lg tracking-wide uppercase">Already Checked In</span>
                    </div>
                @else
                    <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-900/50 px-6 py-3 rounded-xl flex items-center gap-3 w-full justify-center shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold text-lg tracking-wide uppercase">Valid Ticket</span>
                    </div>
                @endif
            </div>

            <!-- Attendee Details -->
            <div class="space-y-5">
                <div>
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Attendee Name</p>
                    <p class="text-xl font-medium text-gray-900 dark:text-white">{{ $booking->user->name }}</p>
                </div>
                
                <div>
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Booking ID</p>
                    <p class="font-mono text-lg text-gray-700 dark:text-gray-300">#EVT-{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }}-B{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>

                @if($booking->is_checked_in)
                    <div class="pt-2">
                        <p class="text-xs font-bold text-red-400 dark:text-red-500 uppercase tracking-wider mb-1">Checked In At</p>
                        <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ $booking->updated_at->format('M j, Y - g:i A') }}</p>
                    </div>
                @endif
            </div>
            
            <!-- Check In Action (Creator Only) -->
            @if($isOwner)
                <div class="mt-10 pt-8 border-t border-gray-100 dark:border-white/10">
                    <p class="text-center text-xs text-gray-500 mb-4 font-medium uppercase tracking-wide">Organizer Actions</p>
                    
                    @if(Session::has('success'))
                        <div class="mb-4 text-center text-sm font-semibold text-green-600 dark:text-green-400">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    @if(Session::has('error'))
                        <div class="mb-4 text-center text-sm font-semibold text-red-600 dark:text-red-400">
                            {{ Session::get('error') }}
                        </div>
                    @endif
                    
                    @if(!$booking->is_checked_in)
                        <form action="{{ route('tickets.checkin', $booking->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors uppercase tracking-widest">
                                Approve Check In
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 cursor-not-allowed uppercase tracking-widest">
                            Checked In
                        </button>
                    @endif
                </div>
            @endif

            <!-- Back to Scanner Button -->
            <div class="mt-6">
                <a href="{{ route('scan') }}" class="w-full flex justify-center py-4 px-4 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm text-base font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-black hover:bg-gray-50 dark:hover:bg-gray-900 transition-all uppercase tracking-widest gap-2 items-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Scan Another Ticket
                </a>
            </div>

        </div>
    </div>
    
    <div class="mt-8 text-center text-xs text-gray-500 dark:text-gray-500 font-medium tracking-wide">
        Powered by Passage Verification System
    </div>

</body>
</html>
