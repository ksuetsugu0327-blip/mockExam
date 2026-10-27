<?php

namespace App\Services;

use App\Repositories\MPerson\MPersonRepositoryInterface;
use App\Repositories\TTicket\TTicketRepositoryInterface;
use Validator;

class ExamNumberCsvService
{
    // リポジトリ
    private $TticketRepository;

    private $MPerson;

    private $register_data = [];

    public function __construct(TTicketRepositoryInterface $repository, MPersonRepositoryInterface $MPerson)
    {
        $this->TticketRepository = $repository;
        $this->MPerson = $MPerson;
    }

    public function getRegisterData()
    {
        if (count($this->register_data) > 0) {
            return $this->register_data;
        }
    }

    /**
     * CSVファイル内のバリデーション
     *
     * @param  $file  readしたCSVファイル
     * @return array $csv_errors エラーメッセージの配列
     */
    public function validateCsvData($file): array
    {
        // CSVファイルがヘッダー行を除いて最大行以上の場合はエラー
        $file->seek($file->getSize());

        // errorリスト
        $csv_errors = [];

        // csvデータ重複チェックは配列
        $csv_keyAry = [];

        // 存在しない塾コード＋塾生コードのチェック用配列
        $bad_data = [];

        // データ件数カウント
        $dataCnt = 0;

        // CSVファイル内のバリデーション
        foreach ($file as $line_num => $line) {

            if ($line_num === 0 || count($line) === 1) {
                // 最初の行または空行など余分な空白がCSVの途中に混ざっている場合は無視
                continue;
            }

            // データ件数カウントアップ
            $dataCnt++;
            $output_line_num = $line_num + 1;

            if (count($line) !== count(config('constants.EXAM_NUM_CSV_HEADER_NUM'))) {

                $csv_errors[] = [$output_line_num, ' カラム数が不正です'];

            } else {

                // 必須項目等バリデーション実行
                if ($this->validateExamData($csv_errors, $line, $output_line_num)) {
                    continue;
                }

                // CSVファイル内重複チェックキー
                if ($this->checkDuplicates($csv_errors, $csv_keyAry, $line, $output_line_num)) {
                    continue;
                }

                // 塾コード＋生徒コードの存在チェックと登録データの生成

                if ($this->checkPersonData($csv_errors, $bad_data, $line, $output_line_num)) {
                    continue;
                }
            }
        }

        // データ件数チェック
        if ($dataCnt === 0) {
            // データ件数が0件のエラー
            $csv_errors[] = [
                'line' => '',
                'message' => trans('message.CSV_TICKET_NOT_DATA'),
            ];
        }

        return $csv_errors;
    }

    /**
     * バリデーションの実行
     *
     * @return bool
     */
    private function validateExamData(&$csv_errors, $line, $output_line_num): bool
    {
        // 入力値を配列に設定
        $arrayCheckData = [
            config('constants.EXAM_NUM_CSV_HEADER_NUM.SCH_CODE.NAME') => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.SCH_CODE.INDEX')],
            config('constants.EXAM_NUM_CSV_HEADER_NUM.PSN_CODE.NAME') => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.PSN_CODE.INDEX')],
            config('constants.EXAM_NUM_CSV_HEADER_NUM.YEAR.NAME') => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.YEAR.INDEX')],
            config('constants.EXAM_NUM_CSV_HEADER_NUM.CNT.NAME') => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.CNT.INDEX')],
            config('constants.EXAM_NUM_CSV_HEADER_NUM.GRADE.NAME') => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.GRADE.INDEX')],
            config('constants.EXAM_NUM_CSV_HEADER_NUM.EXAM_NUM.NAME') => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.EXAM_NUM.INDEX')],
        ];
        // バリデーションルール生成
        $rules = $this->makeCsvValidationRules($arrayCheckData);

        // 入力値を元にエラーメッセージ生成
        $messages = $this->makeCsvValidationMessages($arrayCheckData);
        // 入力値バリデーション
        $validator = Validator::make($arrayCheckData, $rules, $messages);

        if ($validator->fails()) {
            // エラー行数とエラーメッセージを格納
            $csv_errors[] = [
                'line' => $output_line_num,
                'message' => implode(',', $validator->errors()->all()),
            ];

            return true;
        }
    }

