<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'image' => ['required',  'mimes:jpeg,png'],
            'categories' => ['required', 'array'],
            'condition' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'brand_name' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [

            'name.required' => '商品名を入力してください',

            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',

            'image.required' => '商品画像をアップロードしてください',
            'image.mimes' => '商品画像はjpegまたはpng形式でアップロードしてください',

            'categories.required' => 'カテゴリーを選択してください',

            'condition.required' => '商品の状態を選択してください',

            'price.required' => '価格を入力してください',
            'price.numeric' => '価格は数値で入力してください',
            'price.min' => '価格は0円以上で入力してください',
        ];
    }
}
