<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolIDLoginCheckRequest extends FormRequest
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
     * バリデーション前に値の整形をして返す
     *
     * @param  type  $keys
     * @return type
     */
    public function all($keys = null)
    {
        // 値を取得
        $results = parent::all($keys);

        $results['sch_code_org'] = $this->input('sch_code');

        // intにキャスト
        $results['sch_code'] = (int) $this->input('sch_code');
        $results['sch_code_url'] = (int) $this->input('sch_code_url');

        // 整形後の値を返す
        return $results;
    }

    /**
     * 入力チェックルールの設定
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sch_code_org' => 'required|digits_between:1,7',
            'sch_code' => 'required|digits_between:1,7|same:sch_code_url',
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
            'sch_code.same' => trans('message.SCHOOL_CODE_NO_EXISTS'),
            'sch_code_org' => trans('message.SCHOOL_CODE_NO_EXISTS'),
        ];
    }
}
