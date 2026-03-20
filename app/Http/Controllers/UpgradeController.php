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
        $user->update([
            'is_organizer' => true, 
            'has_unlimited_events' => false
        ]);
        
        return redirect()->route('dashboard')->with('success', 'Welcome to your Organizer Dashboard! You are on the Pay-As-You-Go plan. 🎉');
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
            return view('bookings.cashfree_checkout', [
                'paymentSessionId' => $response->json('payment_session_id'),
                'env' => $env
            ]);
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
        
        return redirect()->route('profile.edit')->with('status', 'plan-canceled');
    }
}
