<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'blade_template' => 'required|string',
            'full_html_template' => 'nullable|string',
            'category' => 'required|string|max:255',
            'is_malaysian_design' => 'boolean',
            'preview_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'default_variables' => 'nullable|json',
            'is_active' => 'boolean',
            'parse_variables_used' => 'nullable|boolean',
        ];
    }
}
