<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class KmasterRegistRequest extends FormRequest
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
            'sch_code' => 'required|digits_between:1,7|exists:m_school,sch_code',
            'cls_code' => 'required|digits_between:1,4|unique:m_class,cls_code,NULL,id,sch_code,'.$this->input('sch_code'),
            'cls_name' => 'em_length:20',
            'password' => 'required_if:jpchange,on|only_alpha_num|between:6,50|confirmed',
            'password_confirmation' => 'required_if:jpchange,on',

        ];
    }

    public function messages(): array
    {
        return [
            'password.required_if' => 'パスワードは必須です。',
            'password_confirmation.required_if' => 'パスワード(確認)は必須です。',
        ];
    }

    /**
     * バリデーション結果を判定し、エラーメッセージを追加する
     *
     * @param  Validator  $validator
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if (count($validator->failed()) > 0) {
                $validator->errors()->add('message', trans('message.INPUT_ERROR_MESSAGE'));
            }
        });
    }
}
