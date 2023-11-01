<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowPlatformConnectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'consumer_platform_id' => 'required_unless:provider_platform_id|string|max:255',
            'provider_platform_id' => 'required_unless:consumer_platform_id|string|max:255',
            'status'               => 'nullable|string|in:active,disabled,archived',
        ];
    }
}
