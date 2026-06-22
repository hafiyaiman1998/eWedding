<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePreferencesRequest extends FormRequest
{
    /**
     * Boolean (checkbox) preference fields validated via an after() callback.
     *
     * @var array<int, string>
     */
    protected array $booleanFields = [
        'sidebar_collapsed',
        'floating_hearts_enabled',
        'animations_enabled',
        'background_animation_enabled',
        'background_blur_enabled',
        'email_notifications',
        'browser_notifications',
        'marketing_emails',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules shared by the user and admin preference forms.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function baseRules(): array
    {
        return [
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
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }

    /**
     * Manually validate boolean (checkbox) fields.
     */
    public function withValidator(Validator $validator): void
    {
        foreach ($this->booleanFields as $field) {
            $validator->after(function ($validator) use ($field) {
                $value = $this->input($field);
                if ($value !== null && ! in_array($value, ['0', '1', 0, 1, true, false, 'true', 'false', 'on'])) {
                    $validator->errors()->add($field, "The {$field} field must be a boolean value.");
                }
            });
        }
    }

    /**
     * Reproduce the controller's original Validator::make() failure response,
     * a redirect back with the errors, the original input, and an error flash.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check your input and try again.')
        );
    }
}
