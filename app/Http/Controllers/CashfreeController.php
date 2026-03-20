<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CashfreeController extends Controller
{
    public function connect(Request $request)
    {
        $request->validate([
            'bank_account_number' => 'required|string',
            'ifsc_code' => 'required|string',
            'account_holder_name' => 'required|string',
            'phone' => 'required|string',
            'pan_number' => 'required|string',
            'account_type' => 'required|string',
            'business_type' => 'required|string',
        ]);

        $user = $request->user();
        if (!$user->is_organizer) {
            abort(403, 'Only organizers can connect payout accounts.');
        }

        $appId = config('services.cashfree.app_id');
        $secretKey = config('services.cashfree.secret_key');
        $env = config('services.cashfree.env', 'sandbox');
        
        $baseUrl = $env === 'sandbox' ? 'https://sandbox.cashfree.com/pg' : 'https://api.cashfree.com/pg';

        $vendorId = 'ORG_' . $user->id; // Static vendor ID mapping to internal ID

        $response = Http::withHeaders([
            'x-client-id' => $appId,
            'x-client-secret' => $secretKey,
            'x-api-version' => '2023-08-01',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/easy-split/vendors', [
            'vendor_id' => $vendorId,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $request->phone,
            'status' => 'ACTIVE',
            'verify_account' => false,
            'bank' => [
                'account_number' => $request->bank_account_number,
                'account_holder' => $request->account_holder_name,
                'ifsc' => $request->ifsc_code,
            ],
            'kyc_details' => [
                'account_type' => strtoupper(trim($request->account_type)),
                'business_type' => $request->business_type,
                'pan' => strtoupper(trim($request->pan_number)),
                'name' => $request->account_holder_name, // Map name to Account Holder
            ]
        ]);

        if ($response->successful() && $response->json('vendor_id')) {
            $user->update(['cashfree_vendor_id' => $vendorId]);
            return redirect()->route('dashboard')->with('success', 'Your bank account has been successfully linked to Cashfree Payouts.');
        }

        // If vendor already exists, Cashfree might return an error, but we can gracefully handle or fetch it
        // Depending on the exact sandbox rules, returning raw message helps debugging
        $errorMsg = $response->json('message') ?? 'Unknown error connecting bank account.';
        return redirect()->route('dashboard')->with('error', 'Failed to link bank account: ' . $errorMsg);
    }

    public function disconnect(Request $request)
    {
        $user = $request->user();
        $user->update(['cashfree_vendor_id' => null]);
        return redirect()->route('dashboard')->with('success', 'Bank account disconnected successfully.');
    }
}
