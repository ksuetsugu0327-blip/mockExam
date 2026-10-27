<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CsvUploadRequest extends FormRequest
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
            'csv_file' => [
                'required',
                'file',
                'mimes:csv,txt', // mimesの都合上text/csvなのでtxtも許可が必要
                'mimetypes:text/plain',
            ], [
                'csv_file.required' => 'ファイルを選択してください。',
                'csv_file.file' => 'ファイルアップロードに失敗しました。',
                'csv_file.mimes' => 'ファイル拡張子が異なります。',
                'csv_file.mimetypes' => 'ファイル形式が不正です。',
            ],
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
