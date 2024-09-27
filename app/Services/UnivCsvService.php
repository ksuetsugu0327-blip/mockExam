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
    public function validateCsvData($file,$deptOrSubject) {
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
            // CSVの文字コードがシステム文字コードと一致しない場合、エラーを返す。
            $systemCharCode = config('constants.CHARACTER_CODE');
            if(!mb_check_encoding($line, $systemCharCode)){
                $csv_errors[] = [
                    'charCodeErr' => true,
                    'message' => trans('message.UNVC_CODE_CSV_CHAR_CODE_ERR', ['system_char_code' => $systemCharCode]),
                ];

                return $csv_errors;
            }

            if($systemCharCode==="SJIS" && mb_check_encoding($line, 'SJIS')){
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
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE1.NAME')     => $line[config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE1.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE2.NAME')     => $line[config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE2.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE3.NAME')     => $line[config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE3.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE4.NAME')     => $line[config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE4.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE5.NAME')     => $line[config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE5.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE6.NAME')     => $line[config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE6.INDEX')],
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE7.NAME')     => $line[config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE7.INDEX')],

                    config('constants.UNIV_CSV_HEADER.NAME_HKN.NAME')           => $line[config('constants.UNIV_CSV_HEADER.NAME_HKN.INDEX')],
                    config('constants.UNIV_CSV_HEADER.NAME.NAME')               => $line[config('constants.UNIV_CSV_HEADER.NAME.INDEX')],
                    config('constants.UNIV_CSV_HEADER.NAME_NEW.NAME')           => $line[config('constants.UNIV_CSV_HEADER.NAME_NEW.INDEX')],
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
                $rules = $this->makeCsvValidationRules($arrayCheckData,$deptOrSubject);

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

            }
        }

        // データ件数チェック
        if($dataCnt === 0){
            // データ件数が0件のエラー
            $csv_errors[] = [
                'line' => '' ,
                'message' => trans('message.CSV_MUNIV_NOT_DATA'),
            ];
        }
        return $csv_errors;
    }

    /**
     * CSVファイル内のバリデーションルール作成
     *
     * @return $rules バリデーションルールの配列
     */
    private function makeCsvValidationRules($value,$deptOrSubject) {
        $rules = []; //$rulesという箱を作る

        if ($deptOrSubject == 1){
                // 名前で指定
                // bailをつけることで、複数のバリデーションエラー出力を止める
                $rules = [
                    config('constants.UNIV_CSV_HEADER.UNIV_CODE.NAME')          =>  //大学コード
                        'bail|required|string|size:4',
                    config('constants.UNIV_CSV_HEADER.PREF_CODE.NAME')         =>   //都道府県コード
                        'bail|required|exists:m_prefecture,pref_code',

                    config('constants.UNIV_CSV_HEADER.INSTALL_CODE.NAME')       =>  //設置区分コード
                        'bail|required|exists:m_university_install,install_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE1.NAME')     => //学部系統コード1
                        'bail|required|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE2.NAME')     => //学部系統コード2
                        'bail|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE3.NAME')     => //学部系統コード3
                        'bail|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE4.NAME')     => //学部系統コード4
                        'bail|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE5.NAME')     => //学部系統コード5
                        'bail|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE6.NAME')     => //学部系統コード6
                        'bail|exists:m_university_dept_line,dept_line_cd',                 
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE7.NAME')     => //学部系統コード7
                        'bail|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.NAME_HKN.NAME')           =>  //大学名（半角カナ）
                        'bail|required|string|max:80|half_kana',
                    
                    config('constants.UNIV_CSV_HEADER.NAME.NAME')               =>  //大学名
                        'bail|required|string|max:40',
                    
                    config('constants.UNIV_CSV_HEADER.NAME_NEW.NAME')           =>  //大学名（新字）
                        'bail|required|string|max:40',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_CODE.NAME')          =>  //学部コード
                        'bail|required|string|size:4',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_SEQ.NAME')           =>  //学部表示順
                        'bail|digits_between:1,9',

                    config('constants.UNIV_CSV_HEADER.DEPT_NAME.NAME')          =>  //学部名
                        'bail|required|string|max:40',

                    config('constants.UNIV_CSV_HEADER.CMN_TEST_USE.NAME')       =>  //共通テスト利用
                        'bail|required|in:0,1',

                    config('constants.UNIV_CSV_HEADER.DEVI_VAL.NAME')           =>  //偏差値
                        'bail|required|decimal|numeric|between:30,80', 

                ];
        }else{
                $rules = [
                    config('constants.UNIV_CSV_HEADER.UNIV_CODE.NAME')          =>  //大学コード
                        'bail|required|string|size:4',
                    config('constants.UNIV_CSV_HEADER.PREF_CODE.NAME')         =>   //都道府県コード
                        'bail|required|exists:m_prefecture,pref_code',

                    config('constants.UNIV_CSV_HEADER.INSTALL_CODE.NAME')       =>  //設置区分コード
                        'bail|required|exists:m_university_install,install_cd',

                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE1.NAME')     => //学部系統コード1
                        'bail|required|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE2.NAME')     => //学部系統コード2
                        'bail|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE3.NAME')     => //学部系統コード3
                        'bail|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE4.NAME')     => //学部系統コード4
                        'bail|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE5.NAME')     => //学部系統コード5
                        'bail|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE6.NAME')     => //学部系統コード6
                        'bail|exists:m_university_dept_line,dept_line_cd',                 
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_LINE_CODE7.NAME')     => //学部系統コード7
                        'bail|exists:m_university_dept_line,dept_line_cd',
                    
                    config('constants.UNIV_CSV_HEADER.NAME_HKN.NAME')           =>  //大学名（半角カナ）
                        'bail|required|string|max:80|half_kana',
                    
                    config('constants.UNIV_CSV_HEADER.NAME.NAME')               =>  //大学名
                        'bail|required|string|max:40',
                    
                    config('constants.UNIV_CSV_HEADER.NAME_NEW.NAME')           =>  //大学名（新字）
                        'bail|required|string|max:40',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_CODE.NAME')          =>  //学部コード
                        'bail|required|string|size:4',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_SEQ.NAME')           =>  //学部表示順
                        'bail|digits_between:1,9',
                    
                    config('constants.UNIV_CSV_HEADER.DEPT_NAME.NAME')          =>  //学部名
                        'bail|required|string|max:40',
                    
                    config('constants.UNIV_CSV_HEADER.SUBJ_CODE.NAME')          =>  //学科コード
                        'bail|required|string|size:4',
                    
                    config('constants.UNIV_CSV_HEADER.SUBJ_SEQ.NAME')           =>  //学科表示順
                        'bail|required|digits_between:1,9',
                    
                    config('constants.UNIV_CSV_HEADER.SUBJ_NAME.NAME')          =>  //学科名
                        'bail|required|string|max:60',
                    
                    config('constants.UNIV_CSV_HEADER.SEARCH_SUBJ_NAME.NAME')   =>  //検索用学科名
                        'bail|required|max:60',
                    
                    config('constants.UNIV_CSV_HEADER.CMN_TEST_USE.NAME')       =>  //共通テスト利用
                        'bail|required|in:0,1',
                    
                    config('constants.UNIV_CSV_HEADER.DEVI_VAL.NAME')           =>  //偏差値
                        'bail|required|decimal|numeric|between:30,80', 
        
                ];
        }
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
            'numeric'        => trans('validation.numeric'),
        ];
        return $messages;
    }

    /**
     * 登録処理
     * @param array $personAry 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray($univCodeHeadID, array $univCode){
        return $this->mUnivCodeRepository->registerArray($univCodeHeadID, $univCode);
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
