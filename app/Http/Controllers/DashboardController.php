<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Events this user created, with booking count and attendee list
        $myEvents = $user->events()
            ->withTrashed()
            ->with(['category', 'bookings.user'])
            ->withCount('bookings')
            ->orderByDesc('date')
            ->get();

        // Tickets this user has booked
        $myBookings = $user->bookings()
            ->with(['event' => function ($q) {
                $q->withTrashed()->with(['category', 'user']);
            }])
            ->latest()
            ->get();

        // Quick stats
        $stats = [
            'events_created'  => $myEvents->where('deleted_at', null)->count(),
            'tickets_booked'  => $myBookings->count(),
            'total_attendees' => $myEvents->sum('bookings_count'),
        ];

        return view('dashboard', compact('myEvents', 'myBookings', 'stats'));
    }
}
