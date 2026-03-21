<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'event' => function($q) { $q->withTrashed()->with('user'); }])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('event', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate(20)->withQueryString();
        return view('admin.bookings.index', compact('bookings'));
    }

    public function destroy(Booking $booking)
    {
        $event = $booking->event()->withTrashed()->first();
        if ($event) {
            $event->increment('available_tickets');
        }
        $booking->delete();
        return back()->with('success', 'Booking cancelled and seat returned.');
    }
}
