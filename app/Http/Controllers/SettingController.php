<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        // Validate that all inputs are strings or null to prevent array injection
        $validatedData = $request->validate([
            '*' => 'nullable|string',
        ]);

        $data = collect($validatedData)->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
