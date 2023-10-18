<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class KmasterListRequest extends FormRequest
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
            'sch_code' => 'nullable|numeric',
            'cls_code' => 'nullable|numeric'
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
