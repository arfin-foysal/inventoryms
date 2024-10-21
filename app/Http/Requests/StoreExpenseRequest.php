<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
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
            'support_id' => 'required|integer',
            'advance_amount' => 'nullable|numeric',
            'expense_amount' => 'nullable|numeric',
            'refund_amount' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|integer',
            'head_details' => 'nullable|array',
            'head_details.*.expense_head' => 'required|string',
            'head_details.*.amount' => 'nullable|numeric',
            'head_details.*.attachment' => 'nullable|string',
        ];      
    }
}
