<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;



class StudentEnrollmentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // we should be able to look up everything by the student
            'provider_user_id' => 'required|integer|exists:student_mappings,provider_user_id',
            'provider_platform_id' => 'required|integer|exists:provider_platforms,id',
            'provider_content_id' => 'nullable|integer|exists:provider_contents,id',
            'status' => 'nullable|string|max:255',
        ];
    }
}
