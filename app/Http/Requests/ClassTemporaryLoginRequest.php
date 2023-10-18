<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassTemporaryLoginRequest extends FormRequest
{
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * 入力チェックルールの設定
     * @return array
     */
    public function rules() {
        return [
            'password' => 'required|string',
        ];
    }

    /**
     * 属性設定
     * @return type
     */
    public function attributes() {
        return [
            'password' => 'パスワード'
        ];
    }
        
}
