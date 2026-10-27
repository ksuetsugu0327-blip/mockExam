<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class TicketRegistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $valid = [
            'enable_flag' => 'bail|required|in:0,1',
            'hsc_code' => 'bail|required|string|exists:m_highschool,hsc_code',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $valid += [
                'univ_code'.$i => 'bail|nullable|size:4|exists:m_university,univ_code',
                'faculty_code'.$i => 'bail|nullable|size:4|exists:m_faculty,fcl_code,univ_code,'.$this->input('univ_code'.$i),
                'department_code'.$i => 'bail|nullable|size:4|exists:m_department,dpt_code,fcl_code,'.$this->input('faculty_code'.$i),
            ];
        }

        return $valid;
    }

    /**
     * カスタムメッセージ
     */
    public function messages(): array
    {
        return [
            'enable_flag.required' => trans('message.ILLEGAL_TICKET_REGISTER_DATA'),
            'enable_flag.in' => trans('message.ILLEGAL_TICKET_REGISTER_DATA'),
        ];
    }

    /**
     * バリデーション結果を判定し、エラーメッセージを追加する
     *
     * @param  Validator  $validator
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $univCnt = 0;
            for ($i = 1; $i <= 6; $i++) {
                // 学科までの設定時
                if ($this->input('enable_flag') == '1') {
                    // 大学・学部・学科は必ずセット
                    if ($this->filled(['univ_code'.$i]) || $this->filled(['faculty_code'.$i]) || $this->filled(['department_code'.$i])) {
                        if (! $this->filled(['univ_code'.$i, 'faculty_code'.$i, 'department_code'.$i])) {
                            $validator->errors()->add('ufd_set', trans('validation.ufd_set', ['attribute' => $i]));
                        }
                    }

                    // 学部までの設定時
                } else {
                    // 大学・学部は必ずセット
                    if ($this->filled(['univ_code'.$i]) || $this->filled(['faculty_code'.$i])) {
                        if (! $this->filled(['univ_code'.$i, 'faculty_code'.$i])) {
                            $validator->errors()->add('uf_set', trans('validation.uf_set', ['attribute' => $i]));
                        }
                    }
                }

                // 大学が入力されているかチェック
                if ($this->filled(['univ_code'.$i])) {
                    $univCnt++;
                }
            }

            // // 志望大学１～６すべてに値が入っていない
            // if ($univCnt == 0) {
            //     $validator->errors()->add('univ_no_set', trans('validation.univ_no_set'));
            // }

        });
    }

    /**
     * 後続処理を行うため失敗してもリダイレクトさせない
     */
    protected function failedValidation(Validator $validator)
    {
        // 自動リダイレクト無効
    }

    /**
     * バリデータの取得
     */
    public function getValidator()
    {
        return $this->validator;
    }
}
