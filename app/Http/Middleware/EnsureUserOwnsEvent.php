<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserOwnsEvent
{
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event');

        if (!$event instanceof Event) {
            $event = Event::findOrFail($event);
        }

        if ($event->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action. Only the event owner can perform this.');
        }

        return $next($request);
    }
}
