<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string'],
            'email' => ['nullable', 'email', 'string', 'max:255'],
            'number' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'name' => ['nullable', 'string'],
            'website' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'integer'],
            '_method' => ['required', 'string'],
        ];
    }
}
