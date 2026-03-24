<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    /**
     * Scan an Event Ticket (QR Code)
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $user = auth()->user();
        if (!$user->is_organizer && !$user->employer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access Denied: Only Organizers and Staff can scan tickets.'
            ], 403);
        }

        // Assuming the QR code text is exactly the Booking ID
        $bookingId = $request->input('qr_data');

        $booking = Booking::with('user', 'event')->find($bookingId);

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid ticket! Booking not found.'
            ], 404);
        }

        // Security: Ensure the person scanning actually owns the event (or works for the owner)
        $requiredOwnerId = $user->employer_id ? $user->employer_id : $user->id;
        if ($booking->event->user_id !== $requiredOwnerId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: This ticket belongs to a different organizer\'s event.'
            ], 403);
        }

        $booking = Booking::with('user', 'event')->find($bookingId);

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid ticket! Booking not found.'
            ], 404);
        }

        if ($booking->is_checked_in) {
            return response()->json([
                'status' => 'warning',
                'message' => 'WARNING: Ticket has already been scanned!',
                'booking' => $booking
            ], 400); 
        }

        // Check them in!
        $booking->is_checked_in = true;
        $booking->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Success! Attendee checked in.',
            'booking' => $booking
        ], 200);
    }
}
