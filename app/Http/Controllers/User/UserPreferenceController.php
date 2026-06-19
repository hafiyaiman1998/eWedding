<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    /**
     * Display the user preferences page.
     */
    public function index()
    {
        $user = Auth::user();
        $preferences = $user->getPreferences();
        
        return view('user.preferences.index', compact('preferences'));
    }

    /**
     * Update the user preferences.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'theme' => 'required|in:light,dark,auto',
            'color_scheme' => 'required|in:default,pink,purple,blue,green,orange',
            'layout_density' => 'required|in:comfortable,compact,spacious',
            'font_size' => 'required|in:small,medium,large',
            'animation_speed' => 'required|in:slow,normal,fast',
            'background_theme' => 'required|in:romantic,elegant,modern,nature,sunset,ocean,royal,minimal',
            'background_opacity' => 'required|in:light,medium,bold',
            'card_view_mode' => 'required|in:grid,list',
            'items_per_page' => 'required|integer|min:6|max:100',
            'language' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|in:Y-m-d,d/m/Y,m/d/Y,F j Y',
        ]);

        // Manually validate boolean fields (checkboxes)
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
            $validator->after(function ($validator) use ($request, $field) {
                $value = $request->input($field);
                if ($value !== null && !in_array($value, ['0', '1', 0, 1, true, false, 'true', 'false', 'on'])) {
                    $validator->errors()->add($field, "The {$field} field must be a boolean value.");
                }
            });
        }

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

        return redirect()->route('user.preferences.index')
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

        return redirect()->route('user.preferences.index')
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
