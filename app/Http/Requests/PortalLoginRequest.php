<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortalLoginRequest extends FormRequest
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
            'portal_account' => 'required|string|exists:m_school,portal_account',
        ];
    }

    public function messages()
    {
        return [
            'portal_account.required' => '塾アカウント名を入力してください。',
            'portal_account.exists' => '塾アカウント名が存在しません',
        ];
    }
}
