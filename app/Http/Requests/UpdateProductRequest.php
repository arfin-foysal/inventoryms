<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'unit' => ['nullable', 'in:kg,litre,piece,box,pack,bottle,can,dozen,gram,milligram,milliliter,ounce,pint,pound,ton,yard'],
            'sku' => ['required', 'string'],
            'barcode' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer'],
            'brand_id' => ['nullable', 'integer'],
            'tags' => ['nullable', 'json'],
            'regular_price' => ['required', 'numeric'],
            'sale_price' => ['required', 'numeric'],
            'has_serials' => ['nullable', 'integer'],
            'is_description_shown_in_invoices' => ['required'],
            'has_related_products' => ['required'],
            'is_active' => ['required'],
        ];
    }
}
