<?php

namespace App\Http\Requests;

use App\Rules\EmailFormat;
use Illuminate\Foundation\Http\FormRequest;

class SmasterCorrectionRequest extends FormRequest
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
            'cls_code' => 'sometimes|required|digits_between:1,4|exists:m_class,cls_code,sch_code,'.$this->input('sch_code'),
            'psn_name' => 'sometimes|em_length:20',
            'birthlow4' => 'sometimes|birth_low4',
            'postallow4' => 'sometimes|postal_low4',
            'tellow4' => 'sometimes|tel_low4',
            'email' => ['sometimes', 'required', new EmailFormat],
        ];
    }

    /**
     * バリデーション結果を判定し、エラーメッセージを追加する
     */
    public function withValidator(Validator $validator)
    {
        // パスワードのチェック(管理者以外の場合、必須設定)
        // (塾ユーザ&&メールアドレスない) || (教室ユーザ&&メールアドレスない) || (管理者 && 代理ログイン&&メールアドレスない)の時必須
        $validator->sometimes('temppasswordReissueCheck', 'required', function () {
            // メールアドレスが存在する場合、必須設定しない
            if (request()->has('mail_address')) {
                return false;
            } else {
                return
                  auth()->guard(config('constants.GUARD_SCHOOL'))->check() ||
                  auth()->guard(config('constants.GUARD_CLASS'))->check() ||
                  (
                      auth()->guard(config('constants.GUARD_ADMIN'))->check() &&
                      session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))
                  );
            }

        });

        $validator->after(function ($validator) {
            if (count($validator->failed()) > 0) {
                $validator->errors()->add('message', trans('message.INPUT_ERROR_MESSAGE'));
            }
        });
    }
}
