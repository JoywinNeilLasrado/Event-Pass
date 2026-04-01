<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class UpgradeController extends Controller
{
    public function index()
    {
        $fee = (int) Setting::getVal('organizer_fee', 500);
        $eventFee = (int) Setting::getVal('event_fee', 100);
        return view('upgrade.index', compact('fee', 'eventFee'));
    }

    public function checkoutBasic(Request $request)
    {
        $user = auth()->user();

        // If they already passed KYC in the past but downgraded, restore them instantly
        if ($user->kyc_status === 'approved') {
            $user->update([
                'is_organizer' => true,
                'has_unlimited_events' => false
            ]);
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Welcome back! Your organizer account has been reactivated.', 'is_organizer' => true]);
            }
            return redirect()->route('dashboard')->with('success', 'Welcome back! Your organizer account has been reactivated.');
        }

        // Prevent rejected applications from bypassing the queue
        if ($user->kyc_status === 'rejected') {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Your organizer application was previously rejected by an administrator. Please contact support to appeal.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Your organizer application was previously rejected by an administrator. Please contact support to appeal.');
        }

        $user->update([
            'is_organizer' => false, 
            'kyc_status' => 'pending_submission',
            'has_unlimited_events' => false
        ]);
        
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Plan selected! Please complete your organizer verification.',
                'kyc_status' => 'pending_submission'
            ]);
        }
        return redirect()->route('kyc.setup')->with('success', 'Plan selected! Please complete your organizer verification to gain Dashboard access.');
    }

    public function checkoutPro(Request $request)
    {
        $fee = (int) Setting::getVal('organizer_fee', 500);

        $appId = config('services.cashfree.app_id');
        $secretKey = config('services.cashfree.secret_key');
        $env = config('services.cashfree.env', 'sandbox');
        $baseUrl = $env === 'sandbox' ? 'https://sandbox.cashfree.com/pg' : 'https://api.cashfree.com/pg';

        $cashfreeOrderId = 'UPGRADE_' . auth()->id() . '_' . time();

        $orderPayload = [
            'order_amount' => $fee,
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
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'paymentSessionId' => $response->json('payment_session_id'),
                    'orderId' => $cashfreeOrderId,
                    'env' => $env
                ]);
            }
            return view('bookings.cashfree_checkout', [
                'paymentSessionId' => $response->json('payment_session_id'),
                'env' => $env
            ]);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['error' => 'Failed to initialize upgrade payment.'], 500);
        }
        return back()->with('error', 'Failed to initialize upgrade payment.');
    }

    public function cancel(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'is_organizer' => false,
            'has_unlimited_events' => false
        ]);
        
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Organizer role cancelled successfully.']);
        }
        return redirect()->route('profile.edit')->with('status', 'plan-canceled');
    }
}
