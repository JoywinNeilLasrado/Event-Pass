<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'numeric|min:0',
        ]);

        foreach ($request->settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated_global_settings',
            'model_type' => Setting::class,
            'model_id' => null,
            'details' => ['updated_keys' => $request->settings]
        ]);

        return back()->with('success', 'Platform fees updated successfully.');
    }
}
