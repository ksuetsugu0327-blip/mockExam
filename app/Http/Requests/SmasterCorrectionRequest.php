<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmasterCorrectionRequest extends FormRequest
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
            'cls_code'   => 'sometimes|required|digits_between:1,4|exists:m_class,cls_code,sch_code,'.$this->input('sch_code'),
            'psn_name'   => 'sometimes|em_length:20',
            'birthlow4'  => 'sometimes|birth_low4',
            'postallow4' => 'sometimes|postal_low4',
            'tellow4'    => 'sometimes|tel_low4',
        ];
    }

    /**
     * バリデーション結果を判定し、エラーメッセージを追加する
     * @param Validator $validator
     */
    public function withValidator($validator){        
        // パスワードのチェック(管理者以外の場合、必須設定) 
        // 塾ユーザ || 教室ユーザ || (管理者 && 代理ログイン中)の時必須
        $validator->sometimes('temppasswordReissueCheck','required',function(){
            return (
                    auth()->guard(config('constants.GUARD_SCHOOL'))->check() || 
                    auth()->guard(config('constants.GUARD_CLASS'))->check() ||
                    (
                        auth()->guard(config('constants.GUARD_ADMIN'))->check() && 
                        session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))
                    )
            );
        });        
        
        $validator->after(function($validator){
            if(count($validator->failed())>0){
                $validator->errors()->add('message',trans('message.INPUT_ERROR_MESSAGE'));
            }
        });
    }
            
        
        
        
}
