<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the settings form.
     *
     * @return View
     */
    public function index(): View
    {
        // Get all settings and group them by 'group'
        $settings = Setting::all()->groupBy('group');

        // If settings table is empty, we provide some default structural groupings
        // In a real scenario, you might want to seed default settings
        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings in database.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        // Whitelist of allowed setting keys to prevent arbitrary key injection
        $allowedKeys = [
            'site_title',
            'site_description',
            'contact_whatsapp',
            'contact_email',
            'company_address',
            'social_instagram',
            'social_linkedin',
        ];

        // Validate that all inputs are strings or null to prevent array injection
        $validatedData = $request->validate([
            'site_title' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'contact_whatsapp' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'company_address' => 'nullable|string|max:1000',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
        ]);

        $data = collect($validatedData)->except(['_token', '_method']);

        // Only update whitelisted keys
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        Log::channel('audit')->info('Settings updated', [
            'user_id' => auth()->id(),
            'keys' => $data->keys(),
            'ip' => request()->ip(),
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully!');
    }
}
