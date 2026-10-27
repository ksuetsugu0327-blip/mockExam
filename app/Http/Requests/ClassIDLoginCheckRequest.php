<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassIDLoginCheckRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cls_code' => 'required|digits_between:1,4|exists:m_class,cls_code,sch_code,'.$this->input('sch_code'),
        ];
    }

    /**
     * メッセージの設定
     *
     * @return type
     */
    public function messages()
    {
        return [
            'cls_code.exists' => trans('message.CLASS_CODE_NO_EXISTS'),
        ];
    }
}
