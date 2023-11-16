<?php

use Illuminate\Foundation\Http\FormRequest;


class StoreStudentEnrollmentRequest extends FormRequest
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
            'provider_user_id' => 'required|integer|exists:student_mappings,provider_user_id',
            'provider_platform_id' => 'required|integer|exists:provider_platforms,id',
            'provider_content_id' => 'required|string|exists:provider_contents,provider_content_id',
            'status' => 'nullable|string|max:255',
        ];
    }
}
