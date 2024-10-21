<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
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
            'sub_total' => 'required|numeric',
            'discount' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            'due_amount' => 'required|numeric',
            'qr_code' => 'nullable|string|max:255',
            'payment_method' => 'required|string|in:Cash,BankTransfer,Cheque,OnlinePayment,Bkash,Ucash,Nagad,Rocket,MobileBanking,AgentBanking,Others',
            'description' => 'nullable|string|max:1000',
            'attachment' => 'nullable|string|max:255',
            'transition_id' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:255',
            'product_details' => 'required|array',
            'product_details.*.product_id' => 'required|exists:products,id',
            'product_details.*.serial_number' => 'nullable|string|max:255',
            'product_details.*.qty' => 'required|integer',
            'product_details.*.unit_price' => 'required|numeric',
        ];
    }
}
