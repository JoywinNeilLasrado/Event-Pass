<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) return response()->json(['error' => 'User not found in system! They must register an Event-Pass account first before you can invite them.'], 404);
            return back()->with('error', 'User not found in system! They must register an Event-Pass account first before you can invite them.');
        }

        if ($user->employer_id && $user->employer_id !== auth()->id()) {
            if ($request->expectsJson() || $request->is('api/*')) return response()->json(['error' => 'Authentication rejected: This entity is already registered under another organizational tree.'], 403);
            return back()->with('error', 'Authentication rejected: This entity is already registered under another organizational tree.');
        }
        if ($user->employer_id === auth()->id()) {
            if ($request->expectsJson() || $request->is('api/*')) return response()->json(['error' => 'This user is already an active member of your staff roster.'], 400);
            return back()->with('error', 'This user is already an active member of your staff roster.');
        }
        if ($user->id === auth()->id()) {
            if ($request->expectsJson() || $request->is('api/*')) return response()->json(['error' => 'Invalid Target: You cannot self-assign as nested staff.'], 400);
            return back()->with('error', 'Invalid Target: You cannot self-assign as nested staff.');
        }
        
        $user->update(['employer_id' => auth()->id()]);
        if ($request->expectsJson() || $request->is('api/*')) return response()->json(['message' => 'User successfully appended to your active team matrix!']);
        return back()->with('success', 'User successfully appended to your active team matrix!');
    }

    public function destroy(User $staff)
    {
        if ($staff->employer_id !== auth()->id()) {
            if (request()->expectsJson() || request()->is('api/*')) return response()->json(['error' => 'Forbidden'], 403);
            abort(403);
        }

        $staff->update(['employer_id' => null]);
        if (request()->expectsJson() || request()->is('api/*')) return response()->json(['message' => 'Staff credentials successfully revoked and detached from your organizational tree.']);
        return back()->with('success', 'Staff credentials successfully revoked and detached from your organizational tree.');
    }
}
