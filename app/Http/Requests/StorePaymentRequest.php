<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'currency' => 'nullable|string', 
            'amount' => ['required'],
            'ads_type' => 'required|in:PIC,VID,LINK', 
            'is_ads_type_sec' => 'required|in:FEED,BANNER,SIDE', 
            //'status' => 'required|in:PENDING,PAID,FAILED',
            'method' => 'nullable|in:PAYSTACK',
        ];
    }

    /**
     * Get the custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'currency.required' => 'The currency field is required.',
            'currency.string' => 'The currency must be a valid string.',
            'currency.size' => 'The currency code must be exactly 3 characters.',
            'amount.required' => 'The amount field is required.',
            'amount.regex' => 'The amount format is invalid. It should be a valid currency format.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either PENDING, PAID, or FAILED.',
            'method.required' => 'The payment method field is required.',
            'method.in' => 'The payment method must be either PAYSTACK.',
        ];
    }
}
