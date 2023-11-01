<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowProviderPlatformRequest extends FormRequest
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
            'id' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'icon_url' => 'nullable|url:http,https',
            'account_id' => 'nullable|unique:provider_platforms,account_id',
            'access_key' => 'nullable|unique:provider_platforms,access_key',
            'base_url' => 'nullable|url:http,https',
        ];
    }
}
