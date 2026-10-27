<?php

namespace App\Http\Requests;

use App\Rules\EmailFormat;
use Illuminate\Foundation\Http\FormRequest;

class SmasterRegistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sch_code' => 'required|digits_between:1,7|exists:m_school,sch_code',
            'cls_code' => 'required|digits_between:1,4|exists:m_class,cls_code,sch_code,'.$this->input('sch_code'),
            'psn_code' => 'required|only_alpha_num|between:1,11|unique:m_person,psn_code,NULL,id,sch_code,'.$this->input('sch_code'),
            'psn_name' => 'em_length:20',
            'birthlow4' => 'birth_low4',
            'postallow4' => 'postal_low4',
            'tellow4' => 'tel_low4',
            'mail_address' => new EmailFormat,
        ];
    }

    /**
     * バリデーション結果を判定し、エラーメッセージを追加する
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
