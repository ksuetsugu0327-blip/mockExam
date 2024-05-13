<?php

namespace App\Services;

use Validator;
use App\Repositories\MPerson\MPersonRepositoryInterface as MPersonRepositoryInterface;

class UnivCsvService {

    // リポジトリ
    private $mPersonRepository;

    public function __construct(MPersonRepositoryInterface $repository){
        $this->mPersonRepository = $repository;
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
            if (count($line) !== count(config('constants.UNIV_CSV_HEADER'))) {
                $csv_errors[] = [$output_line, ' カラム数が不正です'];
            } else{
                // 入力値を配列に設定
                $arrayCheckData = array(
                    config('constants.UNIV_CSV_HEADER.UNIV_CODE.NAME')   => $line[config('constants.CSV_HEADER.UNIV_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.PREF_CODE.NAME')   => $line[config('constants.UNIV_CSV_HEADER.PREF_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.INSTALL_CODE.NAME')   => $line[config('constants.UNIV_CSV_HEADER.INSTALL_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE.NAME')       => $line[config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.NAME_HKN.NAME')   => $line[config('constants.UNIV_CSV_HEADER.NAME_HKN.INDEX')],
                    config('constants.UNIV_CSV_HEADER.NAME.NAME') => $line[config('constants.UNIV_CSV_HEADER.NAME.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_CODE.NAME')     => $line[config('constants.UNIV_CSV_HEADER.DEPT_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_SEQ.NAME')   => $line[config('constants.UNIV_CSV_HEADER.DEPT_SEQ.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_NAME.NAME')   => $line[config('constants.UNIV_CSV_HEADER.DEPT_NAME.INDEX')],
                    config('constants.UNIV_CSV_HEADER.SUBJ_CODE.NAME')     => $line[config('constants.UNIV_CSV_HEADER.SUBJ_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.SUBJ_SEQ.NAME')   => $line[config('constants.UNIV_CSV_HEADER.SUBJ_SEQ.INDEX')],
                    config('constants.UNIV_CSV_HEADER.SUBJ_NAME.NAME')   => $line[config('constants.UNIV_CSV_HEADER.SUBJ_NAME.INDEX')],
                    config('constants.UNIV_CSV_HEADER.SEARCH_SUBJ_NAME.NAME')     => $line[config('constants.UNIV_CSV_HEADER.SEARCH_SUBJ_NAME.INDEX')],
                    config('constants.UNIV_CSV_HEADER.CMN_TEST_USE.NAME')   => $line[config('constants.UNIV_CSV_HEADER.CMN_TEST_USE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEVI_VAL.NAME')   => $line[config('constants.UNIV_CSV_HEADER.DEVI_VAL.INDEX')],
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
                        'line' => $output_line,
                        'message' => implode(',', $validator->errors()->all())
                    );
                    continue;
                }

                // CSV内重複チェックキー
                $csv_key = [
                    'sch_code' => $line[config('constants.UNIV_CSV_HEADER.UNIV_CODE.INDEX')],
                    'psn_code' => $line[config('constants.UNIV_CSV_HEADER.PREF_CODE.INDEX')],
                ];

                //キー重複
                $csv_keyAry[] = $csv_key;
                // 配列の数と重複を削除した配列の個数が一致してきること
                if(count($csv_keyAry) != count(array_unique($csv_keyAry,SORT_REGULAR))){
                    $csv_keyAry = array_unique($csv_keyAry,SORT_REGULAR);
                    $csv_errors[] = [
                        'line' => $output_line,
                        'message' => trans('message.CSV_MPERSON_DUPLICATE_DATA',[config('constants.UNIV_CSV_HEADER.UNIV_CODE.NAME')=>$csv_key[config('constants.UNIV_CSV_HEADER.UNIV_CODE.NAME')]
                                                                                    ,config('constants.UNIV_CSV_HEADER.PREF_CODE.NAME')=>$csv_key[config('constants.UNIV_CSV_HEADER.PREF_CODE.NAME')]]),
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
            config('constants.UNIV_CSV_HEADER.UNIV_CODE.NAME')   =>
                'bail|required|digits_between:1,7|max:7|exists:m_university_code,university_cd',

            config('constants.UNIV_CSV_HEADER.PREF_CODE.INDEX')   =>
                'bail|required|digits_between:1,4|max:4',

            config('constants.UNIV_CSV_HEADER.INSTALL_CODE.NAME')   =>
                'bail|required|only_alpha|between:1,11|max:11',

            config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE.NAME')       => 'em_length:20',
            config('constants.UNIV_CSV_HEADER.NAME_HKN.NAME')   => 'bail|birth_low4',
            config('constants.UNIV_CSV_HEADER.NAME.NAME') => 'bail|postal_low4',
            config('constants.UNIV_CSV_HEADER.DEPT_CODE.NAME')     => 'bail|tel_low4',
            config('constants.UNIV_CSV_HEADER.DEPT_SEQ.NAME')     => 'bail|tel_low4',
            config('constants.UNIV_CSV_HEADER.DEPT_NAME.NAME')     => 'bail|tel_low4',
            config('constants.UNIV_CSV_HEADER.SUBJ_CODE.NAME')     => 'bail|tel_low4',
            config('constants.UNIV_CSV_HEADER.SUBJ_SEQ.NAME')     => 'bail|tel_low4',
            config('constants.UNIV_CSV_HEADER.SUBJ_NAME.NAME')     => 'bail|tel_low4',
            config('constants.UNIV_CSV_HEADER.SEARCH_SUBJ_NAME.NAME')     => 'bail|tel_low4',
            config('constants.UNIV_CSV_HEADER.CMN_TEST_USE.NAME')     => 'bail|tel_low4',
            config('constants.UNIV_CSV_HEADER.DEVI_VAL.NAME')     => 'bail|tel_low4',
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
            'required'      => trans('validation.required'),
            'in'            => trans('validation.in'),
            'between'       => trans('validation.between'),
            'exists'        => trans('validation.exists'),
            'max'           => trans('validation.max'),
            'em_length'     => trans('validation.em_length'),
            'birth_low4'    => trans('validation.birth_low4'),
            'postal_low4'   => trans('validation.postal_low4'),
            'tel_low4'      => trans('validation.tel_low4'),

            config('constants.UNIV_CSV_HEADER.PSN_CODE.NAME').'.unique'
                => trans('validation.unique').$value[config('constants.UNIV_CSV_HEADER.PSN_CODE.NAME')],
        ];
        return $messages;
    }

    /**
     * 登録処理
     * @param array $personAry 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray(array $personAry){
        return $this->mPersonRepository->registerArray($personAry);
    }
    public function isNewMPerson($sch_code, $cls_code, $psn_code){
        return 0 == $this->mPersonRepository->countMPerson($sch_code, $cls_code, $psn_code);
    }
}
