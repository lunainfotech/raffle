<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentAuthorizationRequest extends FormRequest
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
            'payment_intent_id' => 'required|string|max:255',
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|in:usd,eur,gbp',
            'description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'payment_intent_id.required' => 'Payment intent ID is required.',
            'member_id.required' => 'Member ID is required.',
            'member_id.exists' => 'Invalid member ID.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be greater than 0.',
            'currency.required' => 'Currency is required.',
            'currency.in' => 'Invalid currency specified.',
        ];
    }
} 