<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 入力チェックルールの設定
     */
    public function rules(): array
    {
        return [
            'cls_code' => 'required|digits_between:1,4',
            'password' => 'required|string',
        ];
    }

    /**
     * 属性設定
     */
    public function attributes(): array
    {
        return [
            'cls_code' => '教室コード',
        ];
    }
}
