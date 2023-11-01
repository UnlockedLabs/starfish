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
            'provider_resource_id' => 'required|string|max:255',
            'provider_id' => 'required|unique:provider_platforms,access_key',
            'provider_user_id' => 'required|string|max:255',
            'status' => 'nullable|url:http,https',
        ];
    }
}
