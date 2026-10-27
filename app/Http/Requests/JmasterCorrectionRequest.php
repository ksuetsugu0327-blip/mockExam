<?php

namespace App\Http\Requests;

use App\Rules\IPAddressFormat;
use Illuminate\Foundation\Http\FormRequest;

class JmasterCorrectionRequest extends FormRequest
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
            'sch_name' => 'em_length:40',
            'cntconvert_kbn' => 'alpha_num_space|max:1',
            'cnttest_kbn' => 'required|in:0,1,2,3',
            'testname' => 'required|em_length:40',
            'admin_kbn' => 'required|digits:1|in:0,1',
            'psn_hide_flg' => 'required|digits:1|in:0,1',
            'schname_hide_flg' => 'required|digits:1|in:0,1',
            'psncard_regist_flg' => 'required|digits:1|in:0,1',
            'psn_psw_update_flg' => 'required|digits:1|in:0,1',
            'cls_grade_csv_flg' => 'required|digits:1|in:0,1',
            'psn_report_publish_flg' => 'required',
            'permit_ip' => ['nullable', new IPAddressFormat],
        ];
    }

    /**
     * validatorインスタンスの設定
     */
    public function withValidator(Validator $validator)
    {

        // パスワードのチェック(チェックボックスがONの場合のみチェックを実施)
        $validator->sometimes('password', 'required|only_alpha_num|between:8,50|confirmed', function ($input) {
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
