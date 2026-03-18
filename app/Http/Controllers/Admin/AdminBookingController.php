<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class AdminBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'event'])
            ->latest()
            ->paginate(20);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function destroy(Booking $booking)
    {
        $booking->event->increment('available_tickets');
        $booking->delete();
        return back()->with('success', 'Booking cancelled and seat returned.');
    }
}
