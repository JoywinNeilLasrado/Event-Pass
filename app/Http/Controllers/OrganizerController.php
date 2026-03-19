<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    public function show(User $user)
    {
        $events = $user->events()
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->get();

        return view('organizers.show', compact('user', 'events'));
    }
}
