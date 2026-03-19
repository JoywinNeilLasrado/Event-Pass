<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
        ]);

        $ticketType = $event->ticketTypes()->findOrFail($request->ticket_type_id);

        if ($ticketType->remaining > 0) {
            return back()->with('error', 'Tickets are currently available for this tier. You can book them directly.');
        }

        $alreadyWaitlisted = $event->waitlists()
            ->where('user_id', auth()->id())
            ->where('ticket_type_id', $ticketType->id)
            ->exists();

        if ($alreadyWaitlisted) {
            return back()->with('error', 'You are already on the waitlist for this ticket.');
        }

        $event->waitlists()->create([
            'user_id' => auth()->id(),
            'ticket_type_id' => $ticketType->id,
            'status' => 'pending'
        ]);

        return back()->with('success', 'You have successfully joined the waitlist! You will be automatically enrolled if a ticket becomes available.');
    }
}
