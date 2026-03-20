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
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $fee = (int) Setting::getVal('organizer_fee', 500);

        $stripeSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'inr',
                    'unit_amount' => $fee * 100,
                    'product_data' => [
                        'name' => 'Passage Pro Organizer Upgrade',
                        'description' => 'One-time fee to unlock unlimited free event publishing',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'client_reference_id' => 'upgrade_' . auth()->id(),
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('upgrade.index'),
        ]);

        return redirect()->away($stripeSession->url);
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
