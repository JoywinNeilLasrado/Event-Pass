<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminKycController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $status = $request->query('status', 'pending');
        
        $users = User::whereNotNull('kyc_status')
                     ->where('kyc_status', $status)
                     ->latest()
                     ->paginate(20)
                     ->withQueryString();
                     
        return view('admin.kyc.index', compact('users', 'status'));
    }

    public function approve(User $user)
    {
        $user->update([
            'is_organizer' => true,
            'kyc_status' => 'approved'
        ]);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'approved_kyc',
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'details' => ['target_name' => $user->name, 'target_email' => $user->email]
        ]);

        return back()->with('success', 'Organizer firm approved successfully. They now have publishing rights.');
    }

    public function reject(User $user)
    {
        $user->update([
            'is_organizer' => false,
            'kyc_status' => 'rejected'
        ]);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'rejected_kyc',
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'details' => ['target_name' => $user->name, 'target_email' => $user->email]
        ]);

        return back()->with('success', 'The organizer application has been securely rejected.');
    }
}
