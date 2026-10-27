<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UnivTestListRequest extends FormRequest
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
            'year' => 'nullable|string',
            'test_name' => 'nullable|string|max:60',
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