    /**
     * CSVファイル内のバリデーションルール作成
     *
     * @return $rules バリデーションルールの配列
     */
    private function makeCsvValidationRules($value)
    {
        // 受験番号の最大桁数を取得する
        $max_exam = config('constants.EXAM_NUMBER_ZERO_PAD_DIGITS');

        // 名前で指定
        // bailをつけることで、複数のバリデーションエラー出力を止める
        return [
            // 塾コード
            config('constants.EXAM_NUM_CSV_HEADER_NUM.SCH_CODE.NAME') => 'bail|required|digits_between:1,7|max:7',

            // 生徒コード
            config('constants.EXAM_NUM_CSV_HEADER_NUM.PSN_CODE.NAME') => 'bail|required|only_alpha_num|between:1,11|max:11',

            // 年度
            config('constants.EXAM_NUM_CSV_HEADER_NUM.YEAR.NAME') => 'bail|required|digits:4',

            // 回数
            config('constants.EXAM_NUM_CSV_HEADER_NUM.CNT.NAME') => 'bail|required|numeric',

            // 学年
            config('constants.EXAM_NUM_CSV_HEADER_NUM.GRADE.NAME') => 'bail|required|numeric|exam_grade:1,3',

            // 受験番号
            config('constants.EXAM_NUM_CSV_HEADER_NUM.EXAM_NUM.NAME') => 'required|digits_between:1,'.$max_exam,

        ];

    }

    /**
     * CSVファイル内のバリデーションメッセージ作成
     *
     * @param  $line_num  CSVファイルの該当行
     * @return $messages バリデーションメッセージの配列
     */
    public function makeCsvValidationMessages($value)
    {
        $messages = [
            'required' => trans('validation.required'),
            'between' => trans('validation.between'),
            'max' => trans('validation.max'),
            'em_length' => trans('validation.em_length'),
            'exam_grade' => trans('validation.exam_grade'),
        ];

        return $messages;
    }

    /**
     * CSVファイルの重複チェック
     * 塾コード・生徒コード・年度・回数・学年 が重複しているデータをチェックする
     *
     * @return bool
     */
    private function checkDuplicates(&$csv_errors, &$csv_keyAry, $line, $output_line_num): bool
    {
        $csv_key = [
            'sch_code' => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.SCH_CODE.INDEX')],
            'psn_code' => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.PSN_CODE.INDEX')],
            'year' => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.YEAR.INDEX')],
            'cnttest' => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.CNT.INDEX')],
            'grade' => $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.GRADE.INDEX')],
        ];

        $keys = implode('-', $csv_key);
        if (isset($csv_keyAry[$keys])) {
            $csv_errors[] = [
                'line' => $output_line_num,
                'message' => trans('message.CSV_TICKET_DUPLICATE_DATA',
                    [
                        'sch_code' => $csv_key['sch_code'],
                        'psn_code' => $csv_key['psn_code'],
                        'year' => $csv_key['year'],
                        'cnttest' => $csv_key['cnttest'],
                        'grade' => $csv_key['grade'],
                    ]),
            ];

            return true;
        } else {
            $csv_keyAry[$keys] = 1;
        }
    }

    private function checkPersonData(&$csv_errors, &$bad_data, $line, $output_line_num)
    {
        $sch_code = $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.SCH_CODE.INDEX')];
        $psn_code = $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.PSN_CODE.INDEX')];
        // 塾コード＋生徒コードの存在をチェック（チェック用配列と比較）
        if (isset($bad_data[$psn_code]) && $bad_data[$psn_code] == $sch_code) {
            $csv_errors[] = [
                'line' => $output_line_num,
                'message' => trans('message.CSV_TICKET_PERSON_NO_EXISTS',
                    [
                        'sch_code' => $sch_code,
                        'psn_code' => $psn_code,
                    ]),
            ];

            return true;
        }
        // m_personの塾コード＋生徒コードデータをチェック
        $person_data = $this->MPerson->selectOneByKey($sch_code, $psn_code);
        // 存在しない場合はエラー
        if ($person_data->count() == 0) {
            $bad_data[$psn_code] = $sch_code;
            $csv_errors[] = [
                'line' => $output_line_num,
                'message' => trans('message.CSV_TICKET_PERSON_NO_EXISTS',
                    [
                        'sch_code' => $sch_code,
                        'psn_code' => $psn_code,
                    ]),
            ];

            return true;
        } else {
            $this->cnvExamData($line, $person_data);
        }
    }

    public function cnvExamData($line, $person_data)
    {
        if (empty($line[config('constants.EXAM_NUM_CSV_HEADER_NUM.EXAM_NUM.INDEX')])) {
            $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.EXAM_NUM.INDEX')] = 0;
        }
        $this->register_data[] = $person_data->get(0)['id'].','.
            $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.YEAR.INDEX')].','.
            $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.CNT.INDEX')].','.
            $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.GRADE.INDEX')].','.
            $line[config('constants.EXAM_NUM_CSV_HEADER_NUM.EXAM_NUM.INDEX')].','.
            'NOW(),'.
            'NOW()';

    }

    /**
     * 登録処理
     *
     * @param  array  $import_data  試験一覧用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray(array $import_data)
    {
        return $this->TticketRepository->registerArray($import_data);
    }
}
