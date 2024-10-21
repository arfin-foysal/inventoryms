<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupportAccountRequest extends FormRequest
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
            'paid_amount' => ['required', 'numeric'],
            'payment_method' => 'required|string|in:Cash,BankTransfer,Cheque,OnlinePayment,Bkash,Ucash,Nagad,Rocket,MobileBanking,AgentBanking,Others',
            'description' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'transition_id' => ['nullable', 'string'],
            'is_advance_paid' => ['nullable', 'integer'],
            'is_refund_paid' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'integer'],
           
        ];
    }
}
