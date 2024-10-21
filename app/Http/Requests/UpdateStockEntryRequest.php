<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockEntryRequest extends FormRequest
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
            'vendor_id' => 'required|integer',
            'invoice_number' => 'required|string',
            'received_date' => 'required|date',
            'price' => 'nullable|numeric',
            'discount_price' => 'nullable|numeric',
            'total_price' => 'nullable|numeric',
            // 'image' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
            'payment_status' => 'nullable|string',
            'product_details' => 'required|array',
            'product_details.*.product_id' => 'required|integer',
            'product_details.*.warranty_period' => 'nullable|in:years,months,days',
            'product_details.*.warranty_period_value' => 'nullable|string',
            'product_details.*.unit_price' => 'nullable|numeric',
            'product_details.*.total_price' => 'nullable|numeric',
            'product_details.*.qty' => 'required|integer',
            'product_details.*.description' => 'nullable|string',

        ];
    }
}