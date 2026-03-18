<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'    => User::count(),
            'total_events'   => Event::withTrashed()->count(),
            'active_events'  => Event::count(),
            'deleted_events' => Event::onlyTrashed()->count(),
            'total_bookings' => Booking::count(),
            'categories'     => Category::count(),
            'tags'           => Tag::count(),
        ];

        $recentBookings = Booking::with(['user', 'event'])
            ->latest()
            ->take(8)
            ->get();

        $recentEvents = Event::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentEvents', 'recentUsers'));
    }
}
