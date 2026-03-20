<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketBooked;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        $session_id = $request->get('session_id');
        if (!$session_id) {
            return redirect()->route('events.index')->with('error', 'Invalid payment session.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = Session::retrieve($session_id);

        if ($session->client_reference_id && str_starts_with($session->client_reference_id, 'upgrade_')) {
            $userId = str_replace('upgrade_', '', $session->client_reference_id);
            if ($session->payment_status === 'paid') {
                \App\Models\User::where('id', $userId)->update([
                    'is_organizer' => true,
                    'has_unlimited_events' => true
                ]);
                return redirect()->route('dashboard')->with('success', 'Your account has been upgraded! You are now a Pro Organizer. 🎉');
            }
        } elseif ($session->client_reference_id && str_starts_with($session->client_reference_id, 'event_')) {
            $eventId = str_replace('event_', '', $session->client_reference_id);
            if ($session->payment_status === 'paid') {
                \App\Models\Event::where('id', $eventId)->update(['is_published' => true, 'payment_status' => 'paid']);
                return redirect()->route('dashboard')->with('success', 'Your event has been published successfully! 🎉');
            }
        } else {
            // Ticket Booking
            $booking = Booking::where('stripe_session_id', $session_id)->firstOrFail();
            if ($session->payment_status === 'paid' && $booking->payment_status !== 'paid') {
                $booking->update(['payment_status' => 'paid']);
                Mail::to($booking->user->email)->send(new TicketBooked($booking));
            }
            return redirect()->route('bookings.index')->with('success', 'Payment successful! Your ticket has been booked! 🎉');
        }

        return redirect()->route('events.index');
    }

    public function cancel(Request $request)
    {
        $session_id = $request->get('session_id');
        if ($session_id) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $session = Session::retrieve($session_id);

                if ($session->client_reference_id && str_starts_with($session->client_reference_id, 'upgrade_')) {
                    return redirect()->route('upgrade.index')->with('error', 'Upgrade cancelled.');
                } elseif ($session->client_reference_id && str_starts_with($session->client_reference_id, 'event_')) {
                    return redirect()->route('dashboard')->with('error', 'Event publication cancelled. The event remains a draft.');
                } else {
                    Booking::where('stripe_session_id', $session_id)->where('payment_status', 'pending')->delete();
                }
            } catch (\Exception $e) {
                // Ignore retrieval errors on cancel
            }
        }
        
        return redirect()->route('events.index')->with('error', 'Payment cancelled.');
    }

    public function webhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $endpoint_secret = config('services.stripe.webhook_secret');
        
        $payload = @file_get_contents('php://input');
        $sig_header = $request->header('HTTP_STRIPE_SIGNATURE', '');
        $event = null;

        try {
            if ($endpoint_secret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
            } else {
                $event = \Stripe\Event::constructFrom(json_decode($payload, true));
            }
        } catch(\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;

            if ($session->client_reference_id && str_starts_with($session->client_reference_id, 'upgrade_')) {
                $userId = str_replace('upgrade_', '', $session->client_reference_id);
                \App\Models\User::where('id', $userId)->update([
                    'is_organizer' => true,
                    'has_unlimited_events' => true
                ]);
            } elseif ($session->client_reference_id && str_starts_with($session->client_reference_id, 'event_')) {
                $eventId = str_replace('event_', '', $session->client_reference_id);
                \App\Models\Event::where('id', $eventId)->update(['is_published' => true, 'payment_status' => 'paid']);
            } else {
                $booking = Booking::where('stripe_session_id', $session->id)->first();
                if ($booking && $booking->payment_status !== 'paid') {
                    $booking->update(['payment_status' => 'paid']);
                    Mail::to($booking->user->email)->send(new TicketBooked($booking));
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
