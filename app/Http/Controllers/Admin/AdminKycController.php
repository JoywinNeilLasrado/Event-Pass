<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminKycController extends Controller
{
    public function index()
    {
        $users = User::where('kyc_status', 'pending')
                     ->latest()
                     ->paginate(20);
                     
        return view('admin.kyc.index', compact('users'));
    }

    public function approve(User $user)
    {
        $user->update([
            'is_organizer' => true,
            'kyc_status' => 'approved'
        ]);

        return back()->with('success', 'Organizer firm approved successfully. They now have publishing rights.');
    }

    public function reject(User $user)
    {
        $user->update([
            'is_organizer' => false,
            'kyc_status' => 'rejected'
        ]);

        return back()->with('success', 'The organizer application has been securely rejected.');
    }
}
