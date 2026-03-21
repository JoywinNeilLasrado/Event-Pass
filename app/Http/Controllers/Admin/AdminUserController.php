<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('employer')->withCount(['events', 'bookings'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            switch ($request->role) {
                case 'admin':
                    $query->where('is_admin', true);
                    break;
                case 'organizer':
                    $query->where('is_organizer', true);
                    break;
                case 'staff':
                    $query->whereNotNull('employer_id');
                    break;
                case 'user':
                    $query->where('is_admin', false)->where('is_organizer', false)->whereNull('employer_id');
                    break;
            }
        }

        $users = $query->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own admin status.');
        }
        $user->update(['is_admin' => !$user->is_admin]);
        $status = $user->is_admin ? 'granted admin' : 'revoked admin from';
        return back()->with('success', "Successfully {$status} {$user->name}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }
        $user->delete();
        return back()->with('success', "User {$user->name} has been deleted.");
    }
}
