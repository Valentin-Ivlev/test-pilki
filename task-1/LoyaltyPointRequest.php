<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoyaltyPointRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'account_type' => 'required|in:phone,card,email',
            'account_id' => 'required|string',
            'loyalty_points_rule' => 'sometimes|required',
            'description' => 'sometimes|required|string',
            'payment_id' => 'sometimes|required',
            'payment_amount' => 'sometimes|required|numeric',
            'payment_time' => 'sometimes|required|date',
            'points_amount' => 'sometimes|required|numeric|min:1',
            'transaction_id' => 'sometimes|required|integer',
        ];

        if ($this->isMethod('post') && $this->route()->getName() == 'loyalty.cancel') {
            $rules['cancellation_reason'] = 'required|string';
        }

        return $rules;
    }
}