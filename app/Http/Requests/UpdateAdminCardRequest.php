<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminCardRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'design_template_id' => 'required|exists:design_templates,id',
            'custom_message' => 'nullable|string',
            'is_published' => 'boolean',
            'bride_name' => 'required|string|max:255',
            'groom_name' => 'required|string|max:255',
            'wedding_date' => 'required|string|max:255',
            'wedding_time' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'contact_bride' => 'nullable|string|max:255',
            'contact_groom' => 'nullable|string|max:255',
        ];
    }
}
