<?php

namespace App\Http\Controllers;

//useしないと 自動的にnamespaceのパスが付与されるのでuse
use \SplFileObject;
use App\Utils\FileUtil;
use App\Models\MUniversityCodeName as MUnivCodeName;
use App\Services\UnivCsvService;
use App\Services\HighSchoolCsvService;
use App\Services\MUnivCodeNameService;
use App\Services\TTestInfoService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UnivCsvUploadRequest;
use App\Exceptions\ExclusionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Faker\Factory as Faker;


class UnivCsvUploadController extends Controller{

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
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->univCsvService = $univCsvService;
        $this->highSchoolCsvService = $highSchoolCsvService;
        $this->mUnivCodeNameService = $univCodeNameService;
        $this->tTestService = $testService;
    }

    /**
     * CSVインポート画面を表示
     */
    public function index() {

        $codeData = config('selectlistarray.CODE_DATA');
        $years = $this->tTestService->generateYearsArray("near");
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
        $updated_at = $request->input($univCodeNameCode.'_updated_at');
        $univCodeHeader = NULL;

        $param = compact('year', 'univCodeNameCode', 'deptOrSubject', 'updated_at');

        //SplFileObjectを生成
        $file = new SplFileObject($uploaded_file);

        //SplFileObject::READ_CSV が最速らしい
        $file->setFlags(SplFileObject::READ_CSV);

        // CSVの中身に対するバリデーションを実施
        if($dataType == 1){ //大学
            $csv_errors = $this->univCsvService->validateCsvData($file,$deptOrSubject);
        }
        else{
            $csv_errors = $this->highSchoolCsvService->validateCsvData($file);
        }

        // 登録データ格納配列
        $arrayInsertData = array();

        // エラー有無をチェック
        if (count($csv_errors) > 0) {
            $systemCharCode = config('constants.CHARACTER_CODE');
            foreach($csv_errors as $errs){
                //dd($errs);
                if(array_key_exists('charCodeErr',$errs)){
                    // 画面表示
                    return redirect()->route('univ.register', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])
                                ->withInput()
                                ->with(array_merge([
                                    'message' => $errs['message'],
                                ],$this->prepareSelectList()));
                }
            }
            // Set the session message
            session()->put('csv_errors', $csv_errors);

            // Set the session message and additional data
            $errors = new MessageBag([trans('message.FAILURE_MUNIVCODE_REGISTER_DATA'), $dataType]);

            // Redirect to the CSV download route
            return redirect()->route('univ.register', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])
                ->withErrors($errors);
        }
        else{
            //ローカルで実行する場合、タイムアウトしたら下記を適用してください。
            //set_time_limit(5000);
            if($dataType == 1){
                try{
                    $univCodeHeader = $this->handleUnivCodeHeader($param);
                }
                catch (ExclusionException $e){
                    // 楽観ロックされているため、エラーを画面に表示
                    return redirect()->route('univ.register', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])
                            ->with(array_merge([
                                'message' => $e->getMessage(),
                            ],$this->prepareSelectList()));
                }

                $this->insertUnivCodeData($file, $univCodeHeader, $param);
            }
            else{
                $this->highSchoolCsvService->clearTable();
                $this->insertHighSchoolCodeData($file);
            }
           
        }
        
        // 画面表示
        return redirect()->route('univ.register', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])
                    ->with(array_merge([
                        'message' => trans('message.SUCCESS_MUNIVCODE_CSV_DATA'),
                    ],$this->prepareSelectList()));
    }

    public function downloadErrCsv($schCode, $mode){
        $fileName = "";
        // if(!isset($sch_code)){
        //     return redirect(route('schoolIdCheck.index',['schCode'=>$schCode]));
        // }

        if($mode == config('selectlistarray.CODE_DATA.UNIV_CODE.VALUE')){
            $fileName = config('constants.UNIV_CODE_IMPORT_CSV_ERRFILENAME');

        }
        else if($mode == config('selectlistarray.CODE_DATA.HIGH_SCHOOL_CODE.VALUE')){
            $fileName = config('constants.HIGH_SCHOOL_CODE_IMPORT_CSV_ERRFILENAME');
        }

        // ヘッダ
        $csvHeader = ['エラー行', 'エラーメッセージ'];

        // HTTP Header 作成
        $responseHeader = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachement;filename=' . $fileName, 
        ];
        $csv_errors = session()->get('csv_errors', []);
            
        // エラー内容CSVをダウンロードさせる
        return response()->streamDownload(function() use($csv_errors, $csvHeader) {
                    echo FileUtil::CreateCSV($csv_errors, $csvHeader);
                    session()->forget('csv_errors');
                }, $fileName, $responseHeader);
    }

    private function handleUnivCodeHeader(array $param){
        $univCodeHeadArr = [
            'university_code_name_cd' => $param['univCodeNameCode'],
            'fiscal_year' => $param['year'],
            'dept_subject_div' => $param['deptOrSubject'],
            'updated_num' => 0,
            'updated_at' => $param['updated_at'],
        ];

        $univCodeHead = $this->univCsvService->getUnivCodeHeader($param['univCodeNameCode']);
        if (empty($univCodeHead)){
            $univCodeHeader = $this->univCsvService->createOrUpdateUnivCodeHead($univCodeHeadArr, "create");
        }
        else{
            // TODO:コードヘッダー上書き
            $univCodeHeadArr['id'] = $univCodeHead->id;
            try{
                $univCodeHeader = $this->univCsvService->createOrUpdateUnivCodeHead($univCodeHeadArr, "update");
            }
            catch (ExclusionException $e){
                // 大学コードヘッダーは楽観ロックでロックされているので更新できない
                throw new ExclusionException($e->getMessage());
            }

            $this->univCsvService->deleteUnivCodesWithHeadID($univCodeHead->id);
        }

        return $univCodeHeader;
    }

    private function insertUnivCodeData($file, $univCodeHeader, $param){
        $row_count = 0;
        $commit_count = 0;
        try{
            DB::beginTransaction();
            //取得したオブジェクトを読み込み
            foreach ($file as $row) {
                // CSVの文字コードがSJISの場合、UTF-8に変更
                if(mb_check_encoding($row, 'SJIS')){
                    $row = mb_convert_encoding($row, 'UTF-8', 'SJIS');
                }

                // 最終行の処理(最終行が空っぽの場合の対策
                if ($row === [null])
                    continue;

                // 1行目のヘッダーは取り込まない
                if ($row_count > 0) {
                    $university_cd        = $row[0];
                    $prefecture_cd        = $row[1];
                    $install_div          = $row[2];
                    $dept_line_cd1        = $row[3];
                    $dept_line_cd2        = $row[4];
                    $dept_line_cd3        = $row[5];
                    $dept_line_cd4        = $row[6];
                    $dept_line_cd5        = $row[7];
                    $dept_line_cd6        = $row[8];
                    $dept_line_cd7        = $row[9];
                    $university_name_hkn  = $row[10];
                    $university_name      = $row[11];
                    $university_name_new  = $row[12];
                    $department_cd        = $row[13];
                    $department_seq       = $row[14];
                    $department_name	  = $row[15];
                    $common_test_use	  = $row[20];								
                    $deviation_value	  = $row[21];
                    
                    if($param['deptOrSubject'] == 2){
                        $subject_cd			  = $row[16];						
                        $subject_seq		  = $row[17];							
                        $subject_name		  = $row[18];							
                        $search_subject_name  = $row[19];	
                    }			

                    // 登録データを配列に追加
                    $arrayInsertData = array(
                        'university_cd'       => $university_cd,
                        'prefecture_cd'       => $prefecture_cd,
                        'install_div'         => $install_div,
                        'dept_line_cd1'       => $dept_line_cd1,
                        'dept_line_cd2'       => empty($dept_line_cd2)?NULL:$dept_line_cd2,
                        'dept_line_cd3'       => empty($dept_line_cd3)?NULL:$dept_line_cd3,
                        'dept_line_cd4'       => empty($dept_line_cd4)?NULL:$dept_line_cd4,
                        'dept_line_cd5'       => empty($dept_line_cd5)?NULL:$dept_line_cd5,
                        'dept_line_cd6'       => empty($dept_line_cd6)?NULL:$dept_line_cd6,
                        'dept_line_cd7'       => empty($dept_line_cd7)?NULL:$dept_line_cd7,
                        'university_name_hkn' => $university_name_hkn,
                        'university_name'     => $university_name,
                        'university_name_new' => $university_name_new,
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

                    if( !empty($arrayInsertData) &&  !empty($univCodeHeader)){
                        // Insert処理
                        $this->univCsvService->registerArray($univCodeHeader->id, $arrayInsertData);
                    }
                }
                if ($commit_count == config('constants.PSN_IMP_COMMIT_COUNT')){
                    DB::commit();
                    $commit_count = 0;
                }
                // 行カウンタをインクリメント
                $row_count++;
                $commit_count++;
            }
            DB::commit(); //  CSV最後の一行読み込み終わったらcommitする、でないと最後の一バッチは未commitのまま処理終わる
        }
        catch (Exception $ex) {
            // システムエラー
            DB::rollBack();
            report($ex);
            return redirect()->route('univ.register', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])
            ->with(array_merge([
                'message' => $ex->getMessage(),
            ],$this->prepareSelectList()));
        }
    }

    private function insertHighSchoolCodeData($file){
        $row_count = 0;

        //取得したオブジェクトを読み込み
        foreach ($file as $row) {
            // CSVの文字コードがSJISの場合、UTF-8に変更
            if(mb_check_encoding($row, 'SJIS')){
                $row = mb_convert_encoding($row, 'UTF-8', 'SJIS');
            }

            // 最終行の処理(最終行が空っぽの場合の対策
            if ($row === [null])
                continue;

            // 1行目のヘッダーは取り込まない
            if ($row_count > 0) {
                $highschool_code     = $row[0];
                $highschool_name_hkn = $row[1];
                $zip_num             = $row[2];
                $tel_num             = $row[3];
                $install_cd          = $row[4];
                $class_cd            = $row[5];
                $highschool_name     = $row[6];
                $address             = $row[7];
                $prefecture_cd	     = substr($row[0], 0, 2);

                // 登録データを配列に追加
                $arrayInsertData[] = array(
                    'highschool_code'     => $highschool_code,
                    'highschool_name_hkn' => $highschool_name_hkn,
                    'zip_num'             => $zip_num,
                    'tel_num'             => $tel_num,
                    'install_cd'          => $install_cd,
                    'class_cd'            => $class_cd,
                    'highschool_name'     => $highschool_name,
                    'address'             => $address,
                    'prefecture_cd'       => $prefecture_cd,
                );
            }
            // 行カウンタをインクリメント
            $row_count++;
        }

        if( !empty($arrayInsertData) ){
          // Insert処理
          $this->highSchoolCsvService->registerArray($arrayInsertData);
        }
    }

    private function prepareSelectList(){
        $codeData = config('selectlistarray.CODE_DATA');
        $years = $this->tTestService->generateYearsArray("near");
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
