<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowConsumerPlatformRequest extends FormRequest
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
            'id' => 'required|int',
            'type' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'api_key' => 'nullable|unique:consumer_platforms,api_key',
            'base_url' => 'nullable|url:http,https',
        ];
    }
}
