<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonIDLoginCheckRequest extends FormRequest
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
            'psn_code' => 'required|em_length:11',
        ];
    }

    /**
     * 属性設定
     */
    public function attributes(): array
    {
        return [
            'psn_code' => '塾生ID',
        ];
    }
}
