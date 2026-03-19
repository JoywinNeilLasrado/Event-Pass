<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\URL;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $myBookings = $request->user()->bookings()
            ->with(['event' => function ($q) {
                $q->withTrashed()->with(['category', 'user']);
            }])
            ->latest()
            ->get();

        return view('bookings.index', compact('myBookings'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id'
        ]);

        $alreadyBooked = $event->bookings()->where('user_id', auth()->id())->exists();
        if ($alreadyBooked) {
            return back()->with('error', 'You have already booked a ticket for this event.');
        }

        $ticketType = $event->ticketTypes()->findOrFail($request->ticket_type_id);

        $updated = DB::transaction(function () use ($event, $ticketType) {
            $event = Event::lockForUpdate()->find($event->id);
            $ticketType = \App\Models\TicketType::lockForUpdate()->find($ticketType->id);

            if ($event->remaining <= 0 || $ticketType->remaining <= 0) {
                return false;
            }

            $event->bookings()->create([
                'user_id' => auth()->id(),
                'ticket_type_id' => $ticketType->id
            ]);

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

        DB::transaction(function () use ($booking) {
            $booking->delete();
        });

        return back()->with('success', 'Your ticket has been cancelled and the seat has been returned. 🔓');
    }

    public function downloadTicket(Request $request, Event $event)
    {
        $booking = $event->bookings()->where('user_id', auth()->id())->first();

        if (!$booking) {
            return back()->with('error', 'You do not have a booking for this event.');
        }

        // fetch SVG format. QRServer uses SVG fills rather than strokes, which DomPDF handles perfectly.
        $verifyUrl = URL::signedRoute('tickets.verify', ['booking' => $booking->id]);
        $svgData = file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=200x200&format=svg&data=' . urlencode($verifyUrl));
        $qrCode = base64_encode($svgData);

        $pdf = Pdf::loadView('bookings.ticket', compact('booking', 'event', 'qrCode'));

        return $pdf->stream('EventPass-Ticket-' . $event->id . '.pdf');
    }

    public function verifyTicket(Request $request, Booking $booking)
    {
        $event = $booking->event;

        // Force Login if scanning
        if (!auth()->check()) {
            return redirect()->guest(route('login'));
        }

        // Hard block for any user who did not create this specific event
        if (auth()->id() !== $event->user_id) {
            abort(403, 'Security Violation: Only the registered event organizer can scan and verify tickets for this event.');
        }

        $isOwner = true;

        return view('bookings.verify', compact('booking', 'event', 'isOwner'));
    }

    public function checkInTicket(Request $request, Booking $booking)
    {
        $event = $booking->event;

        if (!auth()->check() || auth()->id() !== $event->user_id) {
            abort(403, 'Unauthorized action. Only the event creator can check in attendees.');
        }

        if ($booking->is_checked_in) {
            return back()->with('error', 'This ticket has already been checked in!');
        }

        $booking->update(['is_checked_in' => true]);

        return back()->with('success', 'Attendee successfully checked in! ✅');
    }
}
