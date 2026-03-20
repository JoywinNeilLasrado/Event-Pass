<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;

class StripeConnectController extends Controller
{
    public function connect(Request $request)
    {
        $user = $request->user();

        if (!$user->is_organizer) {
            abort(403, 'Only organizers can connect Stripe accounts.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Create an Express account if they don't have one yet
        if (!$user->stripe_account_id) {
            $account = Account::create([
                'type' => 'express',
                'email' => $user->email,
            ]);

            $user->update(['stripe_account_id' => $account->id]);
        }

        // Generate the Account Link for onboarding
        $accountLink = AccountLink::create([
            'account' => $user->stripe_account_id,
            'refresh_url' => route('stripe.connect.refresh'),
            'return_url' => route('stripe.connect.return'),
            'type' => 'account_onboarding',
        ]);

        return redirect()->away($accountLink->url);
    }

    public function returnFromStripe(Request $request)
    {
        $user = $request->user();
        
        Stripe::setApiKey(config('services.stripe.secret'));
        
        // Retrieve the account to check if onboarding is actually completed
        if ($user->stripe_account_id) {
            $account = Account::retrieve($user->stripe_account_id);
            if ($account->details_submitted) {
                $user->update(['stripe_onboarding_completed' => true]);
                return redirect()->route('dashboard')->with('success', 'Stripe account linked successfully! You can now receive automatic payouts.');
            }
        }

        return redirect()->route('dashboard')->with('error', 'Stripe onboarding was not completed. Please try again when you are ready.');
    }

    public function refresh(Request $request)
    {
        // Stripe redirects here if the link expires
        return redirect()->route('stripe.connect')->with('error', 'Your session expired. Please try connecting your bank account again.');
    }
}
