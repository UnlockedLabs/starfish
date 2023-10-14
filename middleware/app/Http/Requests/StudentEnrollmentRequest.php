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
            'provider_user_id' => 'required|string|max:255',
            'provider_id' => 'required|string|max:255',
            'provider_content_id' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ];
    }
}
