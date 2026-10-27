<?php

namespace App\Http\Controllers;

// useしないと 自動的にnamespaceのパスが付与されるのでuse
use App\Exceptions\ExclusionException;
use App\Http\Requests\UnivCsvUploadRequest;
use App\Models\MUniversityCodeName as MUnivCodeName;
use App\Services\HighSchoolCsvService;
use App\Services\MUnivCodeNameService;
use App\Services\TTestInfoService;
use App\Services\UnivCsvService;
use App\Utils\FileUtil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use SplFileObject;

class UnivCsvUploadController extends Controller implements HasMiddleware
{
    private $univCsvService;

    private $highSchoolCsvService;

    private $mUnivCodeNameService;

    private $tTestService;

    public function __construct(
        UnivCsvService $univCsvService,
        HighSchoolCsvService $highSchoolCsvService,
        MUnivCodeNameService $univCodeNameService,
        TTestInfoService $testService
    ) {

        $this->univCsvService = $univCsvService;
        $this->highSchoolCsvService = $highSchoolCsvService;
        $this->mUnivCodeNameService = $univCodeNameService;
        $this->tTestService = $testService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_ADMIN'),
        ];
    }

    /**
     * CSVインポート画面を表示
     */
    public function index(): View
    {

        $codeData = config('selectlistarray.CODE_DATA');
        $years = $this->tTestService->generateYearsArray('near');
        $mUnivCodeName = $this->mUnivCodeNameService->selectList();

        $deptSubjSelect = config('selectlistarray.DEPT_SUBJ_SELECT');

        return view('univ_register', compact(
            'codeData',
            'years',
            'mUnivCodeName',
            'deptSubjSelect'
        ));
    }

    /**
     * CSV アップロード処理
     */
    public function upload(UnivCsvUploadRequest $request): RedirectResponse
    {

        // ロケールを設定(日本語に設定)
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        // アップロードしたファイルを取得
        // 'csv_file' はビューの inputタグのname属性
        $uploaded_file = $request->file('csv_file');
        $dataType = $request->input('data_type');
        $year = $request->input('fiscal_year');
        $univCodeNameCode = $request->input('university_code_name_code');
        $deptOrSubject = $request->input('dept_subject_div');
        $updated_at = $request->input('univ_code_head_updated_at');
        $univCodeHeader = null;

        $param = compact('year', 'univCodeNameCode', 'deptOrSubject', 'updated_at');

        // SplFileObjectを生成
        $file = new SplFileObject($uploaded_file);

        // SplFileObject::READ_CSV が最速らしい
        $file->setFlags(SplFileObject::READ_CSV);

        // CSVの中身に対するバリデーションを実施
        if ($dataType == 1) { // 大学
            $csv_errors = $this->univCsvService->validateCsvData($file, $deptOrSubject);
        } else {
            $csv_errors = $this->highSchoolCsvService->validateCsvData($file);
        }

        // 登録データ格納配列
        $arrayInsertData = [];

        // エラー有無をチェック
        if (count($csv_errors) > 0) {
            foreach ($csv_errors as $errs) {
                // dd($errs);
                if (array_key_exists('charCodeErr', $errs)) {
                    // 画面表示
                    return redirect()->route('univ.register', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])
                        ->withInput()
                        ->with(array_merge([
                            'message' => $errs['message'],
                        ], $this->prepareSelectList()));
                }
            }
            // Set the session message
            session()->put('csv_errors', $csv_errors);

            // Set the session message and additional data
            $errors = new MessageBag([trans('message.FAILURE_MUNIVCODE_REGISTER_DATA'), $dataType]);

            // Redirect to the CSV download route
            return redirect()->route('univ.register', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])
                ->withErrors($errors);
        } else {
            DB::beginTransaction(); // ★ 1つの大枠トランザクションを開始
            // ローカルで実行する場合、タイムアウトしたら下記を適用してください。
            // set_time_limit(5000);
            try {
                if ($dataType == 1) {

                    $univCodeHeader = $this->handleUnivCodeHeader($param);

                    $this->insertUnivCodeData($file, $univCodeHeader, $param);
                } else {
                    $this->highSchoolCsvService->clearTable();
                    $this->insertHighSchoolCodeData($file);
                }
                // 全件正常に完了したらコミット
                DB::commit();
            } catch (ExclusionException $e) {
                DB::rollBack();
                // 楽観ロックエラー時の画面通知処理
                Log::error('CSVインポート失敗(楽観ロックエラー): '.$e->getMessage());

                return redirect()->route('univ.register', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])
                    ->with(array_merge([
                        'message' => $e->getMessage(), // 「他のユーザーによって更新されました」等のメッセージ
                    ], $this->prepareSelectList()));
            } catch (\Exception $e) {
                DB::rollBack(); // ★ 途中でどんなエラーが起きても全データ元通りに復元
                Log::error('CSVインポート失敗(DBエラー): '.$e->getMessage());

                return redirect()->route('univ.register', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])
                    ->with(array_merge([
                        'message' => 'CSVインポート失敗(DBエラー): '.$e->getMessage(),
                    ], $this->prepareSelectList()));
            } catch (ExclusionException $e) {
                // エラー発生時は全ロールバック
                DB::rollBack();

                // エラーを画面に表示
                return redirect()->route('univ.register', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])
                    ->with(array_merge([
                        'message' => $e->getMessage(),
                    ], $this->prepareSelectList()));
            }

        }

        // 画面表示
        return redirect()->route('univ.register', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])
            ->with(array_merge([
                'message' => trans('message.SUCCESS_MUNIVCODE_CSV_DATA'),
            ], $this->prepareSelectList()));
    }

    public function downloadErrCsv($schCode, $mode)
    {
        $fileName = '';
        // if(!isset($sch_code)){
        //     return redirect(route('schoolIdCheck.index',['schCode'=>$schCode]));
        // }

        if ($mode == config('selectlistarray.CODE_DATA.UNIV_CODE.VALUE')) {
            $fileName = config('constants.UNIV_CODE_IMPORT_CSV_ERRFILENAME');

        } elseif ($mode == config('selectlistarray.CODE_DATA.HIGH_SCHOOL_CODE.VALUE')) {
            $fileName = config('constants.HIGH_SCHOOL_CODE_IMPORT_CSV_ERRFILENAME');
        }

        // ヘッダ
        $csvHeader = ['エラー行', 'エラーメッセージ'];

        // HTTP Header 作成
        $responseHeader = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachement;filename='.$fileName,
        ];
        $csv_errors = session()->get('csv_errors', []);

        // エラー内容CSVをダウンロードさせる
        return response()->streamDownload(function () use ($csv_errors, $csvHeader) {
            echo FileUtil::CreateCSV($csv_errors, $csvHeader);
            session()->forget('csv_errors');
        }, $fileName, $responseHeader);
    }

    private function handleUnivCodeHeader(array $param)
    {
        $univCodeHeadArr = [
            'university_code_name_cd' => $param['univCodeNameCode'],
            'fiscal_year' => $param['year'],
            'dept_subject_div' => $param['deptOrSubject'],
            'updated_num' => 0,
            'updated_at' => $param['updated_at'],
        ];

        $univCodeHead = $this->univCsvService->getUnivCodeHeader($param);
        if (empty($univCodeHead)) {
            $univCodeHeader = $this->univCsvService->createOrUpdateUnivCodeHead($univCodeHeadArr, 'create');
        } else {
            // TODO:コードヘッダー上書き
            $univCodeHeadArr['id'] = $univCodeHead->id;
            try {
                $univCodeHeader = $this->univCsvService->createOrUpdateUnivCodeHead($univCodeHeadArr, 'update');
            } catch (ExclusionException $e) {
                // 大学コードヘッダーは楽観ロックでロックされているので更新できない
                throw new ExclusionException($e->getMessage());
            }

            $this->univCsvService->deleteUnivCodesWithHeadID($univCodeHead->id);
        }

        return $univCodeHeader;
    }

    private function insertUnivCodeData($file, $univCodeHeader, $param)
    {
        // 既存データの削除
        $this->univCsvService->deleteUnivCodesWithHeadID($univCodeHeader->id);

        $bulkData = [];
        $chunkSize = config('constants.CSV_BULK_INSERT_SIZE'); // 1,000件単位でまとめてINSERT
        $row_count = 0;    // 行数カウンター

        foreach ($file as $row) {
            // 空行のスキップ（SplFileObject の末尾 null 対策）
            if (empty($row) || (count($row) === 1 && is_null($row[0]))) {
                continue;
            }

            // 1行目のヘッダーは取り込まない
            if ($row_count > 0) {
                // $systemCharCode が 'sjis' の場合、または mb_detect_encoding で判定する場合
                $encoding = mb_detect_encoding(implode('', $row), ['UTF-8', 'SJIS-win', 'eucjp-win'], true);

                if ($encoding === 'SJIS-win' || $encoding === 'SJIS') {
                    mb_convert_variables('UTF-8', 'SJIS-win', $row);
                }

                // 変数の初期化
                $university_cd = $row[0];
                $prefecture_cd = $row[1];
                $install_div = $row[2];
                $dept_line_cd1 = $row[3];
                $dept_line_cd2 = $row[4];
                $dept_line_cd3 = $row[5];
                $dept_line_cd4 = $row[6];
                $dept_line_cd5 = $row[7];
                $dept_line_cd6 = $row[8];
                $dept_line_cd7 = $row[9];
                $university_name_hkn = $row[10];
                $university_name = $row[11];
                $university_name_new = $row[12];
                $department_cd = $row[13];
                $department_seq = $row[14];
                $department_name = $row[15];
                $common_test_use = $row[20];
                $deviation_value = $row[21];
                if ($param['deptOrSubject'] == 2) {
                    $subject_cd = $row[16];
                    $subject_seq = $row[17];
                    $subject_name = $row[18];
                    $search_subject_name = $row[19];
                }

                // バルクインサート用の配列に追加
                $bulkData[] = [
                    'university_code_head_id' => $univCodeHeader->id,
                    'university_cd' => $university_cd,
                    'prefecture_cd' => $prefecture_cd,
                    'install_div' => $install_div,
                    'dept_line_cd1' => $dept_line_cd1,
                    'dept_line_cd2' => empty($dept_line_cd2) ? null : $dept_line_cd2,
                    'dept_line_cd3' => empty($dept_line_cd3) ? null : $dept_line_cd3,
                    'dept_line_cd4' => empty($dept_line_cd4) ? null : $dept_line_cd4,
                    'dept_line_cd5' => empty($dept_line_cd5) ? null : $dept_line_cd5,
                    'dept_line_cd6' => empty($dept_line_cd6) ? null : $dept_line_cd6,
                    'dept_line_cd7' => empty($dept_line_cd7) ? null : $dept_line_cd7,
                    'university_name_hkn' => $university_name_hkn,
                    'university_name' => $university_name,
                    'university_name_new' => $university_name_new,
                    'department_cd' => $department_cd,
                    'department_seq' => $department_seq,
                    'department_name' => $department_name,
                    'subject_cd' => empty($subject_cd) ? null : $subject_cd,
                    'subject_seq' => empty($subject_seq) ? null : $subject_seq,
                    'subject_name' => empty($subject_name) ? null : $subject_name,
                    'search_subject_name' => empty($search_subject_name) ? null : $search_subject_name,
                    'common_test_use' => $common_test_use,
                    'deviation_value' => $deviation_value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // 1,000件溜まったらまとめてINSERT
                if (count($bulkData) >= $chunkSize) {
                    DB::table('m_university_code')->insert($bulkData); // ※実際のテーブル名に書き換えてください
                    $bulkData = []; // 配列のリセット
                }
            }
            $row_count++; // 行数を加算
        }

        // 1,000件に満たない残りのデータをまとめてINSERT
        if (! empty($bulkData)) {
            DB::table('m_university_code')->insert($bulkData);
        }

    }

    private function insertHighSchoolCodeData($file)
    {
        // ★ トランザクション管理は親処理（呼び出し元）で行う前提とし、ここでは開始/コミットしない

        $bulkData = [];
        $chunkSize = config('constants.CSV_BULK_INSERT_SIZE');
        $row_count = 0; // 行数カウンター

        foreach ($file as $row) {
            // 空行のスキップ（SplFileObject の末尾 null 対策）
            if (empty($row) || (count($row) === 1 && is_null($row[0]))) {
                continue;
            }

            // 1行目のヘッダー（0行目）は取り込まない
            if ($row_count > 0) {
                // $systemCharCode が 'sjis' の場合、または mb_detect_encoding で判定する場合
                $encoding = mb_detect_encoding(implode('', $row), ['UTF-8', 'SJIS-win', 'eucjp-win'], true);

                if ($encoding === 'SJIS-win' || $encoding === 'SJIS') {
                    mb_convert_variables('UTF-8', 'SJIS-win', $row);
                }

                $highschool_code = $row[0];
                $highschool_name_hkn = $row[1];
                $zip_num = $row[2];
                $tel_num = $row[3];
                $install_cd = $row[4];
                $class_cd = $row[5];
                $highschool_name = $row[6];
                $address = $row[7];
                $prefecture_cd = substr($row[0], 0, 2);

                // バルクインサート用の配列に追加
                $bulkData[] = [
                    'highschool_code' => $highschool_code,
                    'highschool_name_hkn' => $highschool_name_hkn,
                    'zip_num' => $zip_num,
                    'tel_num' => $tel_num,
                    'install_cd' => $install_cd,
                    'class_cd' => $class_cd,
                    'highschool_name' => $highschool_name,
                    'address' => $address,
                    'prefecture_cd' => $prefecture_cd,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // 1,000件溜まったらまとめてINSERT
                if (count($bulkData) >= $chunkSize) {
                    DB::table('m_highschool_code')->insert($bulkData); // ※実際のテーブル名に書き換えてください
                    $bulkData = []; // 配列のリセット
                }
            }
            // ★ 行数カウンターをインクリメント（ループの最後で必ず実行）
            $row_count++;
        }

        // 1,000件に満たない残りのデータをまとめてINSERT
        if (! empty($bulkData)) {
            DB::table('m_highschool_code')->insert($bulkData);
        }
    }

    private function prepareSelectList()
    {
        $codeData = config('selectlistarray.CODE_DATA');
        $years = $this->tTestService->generateYearsArray('near');
        $mUnivCodeName = MUnivCodeName::select('university_code_name_cd', 'university_code_name')->get();
        $deptSubjSelect = config('selectlistarray.DEPT_SUBJ_SELECT');

        return compact(
            'codeData',
            'years',
            'mUnivCodeName',
            'deptSubjSelect'
        );
    }
    // TODO サービスに持たせる予定
    // private function generateYearsArray() {
    //     $currentYear = date('Y');
    //     $years = [];

    //     // Add next year
    //     $years[] = $currentYear + 1;

    //     // Add previous 4 years
    //     for ($i = 0; $i < 5; $i++) {
    //         $years[] = $currentYear - $i;
    //     }

    //     return $years;
    // }

}
