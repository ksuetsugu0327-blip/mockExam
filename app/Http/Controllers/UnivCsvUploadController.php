<?php

namespace App\Http\Controllers;

//useしないと 自動的にnamespaceのパスが付与されるのでuse
use \SplFileObject;
use App\Utils\FileUtil;
use App\Models\MUniversityCodeName as MUnivCodeName;
use App\Services\UnivCsvService as CsvService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UnivCsvUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Faker\Factory as Faker;


class UnivCsvUploadController extends Controller{

    private $csvService;

    public function __construct(CsvService $csvService) {
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->csvService = $csvService;
    }

    /**
     * CSVインポート画面を表示
     */
    public function index() {

        $codeData = config('selectlistarray.CODE_DATA');
        $years = $this->generateYearsArray();
        $mUnivCodeName = MUnivCodeName::select('university_code_name_cd', 'university_code_name')->get();
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
     * @return \Illuminate\Http\UnivCsvUploadRequest
     * @param Request $request
     */
    public function upload(UnivCsvUploadRequest $request) {

        // ロケールを設定(日本語に設定)
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        // アップロードしたファイルを取得
        // 'csv_file' はビューの inputタグのname属性
        $uploaded_file = $request->file('csv_file');

        $dataType = $request->input('data_type');
        $year = $request->input('fiscal_year');
        $univCodeNameCode = $request->input('university_code_name_code');
        $deptOrSubject = $request->input('dept_subject_div');
        $univCodeHeader = NULL;

        //SplFileObjectを生成
        $file = new SplFileObject($uploaded_file);

        //SplFileObject::READ_CSV が最速らしい
        $file->setFlags(SplFileObject::READ_CSV);

        // CSVの中身に対するバリデーションを実施
        $csv_errors = $this->csvService->validateCsvData($file);

        // 登録データ格納配列
        $arrayInsertData = array();

        // エラー有無をチェック
        if (count($csv_errors) > 0) {
            // Set the session message
            session()->put('csv_errors', $csv_errors);

            // Set the session message and additional data
            $errors = new MessageBag([trans('message.FAILURE_MUNIVCODE_REGISTER_DATA'), 'csv_err']);

            // Redirect to the CSV download route
            return redirect()->route('univ.register', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])
                ->withErrors($errors);
        }
        else{

            $univCodeHeadArr = [
                'university_code_name_cd' => $univCodeNameCode,
                'fiscal_year' => $year,
                'dept_subject_div' => $deptOrSubject,
                'updated_num' => 0,
            ];

            $univCodeHead = $this->csvService->getUnivCodeHeader($univCodeNameCode);
            if (empty($univCodeHead)){
                $univCodeHeader = $this->csvService->createOrUpdateUnivCodeHead($univCodeHeadArr, "create");
            }
            else{
                // TODO:コードヘッダー上書き
                $univCodeHeadArr['id'] = $univCodeHead->id;
                $univCodeHeader = $this->csvService->createOrUpdateUnivCodeHead($univCodeHeadArr, "update");

                $this->csvService->deleteUnivCodesWithHeadID($univCodeHead->id);
            }

            $row_count = 0;

            //取得したオブジェクトを読み込み
            foreach ($file as $row) {
                // 最終行の処理(最終行が空っぽの場合の対策
                if ($row === [null])
                    continue;

                // 1行目のヘッダーは取り込まない
                if ($row_count > 0) {
                    $university_cd        = $row[0];
                    $prefecture_cd        = $row[1];
                    $install_div          = $row[2];
                    $dept_line_cd         = $row[3];
                    $university_name_hkn  = $row[4];
                    $university_name      = $row[5];
                    $department_cd        = $row[6];
                    $department_seq       = $row[7];
                    $department_name	  = $row[8];
                    $common_test_use	  = $row[13];								
                    $deviation_value	  = $row[14];
                    
                    if($deptOrSubject==2){
                        $subject_cd			  = $row[9];						
                        $subject_seq		  = $row[10];							
                        $subject_name		  = $row[11];							
                        $search_subject_name  = $row[12];	
                    }			

                    // 登録データを配列に追加
                    $arrayInsertData[] = array(
                        'university_cd'       => $university_cd,
                        'prefecture_cd'       => $prefecture_cd,
                        'install_div'         => $install_div,
                        'dept_line_cd'        => $dept_line_cd,
                        'university_name_hkn' => $university_name_hkn,
                        'university_name'     => $university_name,
                        'department_cd'       => $department_cd,
                        'department_seq'      => $department_seq,
                        'department_name'     => $department_name,
                        'subject_cd'          => empty($subject_cd)?NULL:$subject_cd,
                        'subject_seq'         => empty($subject_seq)?NULL:$subject_seq,
                        'subject_name'        => empty($subject_name)?NULL:$subject_name,
                        'search_subject_name' => empty($search_subject_name)?NULL:$search_subject_name,
                        'common_test_use'     => $common_test_use,
                        'deviation_value'     => $deviation_value,
                    );
                    
                }
                // 行カウンタをインクリメント
                $row_count++;
            }

            if( !empty($arrayInsertData) &&  !empty($univCodeHeader)){
              // Insert処理
              $this->csvService->registerArray($univCodeHeader->id, $arrayInsertData);
            }
        }

        $codeData = config('selectlistarray.CODE_DATA');
        $years = $this->generateYearsArray();
        $mUnivCodeName = MUnivCodeName::select('university_code_name_cd', 'university_code_name')->get();
        $deptSubjSelect = config('selectlistarray.DEPT_SUBJ_SELECT');
        

        // 画面表示
        return view('univ_register', compact(
                                        'codeData',
                                        'years',
                                        'mUnivCodeName',
                                        'deptSubjSelect'
                                    ))->with('message', trans('message.SUCCESS_MUNIVCODE_CSV_DATA'));
    }

    public function downloadErrCsv($csv_errors){
        // ヘッダ
        $csvHeader = ['エラー行', 'エラーメッセージ'];

        // HTTP Header 作成
        $responseHeader = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachement;filename=' . config('constants.UNIV_CODE_IMPORT_CSV_ERRFILENAME'), 
        ];
        $csv_errors = session()->get('csv_errors', []);

        // エラー内容CSVをダウンロードさせる
        return response()->streamDownload(function() use($csv_errors, $csvHeader) {
                    echo FileUtil::CreateCSV($csv_errors, $csvHeader);
                    session()->forget('csv_errors');
                }, config('constants.UNIV_CODE_IMPORT_CSV_ERRFILENAME'), $responseHeader);
    }

    private function generateYearsArray() {
        $currentYear = date('Y');
        $years = [];

        // Add next year
        $years[] = $currentYear + 1;

        // Add previous 4 years
        for ($i = 0; $i < 5; $i++) {
            $years[] = $currentYear - $i;
        }

        return $years;
    }

}
