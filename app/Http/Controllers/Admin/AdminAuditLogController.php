<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')
                        ->latest()
                        ->paginate(20);

        return view('admin.audit_logs.index', compact('logs'));
    }
}
