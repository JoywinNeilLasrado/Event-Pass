<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class AdminSystemHealthController extends Controller
{
    public function index()
    {
        $pendingJobs = DB::table('jobs')->get();
        $failedJobs = DB::table('failed_jobs')->latest('failed_at')->get();

        return view('admin.system_health.index', compact('pendingJobs', 'failedJobs'));
    }

    public function retry($id)
    {
        try {
            Artisan::call('queue:retry', ['id' => $id]);
            return back()->with('success', 'Job ' . $id . ' has been successfully pushed back onto the active queue.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to retry job: ' . $e->getMessage());
        }
    }

    public function deleteFailed($id)
    {
        try {
            Artisan::call('queue:forget', ['id' => $id]);
            return back()->with('success', 'Job ' . $id . ' has been permanently discarded from the failure ledger.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to discard job: ' . $e->getMessage());
        }
    }

    public function flushFailed()
    {
        try {
            Artisan::call('queue:flush');
            return back()->with('success', 'All failed background jobs have been explicitly flushed from the system.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to flush queues: ' . $e->getMessage());
        }
    }
}
