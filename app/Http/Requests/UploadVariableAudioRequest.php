<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UploadVariableAudioRequest extends FormRequest
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
            'audio' => 'required|file|mimes:mp3,wav,ogg,aac,m4a|max:10240',
            'variable_name' => 'required|string|max:255',
            'template_id' => 'nullable|integer',
            'old_url' => 'nullable|string',
        ];
    }

    /**
     * Reproduce the controller's original try/catch behavior, where a
     * ValidationException was caught and returned as a JSON 500 response.
     */
    protected function failedValidation(Validator $validator): void
    {
        $message = (new ValidationException($validator))->getMessage();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Upload failed: '.$message,
        ], 500));
    }
}
