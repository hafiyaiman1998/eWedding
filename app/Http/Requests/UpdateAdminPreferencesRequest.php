<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class UpdateAdminPreferencesRequest extends UpdatePreferencesRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'sidebar_collapsed' => 'boolean',
            'floating_hearts_enabled' => 'boolean',
            'animations_enabled' => 'boolean',
            'background_animation_enabled' => 'boolean',
            'background_blur_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'browser_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'dashboard_widgets' => 'array',
        ]);
    }

    /**
     * The admin form validates boolean fields via inline rules rather than the
     * after() callback used by the user form, so no extra checks are added.
     */
    public function withValidator(Validator $validator): void
    {
        //
    }
}
