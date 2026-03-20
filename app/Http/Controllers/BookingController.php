<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketBooked;

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
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'promo_code' => 'nullable|string'
        ]);

        $alreadyBooked = $event->bookings()->where('user_id', auth()->id())->exists();
        if ($alreadyBooked) {
            return back()->with('error', 'You have already booked a ticket for this event.');
        }

        $ticketType = $event->ticketTypes()->findOrFail($request->ticket_type_id);
        
        $promoCode = null;
        $finalPrice = $ticketType->price;

        if ($request->filled('promo_code')) {
            $codeStr = strtoupper(trim($request->promo_code));
            $promoCode = $event->promoCodes()->where('code', $codeStr)->first();

            if (!$promoCode) {
                return back()->with('error', 'Invalid promo code.');
            }

            if ($promoCode->expires_at && $promoCode->expires_at->isPast()) {
                return back()->with('error', 'This promo code has expired.');
            }

            if ($promoCode->max_uses && $promoCode->uses >= $promoCode->max_uses) {
                return back()->with('error', 'This promo code usage limit has been reached.');
            }

            if ($promoCode->discount_type === 'percentage') {
                $finalPrice = $finalPrice - ($finalPrice * ($promoCode->discount_amount / 100));
            } else {
                $finalPrice = $finalPrice - $promoCode->discount_amount;
            }

            $finalPrice = max(0, $finalPrice);
        }

        $booking = null;

        $updated = DB::transaction(function () use ($event, $ticketType, $promoCode, $finalPrice, &$booking) {
            $event = Event::lockForUpdate()->find($event->id);
            $ticketType = \App\Models\TicketType::lockForUpdate()->find($ticketType->id);

            if ($event->remaining <= 0 || $ticketType->remaining <= 0) {
                return false;
            }

            if ($promoCode) {
                $promoCode = \App\Models\PromoCode::lockForUpdate()->find($promoCode->id);
                if ($promoCode->max_uses && $promoCode->uses >= $promoCode->max_uses) {
                    return false;
                }
                $promoCode->increment('uses');
            }

            $booking = $event->bookings()->create([
                'user_id' => auth()->id(),
                'ticket_type_id' => $ticketType->id,
                'promo_code_id' => $promoCode ? $promoCode->id : null,
                'amount_paid' => $finalPrice,
                'payment_status' => $finalPrice > 0 ? 'pending' : 'free',
            ]);

            return true;
        });

        if (!$updated) {
            return back()->with('error', 'Sorry, tickets or promo code are no longer available for this event.');
        }

        if ($finalPrice > 0) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            $successUrl = route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('payment.cancel') . '?session_id={CHECKOUT_SESSION_ID}';

            $stripeSession = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'inr',
                        'unit_amount' => (int) ($finalPrice * 100),
                        'product_data' => [
                            'name' => 'Ticket for ' . $event->title,
                            'description' => $ticketType->name,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

            $booking->update(['stripe_session_id' => $stripeSession->id]);

            return redirect()->away($stripeSession->url);
        }

        if ($booking) {
            Mail::to(auth()->user()->email)->send(new TicketBooked($booking));
        }

        if ($promoCode) {
            return back()->with('success', 'Promo code applied! Ticket booked for $0.00. 🎉');
        }

        return back()->with('success', 'Ticket booked successfully! Enjoy the event! 🎉');
    }

    public function destroy(Request $request, Event $event)
    {
        $booking = $event->bookings()->where('user_id', auth()->id())->first();

        if (!$booking) {
            return back()->with('error', 'You do not have a booking for this event.');
        }

        $ticketTypeId = $booking->ticket_type_id;

        // Process Refund if applicable
        if ($booking->payment_status === 'paid' && $booking->stripe_session_id) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $session = \Stripe\Checkout\Session::retrieve($booking->stripe_session_id);
                if ($session->payment_intent) {
                    \Stripe\Refund::create([
                        'payment_intent' => $session->payment_intent,
                    ]);
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to process refund automatically. Please contact support. Error: ' . $e->getMessage());
            }
        }

        DB::transaction(function () use ($booking) {
            $booking->delete();
        });

        $waitlistedUser = $event->waitlists()
            ->where('ticket_type_id', $ticketTypeId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($waitlistedUser) {
            \Illuminate\Support\Facades\Mail::to($waitlistedUser->user->email)->send(new \App\Mail\WaitlistAvailable($waitlistedUser));
            $waitlistedUser->update(['status' => 'notified']);
        }

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

        return $pdf->stream('Passage-Ticket-' . $event->id . '.pdf');
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
