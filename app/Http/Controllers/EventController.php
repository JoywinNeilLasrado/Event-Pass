<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['category', 'user', 'tags'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $events = $query->paginate(9)->withQueryString();
        $categories = Category::all();

        return view('events.index', compact('events', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('events.create', compact('categories', 'tags'));
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('poster_image')) {
            $data['poster_image'] = $request->file('poster_image')->store('posters', 'public');
        }

        $data['user_id'] = auth()->id();
        $data['available_tickets'] = collect($data['tickets'])->sum('capacity');

        $event = Event::create($data);
        $event->tags()->sync($request->input('tags', []));

        foreach ($data['tickets'] as $ticketData) {
            $event->ticketTypes()->create($ticketData);
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    public function show(Event $event)
    {
        $event->load(['category', 'user', 'tags', 'bookings', 'ticketTypes']);
        $hasBooked = auth()->check()
            ? $event->bookings()->where('user_id', auth()->id())->exists()
            : false;
        return view('events.show', compact('event', 'hasBooked'));
    }

    public function edit(Event $event)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $selectedTags = $event->tags->pluck('id')->toArray();
        return view('events.edit', compact('event', 'categories', 'tags', 'selectedTags'));
    }

    public function attendees(Request $request, Event $event)
    {
        $query = $event->bookings()->with(['user', 'ticketType']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%"); // search by booking id
            });
        }

        $bookings = $query->latest()->get();
        $event->setRelation('bookings', $bookings); // Override relation with filtered data

        return view('events.attendees', compact('event'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $data = $request->validated();

        if ($request->hasFile('poster_image')) {
            if ($event->poster_image) {
                Storage::disk('public')->delete($event->poster_image);
            }
            $data['poster_image'] = $request->file('poster_image')->store('posters', 'public');
        }

        $data['available_tickets'] = collect($data['tickets'])->sum('capacity');
        $event->update($data);
        $event->tags()->sync($request->input('tags', []));

        $providedIds = [];
        foreach ($data['tickets'] as $ticketData) {
            if (isset($ticketData['id'])) {
                $providedIds[] = $ticketData['id'];
                $event->ticketTypes()->where('id', $ticketData['id'])->update([
                    'name' => $ticketData['name'],
                    'price' => $ticketData['price'],
                    'capacity' => $ticketData['capacity'],
                    'description' => $ticketData['description'] ?? null,
                ]);
            } else {
                $newTier = $event->ticketTypes()->create($ticketData);
                $providedIds[] = $newTier->id;
            }
        }

        // Delete removed tiers ONLY if they have no bookings
        $typesToDelete = $event->ticketTypes()->whereNotIn('id', $providedIds)->get();
        foreach ($typesToDelete as $type) {
            if ($type->bookings()->count() == 0) {
                $type->delete();
            }
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $event->delete(); // SoftDelete

        return redirect()->route('events.index')
            ->with('success', 'Event deleted (soft).');
    }
}
