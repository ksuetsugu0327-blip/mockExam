<?php

namespace App\Services;

use Validator;
use App\Repositories\MHighSchoolCode\MHighSchoolCodeRepositoryInterface as HighCodeRepositoryInterface;

class HighSchoolCsvService {

    // リポジトリ
    private $mHighCodeRepository;

    public function __construct(HighCodeRepositoryInterface $HighSchoolRepository){
        $this->mHighCodeRepository = $HighSchoolRepository;
    }

    /**
     * CSVファイル内のバリデーション
     *
     * @param $file readしたCSVファイル
     *
     * @return $csv_errors エラーメッセージの配列
     */
    public function validateCsvData($file) {
        // CSVファイルがヘッダー行を除いて最大行以上の場合はエラー
        //$file->seek($file->getSize());

        // errorリスト
        $csv_errors = array();
        // csvデータ重複チェックは配列
        $csv_keyAry = array();

        // データ件数カウント
        $dataCnt = 0;

        // CSVファイル内のバリデーション
        foreach ($file as $line_num => $line) {
            // CSVの文字コードがSJISの場合、UTF-8に変更
            if(mb_check_encoding($line, 'SJIS')){
                $line = mb_convert_encoding($line, 'UTF-8', 'SJIS');
            }
            if (0 === $line_num || 1 === count($line)) {
                // 最初の行または空行など余分な空白がCSVの途中に混ざっている場合は無視
                continue;
            }

            // データ件数カウントアップ
            $dataCnt++;
            $output_line_num = $line_num + 1;
            if (count($line) !== count(config('constants.HIGH_SCHOOL_CSV_HEADER'))) {
                $csv_errors[] = [$output_line_num, ' カラム数が不正です'];
            } else{

                // 入力値を配列に設定
                $arrayCheckData = array(
                    config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_CODE.NAME')       => $line[config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_CODE.INDEX')],
                    config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_NAME_HKN.NAME')   => $line[config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_NAME_HKN.INDEX')],
                    config('constants.HIGH_SCHOOL_CSV_HEADER.ZIP_NUM.NAME')                => $line[config('constants.HIGH_SCHOOL_CSV_HEADER.ZIP_NUM.INDEX')],
                    config('constants.HIGH_SCHOOL_CSV_HEADER.TEL_NUM.NAME')                => $line[config('constants.HIGH_SCHOOL_CSV_HEADER.TEL_NUM.INDEX')],
                    config('constants.HIGH_SCHOOL_CSV_HEADER.INSTALL_CODE.NAME')           => $line[config('constants.HIGH_SCHOOL_CSV_HEADER.INSTALL_CODE.INDEX')],
                    config('constants.HIGH_SCHOOL_CSV_HEADER.CLASS_CODE.NAME')             => $line[config('constants.HIGH_SCHOOL_CSV_HEADER.CLASS_CODE.INDEX')],
                    config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_NAME.NAME')       => $line[config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_NAME.INDEX')],
                    config('constants.HIGH_SCHOOL_CSV_HEADER.ADDRESS.NAME')                => $line[config('constants.HIGH_SCHOOL_CSV_HEADER.ADDRESS.INDEX')],
                    config('constants.HIGH_SCHOOL_CSV_HEADER.PREF_CODE.NAME')              => $line[config('constants.HIGH_SCHOOL_CSV_HEADER.PREF_CODE.INDEX')],
                );
                // バリデーションルール生成
                $rules = $this->makeCsvValidationRules($arrayCheckData);

                // 入力値を元にエラーメッセージ生成
                $messages = $this->makeCsvValidationMessages($arrayCheckData);
                // 入力値バリデーション
                $validator = Validator::make($arrayCheckData, $rules, $messages);

                if($validator->fails()){
                    // エラー行数とエラーメッセージを格納
                    $csv_errors[] = array(
                        'line' => $output_line_num,
                        'message' => implode(',', $validator->errors()->all())
                    );
                    continue;
                }

                // // CSV内重複チェックキー
                // $csv_key = [
                //     config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_CODE.NAME')  => $line[config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_CODE.INDEX')],
                // ];

                // //キー重複
                // $csv_keyAry[] = $csv_key;
                // // 配列の数と重複を削除した配列の個数が一致してきること
                // if(count($csv_keyAry) != count(array_unique($csv_keyAry,SORT_REGULAR))){
                //     $csv_keyAry = array_unique($csv_keyAry,SORT_REGULAR);
                //     $csv_errors[] = [
                //         'line' => $output_line_num,
                //         'message' => trans('message.CSV_MHIGHSCHOOL_DUPLICATE_DATA',[config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_CODE.NAME')
                //                                                                         =>$csv_key[config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_CODE.NAME')]]),
                //     ];
                //     continue;
                // }
            }
        }

        // データ件数チェック
        if($dataCnt === 0){
            // データ件数が0件のエラー
            $csv_errors[] = [
                'line' => '' ,
                'message' => trans('message.CSV_MPERSON_NOT_DATA'),
            ];
        }
        return $csv_errors;
    }

    /**
     * CSVファイル内のバリデーションルール作成
     *
     * @return $rules バリデーションルールの配列
     */
    private function makeCsvValidationRules($value) {
        $rules = []; //$rulesという箱を作る

        // 名前で指定
        // bailをつけることで、複数のバリデーションエラー出力を止める
        $rules = [
            config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_CODE.NAME')            =>
                'bail|required|string|size:6',

            config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_NAME_HKN.NAME')        =>
                'bail|required|string|max:40',

            config('constants.HIGH_SCHOOL_CSV_HEADER.ZIP_NUM.NAME')                     =>
                'bail|nullable|string|size:7',
            
            config('constants.HIGH_SCHOOL_CSV_HEADER.TEL_NUM.NAME')                     => 
                'bail|nullable|string|max:15|tel',
            
            config('constants.HIGH_SCHOOL_CSV_HEADER.INSTALL_CODE.NAME')                => 
                'bail|required|digits:1|exists:m_highschool_install,install_cd2',
            
            config('constants.HIGH_SCHOOL_CSV_HEADER.CLASS_CODE.NAME')                  => 
                'bail|required|digits:1|exists:m_highschool_class,class_cd',
            
            config('constants.HIGH_SCHOOL_CSV_HEADER.HIGH_SCHOOL_NAME.NAME')            => 
                'bail|required|string|em_length:20',
            
            config('constants.HIGH_SCHOOL_CSV_HEADER.ADDRESS.NAME')                     => 
                'bail|nullable|string|em_length:30',
            
            config('constants.HIGH_SCHOOL_CSV_HEADER.PREF_CODE.NAME')                   => 
                'bail|required|digits_between:1,2|max:2|exists:m_prefecture,pref_code',
        ];

        return $rules;
    }

    /**
     * CSVファイル内のバリデーションメッセージ作成
     *
     * @param $line CSVファイルの該当行
     *
     * @return $messages バリデーションメッセージの配列
     */
    public function makeCsvValidationMessages($value) {
         $messages = [
            'required'       => trans('validation.required'),
            'in'             => trans('validation.in'),
            'between'        => trans('validation.between'),
            'exists'         => trans('validation.exists'),
            'max'            => trans('validation.max'),
            'tel'            => trans('validation.tel'),
            'em_length'      => trans('validation.em_length'),
            'integer'        => trans('validation.integer'),
            'string'         => trans('validation.string'),
            'digits_between' => trans('validation.digits_between'),
            'full_space'     => trans('validation.full_space'),
        ];
        return $messages;
    }

    /**
     * 登録処理
     * @param array $personAry 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray(array $highCodeArr){
        return $this->mHighCodeRepository->registerArray($highCodeArr);
    }
}
