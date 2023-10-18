<?php

namespace App\Services;

use Validator;
use App\Repositories\MClass\MClassRepositoryInterface as MClassRepositoryInterface;

class ClsCsvService {

    // リポジトリ
    private $mClassRepository;

    public function __construct(MClassRepositoryInterface $repository){
        $this->mClassRepository = $repository;
    }

    /**
     * CSVファイル内のバリデーション
     *
     * @param $file readしたCSVファイル
     *
     * @return array $csv_errors エラーメッセージの配列
     */
    public function validateCsvData($file) {
        // CSVファイルがヘッダー行を除いて最大行以上の場合はエラー
        $file->seek($file->getSize());

        //errorリスト
        $csv_errors = array();
        // csvデータ重複チェックは配列
        $csv_keyAry = array();

        // データ件数カウント
        $dataCnt = 0;

        // CSVファイル内のバリデーション
        foreach ($file as $line_num => $line) {
            if (0 === $line_num || 1 === count($line)) {
                // 最初の行または空行など余分な空白がCSVの途中に混ざっている場合は無視
                continue;
            }

            // データ件数カウントアップ
            $dataCnt++;
            $output_line_num = $line_num + 1;
            if (count($line) !== count(config('constants.CLS_CSV_HEADER_NUM'))) {
                $line_count = count($line);
                $csv_errors[] = [$output_line_num, ' カラム数が不正です'];
            } else{
                // 入力値を配列に設定
                $arrayCheckData = array(
                    config('constants.CLS_CSV_HEADER_NUM.SCH_CODE.NAME')   => $line[config('constants.CLS_CSV_HEADER_NUM.SCH_CODE.INDEX')],
                    config('constants.CLS_CSV_HEADER_NUM.CLS_CODE.NAME')   => $line[config('constants.CLS_CSV_HEADER_NUM.CLS_CODE.INDEX')],
                    config('constants.CLS_CSV_HEADER_NUM.CLS_NAME.NAME')   => $line[config('constants.CLS_CSV_HEADER_NUM.CLS_NAME.INDEX')],
                    config('constants.CLS_CSV_HEADER_NUM.PASSWORD.NAME')   => $line[config('constants.CLS_CSV_HEADER_NUM.PASSWORD.INDEX')],
                    config('constants.CLS_CSV_HEADER_NUM.TEMPPASS.NAME')   => $line[config('constants.CLS_CSV_HEADER_NUM.TEMPPASS.INDEX')],
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

                // CSV内重複チェックキー
                $csv_key = [
                    'sch_code' => $line[config('constants.CLS_CSV_HEADER_NUM.SCH_CODE.INDEX')],
                    'cls_code' => $line[config('constants.CLS_CSV_HEADER_NUM.CLS_CODE.INDEX')],
                ];

                //キー重複
                $csv_keyAry[] = $csv_key;
                // 配列の数と重複を削除した配列の個数が一致してきること
                if(count($csv_keyAry) != count(array_unique($csv_keyAry,SORT_REGULAR))){
                    $csv_keyAry = array_unique($csv_keyAry,SORT_REGULAR);
                    $csv_errors[] = [
                        'line' => $output_line_num,
                        'message' => trans('message.CSV_MCLASS_DUPLICATE_DATA',['sch_code'=>$csv_key['sch_code'],'cls_code'=>$csv_key['cls_code']]),
                    ];
                    continue;
                }
            }
        }

        // データ件数チェック
        if($dataCnt === 0){
            // データ件数が0件のエラー
            $csv_errors[] = [
                'line' => '' ,
                'message' => trans('message.CSV_MCLASS_NOT_DATA'),
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
            config('constants.CLS_CSV_HEADER_NUM.SCH_CODE.NAME')   => 'bail|required|digits_between:1,7|max:7',

            config('constants.CLS_CSV_HEADER_NUM.CLS_CODE.NAME')   =>
                'bail|required|digits_between:1,4|max:4'.$value[config('constants.CLS_CSV_HEADER_NUM.SCH_CODE.NAME')],

            config('constants.CLS_CSV_HEADER_NUM.CLS_NAME.NAME')   => 'bail|required|em_length:20',

            //パスワード入力時（nullable）は、半角英数6～50文字
            config('constants.CLS_CSV_HEADER_NUM.PASSWORD.NAME')   => 'nullable|only_alpha_num|between:6,50'
        ];

        return $rules;

    }

    /**
     * CSVファイル内のバリデーションメッセージ作成
     *
     * @param $line_num CSVファイルの該当行
     *
     * @return $messages バリデーションメッセージの配列
     */
    public function makeCsvValidationMessages($value) {
         $messages = [
             'required'      => trans('validation.required'),
             'between'       => trans('validation.between'),
             'max'           => trans('validation.max'),
             'em_length'     => trans('validation.em_length'),
        ];
        return $messages;
    }

    /**
     * 登録処理
     * @param array $classAry 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray(array $classAry){
        return $this->mClassRepository->registerArray($classAry);
    }

    public function isNewMClass($sch_code, $cls_code){
        if(!$this->mClassRepository->countMClass($sch_code, $cls_code)){
            return true;
        }
        return false;
    }
}
