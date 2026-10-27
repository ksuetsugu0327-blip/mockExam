<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KmasterCorrectionRequest extends FormRequest
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
            'cls_name' => 'em_length:20',
        ];
    }

    /**
     * validatorインスタンスの設定
     *
     * @param  Validator  $validator
     */
    public function withValidator($validator)
    {

        // パスワードのチェック(チェックボックスがONの場合のみチェックを実施)
        $validator->sometimes('password', 'required|only_alpha_num|between:6,50|confirmed', function ($input) {
            return isset($input->jpchange);
        });
        // パスワードのチェック(チェックボックスがONの場合のみチェックを実施)
        $validator->sometimes('password_confirmation', 'required', function ($input) {
            return isset($input->jpchange);
        });

        // バリデーション結果を判定し、エラーメッセージを追加する
        $validator->after(function ($validator) {
            if (count($validator->failed()) > 0) {
                $validator->errors()->add('message', trans('message.INPUT_ERROR_MESSAGE'));
            }
        });
    }
}
