<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::withTrashed()
            ->with(['user', 'category'])
            ->withCount('bookings')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->whereNull('deleted_at');
                    break;
                case 'deleted':
                    $query->onlyTrashed();
                    break;
                case 'upcoming':
                    $query->whereNull('deleted_at')->where('date', '>=', now());
                    break;
                case 'past':
                    $query->whereNull('deleted_at')->where('date', '<', now());
                    break;
            }
        }

        $events = $query->paginate(15)->withQueryString();
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('admin.events.index', compact('events', 'categories'));
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
