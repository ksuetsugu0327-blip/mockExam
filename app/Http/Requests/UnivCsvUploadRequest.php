<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnivCsvUploadRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = [
            'csv_file' => 'required|file|mimes:csv,txt|mimetypes:text/plain', // mimesの都合上text/csvなのでtxtも許可が必要
            'data_type' => 'required',
        ];
//dd($this->input('data_type'),config('selectlistarray.CODE_DATA.UNIV_CODE.VALUE'));
        // Conditionally add validation rules based on the value of data_type.
        if ($this->input('data_type') == config('selectlistarray.CODE_DATA.UNIV_CODE.VALUE')) {
            $rules['fiscal_year'] = 'required';
            $rules['university_code_name_code'] = 'required';
            $rules['dept_subject_div'] = 'required';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() {
        $messages = [
            'data_type.required' => 'データ区分を選択してください。',
            'csv_file.required' => 'ファイルを選択してください。',
            'csv_file.file' => 'ファイルアップロードに失敗しました。',
            'csv_file.mimes' => 'ファイル拡張子が異なります。',
            'csv_file.mimetypes' => 'ファイル形式が不正です。',
        ];

        if ($this->input('data_type') == config('selectlistarray.CODE_DATA.UNIV_CODE.VALUE')) {
            $messages['fiscal_year.required'] = '年度を選択してください。';
            $messages['university_code_name_code.required'] = 'データ名を選択してください。';
            $messages['dept_subject_div.required'] = '学部/学科を選択してください。';
        }

        return $messages;
    }

    /**
     * バリデーション結果を判定し、エラーメッセージを追加する
     * @param Validator $validator
     */
    public function withValidator($validator) {

        $validator->after(function($validator) {
            //dd($validator);
            if (count($validator->failed()) > 0) {
                $validator->errors()->add('message', trans('message.INPUT_ERROR_MESSAGE'));
            }
        });
    }
}
