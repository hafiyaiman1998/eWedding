<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'max_cards_per_user' => 'required|integer|min:1|max:100',
            'default_card_expiry' => 'required|integer|min:1|max:3650',
            'allow_custom_domains' => 'boolean',
            'enable_analytics' => 'boolean',
            'auto_approve_cards' => 'boolean',
        ];
    }

    /**
     * Reproduce the controller's original behavior, where the
     * ValidationException was caught and returned as a JSON 422 response.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
