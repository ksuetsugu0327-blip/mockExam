<?php

namespace App\Services;

use Validator;
use App\Repositories\MUnivCode\MUniversityCodeRepositoryInterface as CodeRepositoryInterface;
use App\Repositories\MUnivCodeHead\MUniversityCodeHeadRepositoryInterface as CodeHeadRepositoryInterface;
use App\Exceptions\ExclusionException;

class UnivCsvService {

    // リポジトリ
    private $mUnivCodeRepository;
    private $mUnivCodeHeadRepository;

    public function __construct(CodeHeadRepositoryInterface $CodeHeadRepository, CodeRepositoryInterface $CodeRepository){
        $this->mUnivCodeHeadRepository = $CodeHeadRepository;
        $this->mUnivCodeRepository = $CodeRepository;
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
            if (count($line) !== count(config('constants.UNIV_CSV_HEADER'))) {
                $csv_errors[] = [$output_line_num, ' カラム数が不正です'];
            } else{

                // 入力値を配列に設定
                $arrayCheckData = array(
                    config('constants.UNIV_CSV_HEADER.UNIV_CODE.NAME')          => $line[config('constants.UNIV_CSV_HEADER.UNIV_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.PREF_CODE.NAME')          => $line[config('constants.UNIV_CSV_HEADER.PREF_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.INSTALL_CODE.NAME')       => $line[config('constants.UNIV_CSV_HEADER.INSTALL_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE.NAME')     => $line[config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.NAME_HKN.NAME')           => $line[config('constants.UNIV_CSV_HEADER.NAME_HKN.INDEX')],
                    config('constants.UNIV_CSV_HEADER.NAME.NAME')               => $line[config('constants.UNIV_CSV_HEADER.NAME.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_CODE.NAME')          => $line[config('constants.UNIV_CSV_HEADER.DEPT_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_SEQ.NAME')           => $line[config('constants.UNIV_CSV_HEADER.DEPT_SEQ.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_NAME.NAME')          => $line[config('constants.UNIV_CSV_HEADER.DEPT_NAME.INDEX')],
                    config('constants.UNIV_CSV_HEADER.SUBJ_CODE.NAME')          => $line[config('constants.UNIV_CSV_HEADER.SUBJ_CODE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.SUBJ_SEQ.NAME')           => $line[config('constants.UNIV_CSV_HEADER.SUBJ_SEQ.INDEX')],
                    config('constants.UNIV_CSV_HEADER.SUBJ_NAME.NAME')          => $line[config('constants.UNIV_CSV_HEADER.SUBJ_NAME.INDEX')],
                    config('constants.UNIV_CSV_HEADER.SEARCH_SUBJ_NAME.NAME')   => $line[config('constants.UNIV_CSV_HEADER.SEARCH_SUBJ_NAME.INDEX')],
                    config('constants.UNIV_CSV_HEADER.CMN_TEST_USE.NAME')       => $line[config('constants.UNIV_CSV_HEADER.CMN_TEST_USE.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEVI_VAL.NAME')           => $line[config('constants.UNIV_CSV_HEADER.DEVI_VAL.INDEX')],
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
                    config('constants.UNIV_CSV_HEADER.UNIV_CODE.NAME') => $line[config('constants.UNIV_CSV_HEADER.UNIV_CODE.INDEX')],
                ];

                //キー重複
                $csv_keyAry[] = $csv_key;
                // 配列の数と重複を削除した配列の個数が一致してきること
                if(count($csv_keyAry) != count(array_unique($csv_keyAry,SORT_REGULAR))){
                    $csv_keyAry = array_unique($csv_keyAry,SORT_REGULAR);
                    $csv_errors[] = [
                        'line' => $output_line_num,
                        'message' => trans('message.CSV_MUNIVCODE_DUPLICATE_DATA',["univ_code"
                                                                                    =>$csv_key[config('constants.UNIV_CSV_HEADER.UNIV_CODE.NAME')]]),
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
            config('constants.UNIV_CSV_HEADER.UNIV_CODE.NAME')          =>
                'bail|required|digits_between:1,4|max:4',

            config('constants.UNIV_CSV_HEADER.PREF_CODE.NAME')         =>
                'bail|required|digits_between:1,4|max:4',

            config('constants.UNIV_CSV_HEADER.INSTALL_CODE.NAME')       =>
                'bail|required|string|max:1',

            config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE.NAME')     => 
                'bail|required|string|max:1',
            
            config('constants.UNIV_CSV_HEADER.NAME_HKN.NAME')           => 
                'bail|required|string|max:100|half_kana',
            
            config('constants.UNIV_CSV_HEADER.NAME.NAME')               => 
                'bail|required|string|max:50',
            
            config('constants.UNIV_CSV_HEADER.DEPT_CODE.NAME')          => 
                'bail|required|digits_between:1,4|max:4',
            
            config('constants.UNIV_CSV_HEADER.DEPT_SEQ.NAME')           => 
                'bail|integer',
            
            config('constants.UNIV_CSV_HEADER.DEPT_NAME.NAME')          => 
                'bail|required|string|max:50',
            
            config('constants.UNIV_CSV_HEADER.SUBJ_CODE.NAME')          => 
                'bail|digits_between:1,4|max:4',
            
            config('constants.UNIV_CSV_HEADER.SUBJ_SEQ.NAME')           => 
                'bail|integer',
            
            config('constants.UNIV_CSV_HEADER.SUBJ_NAME.NAME')          => 
                'bail|string|max:80',
            
            config('constants.UNIV_CSV_HEADER.SEARCH_SUBJ_NAME.NAME')   => 
                'bail|string|max:80',
            
            config('constants.UNIV_CSV_HEADER.CMN_TEST_USE.NAME')       => 
                'bail|required|integer|min:0|max:255',
            
            config('constants.UNIV_CSV_HEADER.DEVI_VAL.NAME')           => 
                'bail|required|numeric|decimal|between:30,80',
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
            'integer'        => trans('validation.integer'),
            'string'         => trans('validation.string'),
            'digits_between' => trans('validation.digits_between'),
            'half_kana'      => trans('validation.half_kana'),
            'decimal'        => trans('validation.decimal'),

            // config('constants.UNIV_CSV_HEADER.PSN_CODE.NAME').'.unique'
            //     => trans('validation.unique').$value[config('constants.UNIV_CSV_HEADER.PSN_CODE.NAME')],
        ];
        return $messages;
    }

    /**
     * 登録処理
     * @param array $personAry 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray($univCodeHeadID, array $univCodeArr){
        return $this->mUnivCodeRepository->registerArray($univCodeHeadID, $univCodeArr);
    }

    public function deleteUnivCodesWithHeadID($univCodeHeadID){
        return $this->mUnivCodeRepository->deleteCodes($univCodeHeadID);
    }

    public function getUnivCodeHeader($dataNameCode){
        return $this->mUnivCodeHeadRepository->getCodeHeader($dataNameCode);
    }

    public function createOrUpdateUnivCodeHead(array $codeHeadArray, $mode){
        try{
            return $this->mUnivCodeHeadRepository->createOrUpdateCodeHead($codeHeadArray, $mode);
        }
        catch (ExclusionException $e){
            throw new ExclusionException($e->getMessage());
        }
    }
}
