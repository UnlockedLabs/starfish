<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;



class ShowStudentEnrollmentRequest extends FormRequest
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
            'provider_user_id' => 'required|string|max:255',
            // 'provider_id' => 'required|string|max:255',
            // 'provider_content_id' => 'required|string|max:255',
            // 'status' => 'nullable|string|max:255',
        ];
    }
}
