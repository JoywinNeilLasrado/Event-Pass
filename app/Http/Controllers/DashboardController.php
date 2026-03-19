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
            ->with(['category', 'bookings.user', 'ticketTypes'])
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
            'total_revenue'   => 0,
            'total_views'     => $myEvents->sum('views')
        ];

        // Analytics variables
        $salesByDate = [];
        $totalRevenue = 0;

        foreach ($myEvents as $event) {
            foreach ($event->bookings as $booking) {
                // Sales aggregation
                $dateStr = $booking->created_at->format('Y-m-d');
                if (!isset($salesByDate[$dateStr])) {
                    $salesByDate[$dateStr] = 0;
                }
                $salesByDate[$dateStr]++;

                // Revenue calculation
                if ($booking->ticketType) {
                    $totalRevenue += (float) $booking->ticketType->price;
                }
            }
        }

        $stats['total_revenue'] = $totalRevenue;
        
        // Sorting dates for the chart
        ksort($salesByDate);
        $chartLabels = array_keys($salesByDate);
        $chartData = array_values($salesByDate);

        // Fill empty dates if needed, or just pass as is (Chart.js handles categories fine)
        if (empty($chartLabels)) {
            $chartLabels = [date('Y-m-d')];
            $chartData = [0];
        }

        $conversionRate = $stats['total_views'] > 0 
            ? round(($stats['total_attendees'] / $stats['total_views']) * 100, 2) 
            : 0;

        return view('dashboard', compact('myEvents', 'myBookings', 'stats', 'chartLabels', 'chartData', 'conversionRate'));
    }
}
