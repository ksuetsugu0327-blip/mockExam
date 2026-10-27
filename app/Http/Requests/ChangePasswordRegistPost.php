<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * パスワード変更処理用リクエスト
 */
class ChangePasswordRegistPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => ['required' | 'alpha_num' | 'min:8' | 'confirmed'],
        ];
    }

    /**
     * エラーメッセージ設定
     */
    /*
        public function messages()
        {
            return [
                'required'=>'※必須入力です',
                'min'=>'※:min文字以上の文字を入力してください',
                'confirmed'=>'※パスワードと確認用パスワードの入力内容が不一致です',
            ];
        }
     */

}
