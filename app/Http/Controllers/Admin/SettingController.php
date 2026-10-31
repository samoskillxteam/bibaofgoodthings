<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        // Send flat array — Vue groups them client-side
        $settings = Setting::orderBy('group')->orderBy('id')->get();

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->input('settings', []);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : (string) $value]
            );
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            Setting::updateOrCreate(['key' => 'logo_path'], ['value' => 'storage/' . $path]);
        }

        if ($request->hasFile('logo_dark')) {
            $path = $request->file('logo_dark')->store('branding', 'public');
            Setting::updateOrCreate(['key' => 'logo_dark_path'], ['value' => 'storage/' . $path]);
        }

        Cache::forget('app_settings');

        return back()->with('success', 'Application settings updated successfully.');
    }
}
