<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'org_name' => ['required', 'string', 'max:255'],
            'org_email' => ['nullable', 'email', 'max:255'],
            'org_phone' => ['nullable', 'string', 'max:50'],
            'org_address' => ['nullable', 'string', 'max:1000'],
            'response_deadline_days' => ['required', 'integer', 'min:1', 'max:60'],
            'banner_text' => ['nullable', 'string', 'max:500'],
            'banner_is_active' => ['boolean'],
        ];
    }
}
