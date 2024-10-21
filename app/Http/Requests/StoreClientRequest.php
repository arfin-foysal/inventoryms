<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|string|email',
            'address' => 'nullable|string',
            'image' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
            'company_name' => 'nullable|string',
            'customer_source' => 'nullable|string|in:Internal,External',
            'source_details' => 'nullable|string',
            'is_active' => 'nullable|integer',
        ];
    }
}
