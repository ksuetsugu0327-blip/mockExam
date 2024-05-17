<?php

namespace App\Http\Controllers;

//useしないと 自動的にnamespaceのパスが付与されるのでuse
use \SplFileObject;
use App\Models\MUniversityCodeName as MUnivCodeName;
use App\Services\UnivCsvService as CsvService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UnivCsvUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Utils\FileUtil;
use Illuminate\Support\Facades\DB;
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
//dd($mUnivCodeName);
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

        // ヘッダ
        $csvHeader = ['エラー行', 'エラーメッセージ'];

        // ロケールを設定(日本語に設定)
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        // アップロードしたファイルを取得
        // 'csv_file' はビューの inputタグのname属性
        $uploaded_file = $request->file('csv_file');

        $dataType = $request->input('data_type');
        $year = $request->input('fiscal_year');
        $univCodeNameCode = $request->input('university_code_name_code');
        $deptOrSubject = $request->input('dept_subject_div');
        
dd($dataType, $year, $univCodeNameCode, $deptOrSubject);

        //$univCodeHeadID = $this->csvService->getUnivCodeHeader($univCodeNameCode, $year, $deptOrSubject);

        //SplFileObjectを生成
        $file = new SplFileObject($uploaded_file);

        //SplFileObject::READ_CSV が最速らしい
        $file->setFlags(SplFileObject::READ_CSV);

        // CSVの中身に対するバリデーションを実施
        $csv_errors = $this->csvService->validateCsvData($file);

//dd($csv_errors);




        // 登録データ格納配列
        $arrayInsertData = array();

        // エラー有無をチェック
        if (count($csv_errors) > 0) {
            // HTTP Header 作成
            $responseHeader = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachement;filename=' . config('constants.UNIV_CODE_IMPORT_CSV_ERRFILENAME'), 
            ];

            // エラー内容CSVをダウンロードさせる
            return response()->streamDownload(function() use($csv_errors, $csvHeader) {
                        echo FileUtil::CreateCSV($csv_errors, $csvHeader);
                    }, config('constants.UNIV_CODE_IMPORT_CSV_ERRFILENAME'), $responseHeader);
        }
        else{
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
                    $subject_cd			  = $row[9];						
                    $subject_seq		  = $row[10];							
                    $subject_name		  = $row[11];							
                    $search_subject_name  = $row[12];									
                    $common_test_use	  = $row[13];								
                    $deviation_value	  = $row[14];								

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
                        'subject_cd'          => $subject_cd,
                        'subject_seq'         => $subject_seq,
                        'subject_name'        => $subject_name,
                        'search_subject_name' => $search_subject_name,
                        'common_test_use'     => $common_test_use,
                        'deviation_value'     => $deviation_value,
                    );
                    
                }
                // 行カウンタをインクリメント
                $row_count++;
            }
//dd($arrayInsertData);
            if( !empty($arrayInsertData) ){
              // Insert処理
              $this->csvService->registerArray($univCodeHeadID, $arrayInsertData);
            }
        }

        // 画面表示
        return view('csvUpload')->with('message', trans('message.SUCCESS_MPERSON_CSV_DATA'));
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
