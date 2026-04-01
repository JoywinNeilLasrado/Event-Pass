<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KycController extends Controller
{
    public function setup(Request $request)
    {
        $user = auth()->user();

        // Already fully approved — they have organizer access
        if ($user->kyc_status === 'approved') {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['status' => 'approved', 'message' => 'Already approved']);
            }
            return redirect()->route('dashboard');
        }

        // Already submitted, waiting for admin review — redirect to events (not dashboard, they don't have is_organizer yet)
        if ($user->kyc_status === 'pending') {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['status' => 'pending', 'message' => 'Your organizer application is under review. You\'ll be notified once approved!']);
            }
            return redirect()->route('events.index')
                ->with('success', 'Your organizer application is under review. You\'ll be notified once approved!');
        }

        // null or pending_submission — show the form
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['status' => 'pending_submission', 'message' => 'Please provide KYC details']);
        }
        return view('kyc.setup');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'business_details' => 'required|string|max:1000',
            'social_links' => 'nullable|string|max:500'
        ]);

        auth()->user()->update([
            'business_details' => $request->business_details,
            'social_links' => $request->social_links,
            'kyc_status' => 'pending'
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Your Organizer Application has been submitted securely and is now Under Review.',
                'kyc_status' => 'pending'
            ]);
        }

        return redirect()->route('events.index')->with('success', 'Your Organizer Application has been submitted securely and is now Under Review.');
    }
}
