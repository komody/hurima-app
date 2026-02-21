<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];

        // 購入完了（コンビニ払い）のフォーム
        if ($this->routeIs('purchase.complete-convenience')) {
            $rules['payment_method'] = 'required|in:コンビニ払い,カード支払い';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '支払い方法が不正です',
        ];
    }
}
