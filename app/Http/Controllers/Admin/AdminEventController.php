<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;

class AdminEventController extends Controller
{
    public function index()
    {
        $events = Event::withTrashed()
            ->with(['user', 'category'])
            ->withCount('bookings')
            ->latest()
            ->paginate(15);
        return view('admin.events.index', compact('events'));
    }

    public function restore($id)
    {
        $event = Event::onlyTrashed()->findOrFail($id);
        $event->restore();
        return back()->with('success', "Event \"{$event->title}\" has been restored.");
    }

    public function forceDestroy($id)
    {
        $event = Event::withTrashed()->findOrFail($id);
        $event->forceDelete();
        return back()->with('success', 'Event permanently deleted.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return back()->with('success', "Event \"{$event->title}\" soft-deleted.");
    }
}
