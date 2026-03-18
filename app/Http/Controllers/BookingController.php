<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $alreadyBooked = $event->bookings()->where('user_id', auth()->id())->exists();
        if ($alreadyBooked) {
            return back()->with('error', 'You have already booked a ticket for this event.');
        }

        $updated = DB::transaction(function () use ($event) {
            $event = Event::lockForUpdate()->find($event->id);

            if ($event->available_tickets <= 0) {
                return false;
            }

            $event->decrement('available_tickets');
            $event->bookings()->create(['user_id' => auth()->id()]);

            return true;
        });

        if (!$updated) {
            return back()->with('error', 'Sorry, no tickets are available for this event.');
        }

        return back()->with('success', 'Ticket booked successfully! Enjoy the event! 🎉');
    }

    public function destroy(Request $request, Event $event)
    {
        $booking = $event->bookings()->where('user_id', auth()->id())->first();

        if (!$booking) {
            return back()->with('error', 'You do not have a booking for this event.');
        }

        DB::transaction(function () use ($booking, $event) {
            $booking->delete();
            $event->increment('available_tickets');
        });

        return back()->with('success', 'Your ticket has been cancelled and the seat has been returned. 🔓');
    }
}
