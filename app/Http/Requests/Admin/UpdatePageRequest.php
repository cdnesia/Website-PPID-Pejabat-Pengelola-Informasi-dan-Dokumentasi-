<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'array'],
            'content.body' => ['nullable', 'string'],
            'content.visi' => ['nullable', 'string'],
            'content.misi' => ['nullable', 'array'],
            'content.misi.*' => ['string'],
            'content.items' => ['nullable', 'array'],
            'content.items.*.title' => ['required_with:content.items', 'string'],
            'content.items.*.description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
