<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonResetPasswordRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 入力チェックルールの設定
     */
    public function rules(): array
    {
        return [
            'email' => 'bail|required|string|email|max:255|unique:users',
            'email_confirmation' => 'required|string|email|max:255|unique:users',
        ];
    }
}
