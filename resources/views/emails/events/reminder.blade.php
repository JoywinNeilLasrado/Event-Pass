<x-mail::message>
# Hi {{ explode(' ', $booking->user->name)[0] }},

Just a quick reminder that **{{ $event->title }}** is happening exactly tomorrow!

**Date:** {{ $event->date->format('l, F j, Y') }}  
**Time:** {{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}  
**Location:** {{ $event->location }}

Get your bags ready for an amazing experience! You can view or download your ticket anytime using the button below.

<x-mail::button :url="route('bookings.ticket', $event->id)">
View My Ticket
</x-mail::button>

See you there,<br>
The {{ config('app.name') }} Team
</x-mail::message>
