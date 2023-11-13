<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Rules\IPAddressFormat;

class JmasterRegistRequest extends FormRequest
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
            'sch_code'       => 'required|digits_between:1,7|unique:m_school,sch_code',
            'sch_name'       => 'em_length:40',
            'portal_account'       => 'em_length:40', //塾アカウント名
            'cntconvert_kbn' => 'alpha_num_space|max:1',
            'cnttest_kbn'    => 'required|in:0,1,2,3',   // テスト回数表示区分は設計書の備考に合わせて'0'か'1'でバリデーションをかけているが、もし'00'か'01'の定義に変更するなら修正必要
            'testname'       => 'required|em_length:40',
            'admin_kbn'      => 'required|digits:1|in:0,1',
            'psn_hide_flg'   => 'required|digits:1|in:0,1',
            'schname_hide_flg' => 'required|digits:1|in:0,1',
            'psncard_regist_flg' => 'required|digits:1|in:0,1',
            'psn_psw_update_flg' => 'required|digits:1|in:0,1',
            'cls_grade_csv_flg'  => 'required|digits:1|in:0,1',
            'permit_ip'       => ['nullable',new IPAddressFormat],
            'password'       => 'required|string|regex:/^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])[!-~]{12,50}$/|confirmed', //  正規表現（半角英字1文字以上 かつ 半角数字1文字以上）で仮設定、記号追加の場合修正
            'password_confirmation' => 'required'
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
