<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * リクエストの認可
     */
    public function authorize()
    {
        return true;
    }

    /**
     * バリデーションルール
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png|max:2048',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'condition_id' => 'required|exists:conditions,id',
            'price' => 'required|integer|min:0',
            'brand_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * バリデーションエラーメッセージ
     */
    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'image.required' => '商品画像をアップロードしてください',
            'image.image' => '画像ファイルを選択してください',
            'image.mimes' => '商品画像はjpegまたはpng形式でアップロードしてください',
            'category_ids.required' => 'カテゴリーを1つ以上選択してください',
            'condition_id.required' => '商品の状態を選択してください',
            'condition_id.exists' => '選択された商品の状態は無効です',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '販売価格は数値で入力してください',
            'price.min' => '販売価格は0円以上で入力してください',
        ];
    }
}
