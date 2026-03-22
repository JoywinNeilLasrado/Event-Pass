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
        $bookings = $request->user()->bookings()
            ->with(['event' => function ($q) {
                $q->withTrashed()->with(['category', 'user']);
            }, 'ticketType'])
            ->latest()
            ->get();

        $groupedBookings = $bookings->groupBy('event_id');

        return view('bookings.index', compact('groupedBookings'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'promo_code' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1|max:10'
        ]);

        $quantity = (int) ($request->quantity ?? 1);

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

            if ($promoCode->max_uses && ($promoCode->uses + $quantity) > $promoCode->max_uses) {
                return back()->with('error', 'This promo code usage limit has been reached.');
            }

            if ($promoCode->discount_type === 'percentage') {
                $finalPrice = $finalPrice - ($finalPrice * ($promoCode->discount_amount / 100));
            } else {
                $finalPrice = $finalPrice - $promoCode->discount_amount;
            }

            $finalPrice = max(0, $finalPrice);
        }

        $bookings = collect();

        $updated = DB::transaction(function () use ($event, $ticketType, $promoCode, $finalPrice, $quantity, &$bookings) {
            $event = Event::lockForUpdate()->find($event->id);
            $ticketType = \App\Models\TicketType::lockForUpdate()->find($ticketType->id);

            if ($event->remaining < $quantity || $ticketType->remaining < $quantity) {
                return false;
            }

            if ($promoCode) {
                $promoCode = \App\Models\PromoCode::lockForUpdate()->find($promoCode->id);
                if ($promoCode->max_uses && ($promoCode->uses + $quantity) > $promoCode->max_uses) {
                    return false;
                }
                $promoCode->increment('uses', $quantity);
            }

            for($i = 0; $i < $quantity; $i++) {
                $bookings->push($event->bookings()->create([
                    'user_id' => auth()->id(),
                    'ticket_type_id' => $ticketType->id,
                    'promo_code_id' => $promoCode ? $promoCode->id : null,
                    'amount_paid' => $finalPrice,
                    'payment_status' => $finalPrice > 0 ? 'pending' : 'free',
                ]));
            }

            return true;
        });

        if (!$updated) {
            return back()->with('error', 'Sorry, tickets or promo code are no longer available for this event.');
        }

        $totalAmount = round($finalPrice * $quantity, 2);

        if ($totalAmount > 0) {
            $appId = config('services.cashfree.app_id');
            $secretKey = config('services.cashfree.secret_key');
            $env = config('services.cashfree.env', 'sandbox');
            $baseUrl = $env === 'sandbox' ? 'https://sandbox.cashfree.com/pg' : 'https://api.cashfree.com/pg';

            $cashfreeOrderId = 'ORDER_' . $bookings->first()->id . '_' . time();

            $orderPayload = [
                'order_amount' => round($totalAmount, 2),
                'order_currency' => 'INR',
                'order_id' => $cashfreeOrderId,
                'customer_details' => [
                    'customer_id' => (string) auth()->id(),
                    'customer_name' => auth()->user()->name,
                    'customer_email' => auth()->user()->email,
                    'customer_phone' => auth()->user()->phone ?? '9999999999',
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
                $paymentSessionId = $response->json('payment_session_id');
                // Store order ID mapped directly to our database cache for ALL generated bookings
                foreach ($bookings as $booking) {
                    $booking->update(['cashfree_order_id' => $cashfreeOrderId]);
                }
                
                return view('bookings.cashfree_checkout', [
                    'paymentSessionId' => $paymentSessionId,
                    'env' => $env
                ]);
            }

            return back()->with('error', 'Payment gateway error: ' . $response->json('message', 'Unknown error'));
        }

        foreach ($bookings as $booking) {
            Mail::to(auth()->user()->email)->send(new TicketBooked($booking));
        }

        if ($promoCode) {
            return back()->with('success', "Promo code applied! $quantity Ticket(s) booked. 🎉");
        }

        return back()->with('success', "$quantity Ticket(s) booked successfully! Enjoy the event! 🎉");
    }

    public function destroy(Request $request, Event $event)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1'
        ]);

        $bookings = $event->bookings()->where('user_id', auth()->id())->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', 'You do not have a booking for this event.');
        }

        $cancelQuantity = $request->input('quantity', $bookings->count());
        
        if ($cancelQuantity > $bookings->count()) {
            return back()->with('error', 'You cannot cancel more tickets than you own.');
        }

        $bookingsToCancel = $bookings->take($cancelQuantity);

        $ticketTypeId = $bookingsToCancel->first()->ticket_type_id;
        $totalRefund = $bookingsToCancel->sum('amount_paid');
        $cashfreeOrderId = $bookingsToCancel->first()->cashfree_order_id;
        $isPaid = $bookingsToCancel->first()->payment_status === 'paid';

        // Process Refund if applicable
        if ($isPaid && $cashfreeOrderId && $totalRefund > 0) {
            try {
                $appId = config('services.cashfree.app_id');
                $secretKey = config('services.cashfree.secret_key');
                $env = config('services.cashfree.env', 'sandbox');
                $baseUrl = $env === 'sandbox' ? 'https://sandbox.cashfree.com/pg' : 'https://api.cashfree.com/pg';

                \Illuminate\Support\Facades\Http::withHeaders([
                    'x-client-id' => $appId,
                    'x-client-secret' => $secretKey,
                    'x-api-version' => '2023-08-01',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post($baseUrl . '/orders/' . $cashfreeOrderId . '/refunds', [
                    'refund_amount' => $totalRefund,
                    'refund_id' => 'REF_' . $bookingsToCancel->first()->id . '_' . time(),
                ]);
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to process refund automatically. Please contact support. Error: ' . $e->getMessage());
            }
        }

        DB::transaction(function () use ($bookingsToCancel) {
            foreach ($bookingsToCancel as $booking) {
                $booking->delete();
            }
        });

        $freedSpots = $bookingsToCancel->count();
        $waitlistedUsers = $event->waitlists()
            ->where('ticket_type_id', $ticketTypeId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->take($freedSpots)
            ->get();

        foreach ($waitlistedUsers as $waitlistedUser) {
            Mail::to($waitlistedUser->user->email)->send(new \App\Mail\WaitlistAvailable($waitlistedUser));
            $waitlistedUser->update(['status' => 'notified']);
        }

        return back()->with('success', "Your {$freedSpots} ticket(s) have been cancelled and seats returned. 🔓");
    }

    public function downloadTicket(Request $request, Event $event)
    {
        $bookings = $event->bookings()->where('user_id', auth()->id())->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', 'You do not have a booking for this event.');
        }

        foreach ($bookings as $booking) {
            $verifyUrl = URL::signedRoute('tickets.verify', ['booking' => $booking->id]);
            $svgData = file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=200x200&format=svg&data=' . urlencode($verifyUrl));
            $booking->qrCode = base64_encode($svgData);
        }

        $pdf = Pdf::loadView('bookings.ticket', compact('bookings', 'event'));

        return $pdf->stream('Passage-Tickets-' . $event->id . '.pdf');
    }

    public function verifyTicket(Request $request, Booking $booking)
    {
        $event = $booking->event;

        // Force Login if scanning
        if (!auth()->check()) {
            return redirect()->guest(route('login'));
        }

        $isCreator = auth()->id() === $event->user_id;
        $isStaff = auth()->user()->employer_id === $event->user_id;

        if (!$isCreator && !$isStaff) {
            abort(403, 'Security Violation: Only the registered event organizer or authorized staff can scan and verify tickets for this event.');
        }

        $isOwner = true;

        return view('bookings.verify', compact('booking', 'event', 'isOwner'));
    }

    public function checkInTicket(Request $request, Booking $booking)
    {
        $event = $booking->event;

        if (!auth()->check()) {
            abort(403, 'Unauthorized action. Please login first.');
        }

        $isCreator = auth()->id() === $event->user_id;
        $isStaff = auth()->user()->employer_id === $event->user_id;

        if (!$isCreator && !$isStaff) {
            abort(403, 'Unauthorized action. Only the event creator or verified staff can check in attendees.');
        }

        if ($booking->is_checked_in) {
            return back()->with('error', 'This ticket has already been checked in!');
        }

        $booking->update(['is_checked_in' => true]);

        return back()->with('success', 'Attendee successfully checked in! ✅');
    }
}
