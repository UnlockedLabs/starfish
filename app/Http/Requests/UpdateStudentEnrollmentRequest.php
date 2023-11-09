<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;



class UpdateStudentEnrollmentRequest extends FormRequest
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
            'provider_user_id' => 'required|integer|exists:student_enrollments,provider_user_id',
            'provider_platform_id' => 'required|integer|exists:provider_platforms,id',
            'provider_content_id' => 'required|integer|exists:provider_contents,id',
            'status' => 'required|string|max:255',
            // status would be the only thing that would be changed
        ];
    }
}
