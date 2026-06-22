<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminPreferencesRequest;
use App\Models\UserPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminPreferenceController extends Controller
{
    /**
     * Display the admin preferences page.
     */
    public function index(): View
    {
        $user = Auth::user();
        $preferences = $user->getPreferences();

        return view('admin.preferences.index', compact('preferences'));
    }

    /**
     * Update the admin preferences.
     */
    public function update(UpdateAdminPreferencesRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $preferences = $user->preferences ?: new UserPreference(['user_id' => $user->id]);

        // Convert checkbox values to boolean
        $data = $request->all();
        $booleanFields = [
            'sidebar_collapsed',
            'floating_hearts_enabled',
            'animations_enabled',
            'background_animation_enabled',
            'background_blur_enabled',
            'email_notifications',
            'browser_notifications',
            'marketing_emails',
        ];

        foreach ($booleanFields as $field) {
            $data[$field] = $request->has($field);
        }

        $preferences->fill($data);
        $preferences->save();

        return redirect()->route('admin.preferences.index')
            ->with('success', 'Your preferences have been updated successfully!');
    }

    /**
     * Reset preferences to default.
     */
    public function reset(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->preferences) {
            $user->preferences->delete();
        }

        UserPreference::createDefaults($user->id);

        return redirect()->route('admin.preferences.index')
            ->with('success', 'Your preferences have been reset to default values.');
    }

    /**
     * Get preferences as JSON for AJAX requests.
     */
    public function json(): JsonResponse
    {
        $user = Auth::user();
        $preferences = $user->getPreferences();

        return response()->json([
            'preferences' => $preferences,
            'css_variables' => $preferences->getCssVariables(),
        ]);
    }
}
