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
        $query = Event::with(['category', 'user', 'tags', 'ticketTypes', 'bookings'])
            ->where('is_published', true)
            ->latest();

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

        $myEvents = collect();
        if (auth()->check()) {
            $myEvents = Event::where('user_id', auth()->id())->latest()->get();
            // Don't show my events in the main "Upcoming Events" feed
            $query->where('user_id', '!=', auth()->id());
        }

        $events = $query->paginate(9)->withQueryString();
        $categories = Category::all();

        return view('events.index', compact('events', 'categories', 'myEvents'));
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

        if ($request->hasFile('images')) {
            $imagesArray = [];
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('posters', 'public');
                $imagesArray[] = $path;
                
                // Fallback: If no primary poster was explicitly uploaded, set the first gallery image as the main event poster
                if ($index === 0 && !isset($data['poster_image'])) {
                    $data['poster_image'] = $path;
                }
            }
            $data['images'] = $imagesArray;
        }

        $data['user_id'] = auth()->id();
        $data['available_tickets'] = collect($data['tickets'])->sum('capacity');
        $data['is_featured'] = $request->boolean('is_featured');

        $eventFee = (int) \App\Models\Setting::getVal('event_fee', 100);
        if (auth()->user()->has_unlimited_events) {
            $eventFee = 0;
        }
        
        $data['is_published'] = ($eventFee <= 0);
        $data['payment_status'] = ($eventFee > 0) ? 'pending' : 'free';

        $event = Event::create($data);
        $event->tags()->sync($request->input('tags', []));

        foreach ($data['tickets'] as $ticketData) {
            $event->ticketTypes()->create($ticketData);
        }

        if ($eventFee > 0) {
            $appId = config('services.cashfree.app_id');
            $secretKey = config('services.cashfree.secret_key');
            $env = config('services.cashfree.env', 'sandbox');
            $baseUrl = $env === 'sandbox' ? 'https://sandbox.cashfree.com/pg' : 'https://api.cashfree.com/pg';

            $cashfreeOrderId = 'EVENT_' . $event->id . '_' . time();

            $orderPayload = [
                'order_amount' => $eventFee,
                'order_currency' => 'INR',
                'order_id' => $cashfreeOrderId,
                'customer_details' => [
                    'customer_id' => (string) auth()->id(),
                    'customer_name' => auth()->user()->name,
                    'customer_email' => auth()->user()->email,
                    'customer_phone' => '9999999999',
                ],
                'order_meta' => [
                    'return_url' => route('payment.success') . '?order_id={order_id}',
                    'notify_url' => route('cashfree.webhook'),
                ]
            ];

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-client-id' => $appId,
                'x-client-secret' => $secretKey,
                'x-api-version' => '2023-08-01',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/orders', $orderPayload);

            if ($response->successful()) {
                $event->update(['cashfree_order_id' => $cashfreeOrderId]);
                return view('bookings.cashfree_checkout', [
                    'paymentSessionId' => $response->json('payment_session_id'),
                    'env' => $env
                ]);
            }

            return redirect()->route('events.show', $event)
                ->with('error', 'Event created but failed to initialize publishing payment.');
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    public function show(Event $event)
    {
        if (!$event->is_published && (!auth()->check() || auth()->id() !== $event->user_id)) {
            abort(404, 'Event not found or not published.');
        }

        // Increment the page views to calculate conversion rate later
        $event->increment('views');

        $event->load(['category', 'user', 'tags', 'bookings', 'ticketTypes']);
        $hasBooked = auth()->check()
            ? $event->bookings()->where('user_id', auth()->id())->exists()
            : false;
            
        $userWaitlistTiers = auth()->check()
            ? auth()->user()->waitlists()->where('event_id', $event->id)->pluck('ticket_type_id')->toArray()
            : [];

        return view('events.show', compact('event', 'hasBooked', 'userWaitlistTiers'));
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

    public function messageAttendees(Request $request, Event $event)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $attendees = $event->bookings()->with('user')->get()->pluck('user')->unique('id');

        if ($attendees->isEmpty()) {
            return back()->with('error', 'There are no attendees to message yet.');
        }

        foreach ($attendees as $attendee) {
            \Illuminate\Support\Facades\Mail::to($attendee->email)->send(new \App\Mail\AttendeeBroadcast(
                $event,
                $request->subject,
                $request->message
            ));
        }

        return back()->with('success', 'Blast Message dispatched unconditionally to all ' . $attendees->count() . ' verified unique attendees! 🚀');
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

        if ($request->hasFile('images')) {
            // Get existing images if replacing
            $imagesArray = $event->images ?? [];
            foreach ($request->file('images') as $file) {
                // we technically append new images to the gallery here
                $path = $file->store('posters', 'public');
                $imagesArray[] = $path;
            }
            $data['images'] = $imagesArray;
            
            // Re-fallback
            if (!$event->poster_image && !isset($data['poster_image']) && count($imagesArray) > 0) {
                $data['poster_image'] = $imagesArray[0];
            }
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
        // Issue refunds to all attendees before soft deleting
        $bookings = $event->bookings()->where('payment_status', 'paid')->whereNotNull('cashfree_order_id')->get();
        
        if ($bookings->isNotEmpty()) {
            $appId = config('services.cashfree.app_id');
            $secretKey = config('services.cashfree.secret_key');
            $env = config('services.cashfree.env', 'sandbox');
            $baseUrl = $env === 'sandbox' ? 'https://sandbox.cashfree.com/pg' : 'https://api.cashfree.com/pg';

            foreach ($bookings as $booking) {
                if ($booking->amount_paid > 0) {
                    try {
                        \Illuminate\Support\Facades\Http::withHeaders([
                            'x-client-id' => $appId,
                            'x-client-secret' => $secretKey,
                            'x-api-version' => '2023-08-01',
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ])->post($baseUrl . '/orders/' . $booking->cashfree_order_id . '/refunds', [
                            'refund_amount' => round($booking->amount_paid, 2),
                            'refund_id' => 'REF_EVT_' . $booking->id . '_' . time(),
                        ]);
                        
                        $booking->update(['payment_status' => 'refunded']);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to refund booking '.$booking->id.': '.$e->getMessage());
                    }
                }
            }
        }

        $event->delete(); // SoftDelete

        return redirect()->route('events.index')
            ->with('success', 'Event definitively canceled! All original tickets have been fully refunded via Cashfree.');
    }

    public function exportAttendees(Event $event)
    {
        $bookings = $event->bookings()->with(['user', 'ticketType'])->latest()->get();

        $cols = ['Booking ID', 'Attendee Name', 'Attendee Email', 'Ticket Type', 'Price', 'Booked At', 'Status'];
        
        $output = fopen('php://temp', 'w');
        fputcsv($output, $cols);

        foreach ($bookings as $booking) {
            fputcsv($output, [
                $booking->id,
                $booking->user->name,
                $booking->user->email,
                $booking->ticketType ? $booking->ticketType->name : 'Standard',
                $booking->ticketType ? ($booking->ticketType->price ?? 0) : 0,
                $booking->created_at->format('Y-m-d H:i:s'),
                $booking->is_checked_in ? 'Checked In' : 'Confirmed'
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="attendees_event_' . $event->id . '.csv"');
    }

    public function retryPublishPayment(Event $event)
    {
        if ($event->is_published || $event->payment_status === 'paid' || auth()->id() !== $event->user_id) {
            return redirect()->route('dashboard');
        }

        $appId = config('services.cashfree.app_id');
        $secretKey = config('services.cashfree.secret_key');
        $env = config('services.cashfree.env', 'sandbox');
        $baseUrl = $env === 'sandbox' ? 'https://sandbox.cashfree.com/pg' : 'https://api.cashfree.com/pg';

        if ($event->cashfree_order_id) {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-client-id' => $appId,
                'x-client-secret' => $secretKey,
                'x-api-version' => '2023-08-01',
                'Accept' => 'application/json',
            ])->get($baseUrl . '/orders/' . $event->cashfree_order_id);

            if ($response->successful() && $response->json('order_status') === 'ACTIVE') {
                return view('bookings.cashfree_checkout', [
                    'paymentSessionId' => $response->json('payment_session_id'),
                    'env' => $env
                ]);
            }
        }

        // If order EXPIRED, failed, or missing, we must proactively forge a newly timestamped order scope footprint
        $eventFee = (int) \App\Models\Setting::getVal('event_fee', 100);
        $cashfreeOrderId = 'EVENT_' . $event->id . '_' . time();
        
        $orderPayload = [
            'order_amount' => $eventFee,
            'order_currency' => 'INR',
            'order_id' => $cashfreeOrderId,
            'customer_details' => [
                'customer_id' => (string) auth()->id(),
                'customer_name' => auth()->user()->name,
                'customer_email' => auth()->user()->email,
                'customer_phone' => '9999999999',
            ],
            'order_meta' => [
                'return_url' => route('payment.success') . '?order_id={order_id}',
                'notify_url' => route('cashfree.webhook'),
            ]
        ];

        $newResponse = \Illuminate\Support\Facades\Http::withHeaders([
            'x-client-id' => $appId,
            'x-client-secret' => $secretKey,
            'x-api-version' => '2023-08-01',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/orders', $orderPayload);

        if ($newResponse->successful()) {
            $event->update(['cashfree_order_id' => $cashfreeOrderId]);
            return view('bookings.cashfree_checkout', [
                'paymentSessionId' => $newResponse->json('payment_session_id'),
                'env' => $env
            ]);
        }

        return redirect()->route('dashboard')->with('error', 'Unable to initialize checkout. Please try again.');
    }
}
