<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupportRequest extends FormRequest
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
            'support_type_id' => 'required|integer',    
            'assign_date' => 'required|date',
            'deadline'=> 'nullable|date',
            'sale_id' => 'required|integer',
            'product_id' => 'nullable|integer',
            'employee_ids' => 'required|string',
            'task' => 'nullable|string',
            'attachment' => 'nullable|image|file|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'required|integer',
        ];
    }
}
