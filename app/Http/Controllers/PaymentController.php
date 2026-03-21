<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketBooked;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        $order_id = $request->get('order_id');
        if (!$order_id) {
            return redirect()->route('events.index')->with('error', 'Invalid payment session.');
        }

        $appId = config('services.cashfree.app_id');
        $secretKey = config('services.cashfree.secret_key');
        $env = config('services.cashfree.env', 'sandbox');
        $baseUrl = $env === 'sandbox' ? 'https://sandbox.cashfree.com/pg' : 'https://api.cashfree.com/pg';

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'x-client-id' => $appId,
            'x-client-secret' => $secretKey,
            'x-api-version' => '2023-08-01',
            'Accept' => 'application/json',
        ])->get($baseUrl . '/orders/' . $order_id);

        if (!$response->successful() || $response->json('order_status') !== 'PAID') {
            return redirect()->route('events.index')->with('error', 'Payment was not successful or is currently pending.');
        }

        if (str_starts_with($order_id, 'UPGRADE_')) {
            $parts = explode('_', $order_id);
            $userId = $parts[1];
            $user = \App\Models\User::find($userId);
            if ($user && $user->kyc_status === 'approved') {
                $user->update([
                    'is_organizer' => true,
                    'has_unlimited_events' => true
                ]);
                return redirect()->route('dashboard')->with('success', 'Welcome back! Your Pro Organizer account has been reactivated.');
            } else if ($user) {
                $user->update([
                    'kyc_status' => 'pending_submission',
                    'has_unlimited_events' => true
                ]);
                return redirect()->route('kyc.setup')->with('success', 'Pro payment successful! Please complete your organizer verification to activate your account.');
            }
        } elseif (str_starts_with($order_id, 'EVENT_')) {
            $parts = explode('_', $order_id);
            $eventId = $parts[1];
            \App\Models\Event::where('id', $eventId)->update(['is_published' => true, 'payment_status' => 'paid']);
            return redirect()->route('dashboard')->with('success', 'Your event has been published successfully! 🎉');
        } else {
            // Ticket Booking
            $bookings = Booking::where('cashfree_order_id', $order_id)->get();
            if ($bookings->isNotEmpty() && $bookings->first()->payment_status !== 'paid') {
                Booking::where('cashfree_order_id', $order_id)->update(['payment_status' => 'paid']);
                foreach ($bookings as $booking) {
                    Mail::to($booking->user->email)->send(new TicketBooked($booking));
                }
            }
            return redirect()->route('bookings.index')->with('success', 'Payment successful! Your tickets have been booked! 🎉');
        }
    }

    public function cancel(Request $request)
    {
        // For cashfree we might drop here generally if they close the modal
        return redirect()->route('events.index')->with('error', 'Payment cancelled.');
    }

    public function webhook(Request $request)
    {
        // Verify Cashfree Webhook Signature
        $payload = @file_get_contents('php://input');
        $signature = $request->header('x-webhook-signature');
        $timestamp = $request->header('x-webhook-timestamp');
        
        $secretKey = config('services.cashfree.secret_key');
        
        $data = $timestamp . $payload;
        $expectedSignature = base64_encode(hash_hmac('sha256', $data, $secretKey, true));

        if ($signature !== $expectedSignature) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);

        if (isset($event['type']) && $event['type'] == 'PAYMENT_SUCCESS_WEBHOOK') {
            $order_id = $event['data']['order']['order_id'];

            if (str_starts_with($order_id, 'UPGRADE_')) {
                $parts = explode('_', $order_id);
                $userId = $parts[1];
                $user = \App\Models\User::find($userId);
                if ($user && $user->kyc_status === 'approved') {
                    $user->update([
                        'is_organizer' => true,
                        'has_unlimited_events' => true
                    ]);
                } else if ($user) {
                    $user->update([
                        'kyc_status' => 'pending_submission',
                        'has_unlimited_events' => true
                    ]);
                }
            } elseif (str_starts_with($order_id, 'EVENT_')) {
                $parts = explode('_', $order_id);
                $eventId = $parts[1];
                \App\Models\Event::where('id', $eventId)->update(['is_published' => true, 'payment_status' => 'paid']);
            } else {
                $bookings = Booking::where('cashfree_order_id', $order_id)->get();
                if ($bookings->isNotEmpty() && $bookings->first()->payment_status !== 'paid') {
                    Booking::where('cashfree_order_id', $order_id)->update(['payment_status' => 'paid']);
                    foreach ($bookings as $booking) {
                        Mail::to($booking->user->email)->send(new TicketBooked($booking));
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
