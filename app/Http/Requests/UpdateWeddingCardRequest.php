<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWeddingCardRequest extends FormRequest
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
            'design_template_id' => 'required|exists:design_templates,id',
            'title' => 'required|string|max:255',
            'card_details' => 'array',
            'custom_message' => 'nullable|string',
            'variable_files.*' => 'nullable|file|max:20000',
        ];
    }
}
