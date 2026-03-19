<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index(Request $request, Event $event)
    {
        if ($event->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $promoCodes = $event->promoCodes()->latest()->get();

        return view('promo_codes.index', compact('event', 'promoCodes'));
    }

    public function store(Request $request, Event $event)
    {
        if ($event->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'discount_amount' => 'required|numeric|min:0.01',
            'discount_type' => 'required|in:fixed,percentage',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $codeStr = strtoupper(trim($request->code));

        if ($event->promoCodes()->where('code', $codeStr)->exists()) {
            return back()->with('error', 'This promo code already exists for this event.');
        }

        $event->promoCodes()->create([
            'code' => $codeStr,
            'discount_amount' => $validated['discount_amount'],
            'discount_type' => $validated['discount_type'],
            'max_uses' => $validated['max_uses'],
            'expires_at' => $validated['expires_at'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Promo code created successfully!');
    }

    public function destroy(Request $request, Event $event, PromoCode $promoCode)
    {
        if ($event->user_id !== $request->user()->id || $promoCode->event_id !== $event->id) {
            abort(403, 'Unauthorized action.');
        }

        $promoCode->delete();

        return back()->with('success', 'Promo code deleted successfully!');
    }

    public function validateCode(Request $request, Event $event)
    {
        $request->validate(['code' => 'required|string']);
        
        $codeStr = strtoupper(trim($request->code));
        $promoCode = $event->promoCodes()->where('code', $codeStr)->first();

        if (!$promoCode) {
            return response()->json(['valid' => false, 'message' => 'Invalid promo code.']);
        }

        if ($promoCode->expires_at && $promoCode->expires_at->isPast()) {
            return response()->json(['valid' => false, 'message' => 'Promo code has expired.']);
        }

        if ($promoCode->max_uses && $promoCode->uses >= $promoCode->max_uses) {
            return response()->json(['valid' => false, 'message' => 'Promo code usage limit reached.']);
        }

        return response()->json([
            'valid' => true,
            'discount_amount' => $promoCode->discount_amount,
            'discount_type' => $promoCode->discount_type,
            'message' => 'Promo code applied!'
        ]);
    }
}
