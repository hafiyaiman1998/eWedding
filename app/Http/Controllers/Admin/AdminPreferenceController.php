<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminPreferenceController extends Controller
{
    /**
     * Display the admin preferences page.
     */
    public function index()
    {
        $user = Auth::user();
        $preferences = $user->getPreferences();
        
        return view('admin.preferences.index', compact('preferences'));
    }

    /**
     * Update the admin preferences.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'theme' => 'required|in:light,dark,auto',
            'color_scheme' => 'required|in:default,pink,purple,blue,green,orange',
            'sidebar_collapsed' => 'boolean',
            'layout_density' => 'required|in:comfortable,compact,spacious',
            'font_size' => 'required|in:small,medium,large',
            'floating_hearts_enabled' => 'boolean',
            'animations_enabled' => 'boolean',
            'animation_speed' => 'required|in:slow,normal,fast',
            'background_theme' => 'required|in:romantic,elegant,modern,nature,sunset,ocean,royal,minimal',
            'background_animation_enabled' => 'boolean',
            'background_opacity' => 'required|in:light,medium,bold',
            'background_blur_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'browser_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'card_view_mode' => 'required|in:grid,list',
            'items_per_page' => 'required|integer|min:6|max:100',
            'language' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|in:Y-m-d,d/m/Y,m/d/Y,F j Y',
            'dashboard_widgets' => 'array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check your input and try again.');
        }

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
            'marketing_emails'
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
    public function reset()
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
    public function json()
    {
        $user = Auth::user();
        $preferences = $user->getPreferences();
        
        return response()->json([
            'preferences' => $preferences,
            'css_variables' => $preferences->getCssVariables()
        ]);
    }
}
