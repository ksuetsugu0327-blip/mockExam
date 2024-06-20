<?php

namespace App\Services;

use Validator;
use App\Rules\EmailFormat;
use App\Repositories\MSchool\MSchoolRepositoryInterface;
use App\Repositories\MPerson\MPersonRepositoryInterface as MPersonRepositoryInterface;

class CsvService {

    // リポジトリ
    private $mPersonRepository;
    private $mschoolRepository;

    public function __construct(MPersonRepositoryInterface $repository, MSchoolRepositoryInterface $mSchoolRepository){
        $this->mPersonRepository = $repository;
        $this->mSchoolRepository = $mSchoolRepository;
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
            //dd(count($line),count(config('constants.CSV_HEADER_NUM')));
            if (count($line) !== count(config('constants.CSV_HEADER_NUM'))) {
                $csv_errors[] = [$output_line_num, ' カラム数が不正です'];
            } else{
                // 入力値を配列に設定
                $arrayCheckData = array(
                    config('constants.CSV_HEADER_NUM.SCH_CODE.NAME')     => $line[config('constants.CSV_HEADER_NUM.SCH_CODE.INDEX')],
                    config('constants.CSV_HEADER_NUM.PSN_CODE.NAME')     => $line[config('constants.CSV_HEADER_NUM.PSN_CODE.INDEX')],
                    config('constants.CSV_HEADER_NUM.CLS_CODE.NAME')     => $line[config('constants.CSV_HEADER_NUM.CLS_CODE.INDEX')],
                    config('constants.CSV_HEADER_NUM.NAME.NAME')         => $line[config('constants.CSV_HEADER_NUM.NAME.INDEX')],
                    config('constants.CSV_HEADER_NUM.BIRTHLOW.NAME')     => $line[config('constants.CSV_HEADER_NUM.BIRTHLOW.INDEX')],
                    config('constants.CSV_HEADER_NUM.POSTALLLOW.NAME')   => $line[config('constants.CSV_HEADER_NUM.POSTALLLOW.INDEX')],
                    config('constants.CSV_HEADER_NUM.TELLOW.NAME')       => $line[config('constants.CSV_HEADER_NUM.TELLOW.INDEX')],
                    config('constants.CSV_HEADER_NUM.MAIL_ADDRESS.NAME') => $line[config('constants.CSV_HEADER_NUM.MAIL_ADDRESS.INDEX')],
                    config('constants.CSV_HEADER_NUM.PASSWORD.NAME')     => $line[config('constants.CSV_HEADER_NUM.PASSWORD.INDEX')],
                    config('constants.CSV_HEADER_NUM.TEMPPASS.NAME')     => $line[config('constants.CSV_HEADER_NUM.TEMPPASS.INDEX')]
                );

                // 塾の存在チェックとパスワード変更可能チェック
                $psn_psw_chg_flg = $this->changePasswordAllowed($arrayCheckData[config('constants.CSV_HEADER_NUM.SCH_CODE.NAME')]);
                if ($psn_psw_chg_flg == -1){
                    $csv_errors[] = array(
                        'line' => $output_line_num,
                        'message' => "指定された塾は存在しません。"
                    );
                    continue;
                }

                if(!empty($arrayCheckData[config('constants.CSV_HEADER_NUM.MAIL_ADDRESS.NAME')]) && 
                    !$psn_psw_chg_flg){
                        $csv_errors[] = array(
                            'line' => $output_line_num,
                            'message' => "生徒パスワード変更不可の塾ではメールアドレスは入力できません。"
                        );
                        continue;
                }

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
                    'sch_code' => $line[config('constants.CSV_HEADER_NUM.SCH_CODE.INDEX')],
                    'psn_code' => $line[config('constants.CSV_HEADER_NUM.PSN_CODE.INDEX')],
                ];

                //キー重複
                $csv_keyAry[] = $csv_key;
                // 配列の数と重複を削除した配列の個数が一致してきること
                if(count($csv_keyAry) != count(array_unique($csv_keyAry,SORT_REGULAR))){
                    $csv_keyAry = array_unique($csv_keyAry,SORT_REGULAR);
                    $csv_errors[] = [
                        'line' => $output_line_num,
                        'message' => trans('message.CSV_MPERSON_DUPLICATE_DATA',['sch_code'=>$csv_key['sch_code'],'psn_code'=>$csv_key['psn_code']]),
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
            config('constants.CSV_HEADER_NUM.SCH_CODE.NAME')   =>
                'bail|required|digits_between:1,7|max:7',

            config('constants.CSV_HEADER_NUM.CLS_CODE.NAME')   =>
                'bail|required|digits_between:1,4|max:4|exists:m_class,cls_code,sch_code,'.$value[config('constants.CSV_HEADER_NUM.SCH_CODE.NAME')],

//            config('constants.CSV_HEADER_NUM.PSN_CODE.NAME')   =>
//                'bail|required|only_alpha_num|between:1,11|max:11|unique:m_person,psn_code,sch_code',
            config('constants.CSV_HEADER_NUM.PSN_CODE.NAME')   =>
                'bail|required|only_alpha_num|between:1,11|max:11',

            config('constants.CSV_HEADER_NUM.NAME.NAME')         => 'em_length:20',
            config('constants.CSV_HEADER_NUM.BIRTHLOW.NAME')     => 'bail|birth_low4',
            config('constants.CSV_HEADER_NUM.POSTALLLOW.NAME')   => 'bail|postal_low4',
            config('constants.CSV_HEADER_NUM.TELLOW.NAME')       => 'bail|tel_low4',
            config('constants.CSV_HEADER_NUM.PASSWORD.NAME')     => 'bail|nullable|string|regex:/^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])[!-~]{12,50}$/',
            config('constants.CSV_HEADER_NUM.MAIL_ADDRESS.NAME') =>  ['bail', new EmailFormat()]
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
            'in'            => trans('validation.in'),
            'between'       => trans('validation.between'),
            'exists'        => trans('validation.exists'),
            'max'           => trans('validation.max'),
            'em_length'     => trans('validation.em_length'),
            'birth_low4'    => trans('validation.birth_low4'),
            'postal_low4'   => trans('validation.postal_low4'),
            'tel_low4'      => trans('validation.tel_low4'),

            config('constants.CSV_HEADER_NUM.PSN_CODE.NAME').'.unique'
                => trans('validation.unique').$value[config('constants.CSV_HEADER_NUM.PSN_CODE.NAME')],
        ];
        return $messages;
    }

    /**
     * 登録処理
     * @param array $personAry 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray(array $person){
        return $this->mPersonRepository->importFromCsv($person);
    }
    public function isNewMPerson($sch_code, $cls_code, $psn_code){
        return 0 == $this->mPersonRepository->countMPerson($sch_code, $cls_code, $psn_code);
    }
    private function changePasswordAllowed($sch_code){
        $school = $this->mSchoolRepository->selectOneByKey($sch_code)->first();
        return is_null($school)? -1: $school->psn_psw_update_flg;
    }
}
