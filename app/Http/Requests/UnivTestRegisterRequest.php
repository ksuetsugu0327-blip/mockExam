<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UnivTestRegisterRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'oem_sch_code' => 'required',
            'fiscal_year' => 'required',
            'grade' => 'required',
            'test_name_cd' => 'required',
            'highschool_cls' => 'nullable|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'oem_sch_code.required' => '塾は必須です。',
            'fiscal_year.required' => '年度は必須です。',
            'grade.required' => '学年は必須です。',
            'test_name_cd.required' => 'テスト名は必須です。',
            'highschool_cls.max' => '備考には:max文字以下の文字列を指定してください。',
        ];
    }
    
    /**
     * バリデーション結果を判定し、エラーメッセージを追加する
     * @param Validator $validator
     */
    public function withValidator($validator){
        $validator->after(function($validator){
            if(count($validator->failed())>0){
                $validator->errors()->add('message',trans('message.INPUT_ERROR_MESSAGE'));
            }
        });
    }

}
