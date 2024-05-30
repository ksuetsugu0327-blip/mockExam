<?php

namespace App\Http\Controllers\Rest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MOemService;
use App\Services\TTestInfoService;
use App\Services\UnivCodeHeadService;

class UnivCodeSearchController extends Controller
{
    private $oemSchoolService;
    private $testNameService;
    private $common;

    /**
     * コンストラクタ
     * @param MOemSchoolService $mOemSchoolService OEM塾マスタサービス
     */
    public function __construct(
        MOemService $mOemSchoolService,
        TTestInfoService $mTestInfoService,
        RestCommon $common
    ){
        $this->oemSchoolService = $mOemSchoolService;
        $this->testInfoService = $mTestInfoService;
        $this->common = $common;
    }

    public function index(Request $request) {
       
        return view('univ_search');
    }

    /**
     * 塾名マスタリスト取得処理
     *
     * @return JSON 塾名マスタリスト
     */
    public function getOemSchoolName(Request $request){
        $OEMSchCode = $request->get('oem_sch_code');

        try{
            $list = $this->oemSchoolService->getOemSchoolName($OEMSchCode);
        }
        catch (\Exception $e){        
            $header = $this->common->makeHeader('error', '', 0);
            $data = [];

            return response()->json(compact('header', 'data', 500));
        }

        if (empty($list)){
            $header = $this->common->makeHeader('success', 'Data retrieved successfully', 0);
            $data = [];

            return response()->json(compact('header', 'data'));
        }

        $header = $this->common->makeHeader('success', 'Data retrieved successfully', 1);

        $data = [
            'oem_sch_code' => $list->oem_sch_code,
            'oem_sch_name' => $list->oem_sch_name,
        ];

        return response()->json(compact('header', 'data'));
    }

    /**
     * テスト名リスト取得処理
     *
     * @return JSON テスト名リストマスタリスト
     */
    public function getTestInfo(Request $request){
        $OEMSchCode = $request->get('oem_sch_code');
        $grade = $request->get('grade');
        //return response()->json(compact('OEMSchCode', 'grade'));

        try{
            $list = $this->testInfoService->getTestInfo($OEMSchCode, $grade);
        }
        catch (\Exception $e){
            $header = $this->common->makeHeader('error', '', 0);
            $data = ['message' => $e->getMessage()];

            return response()->json(compact('header', 'data', 500));
        }

        if (empty($list)){
            $header = $this->common->makeHeader('success', 'テスト情報が登録されていません', 0);
            $data = [];
            
            return response()->json(compact('header', 'data'));
        }

        $header = $this->common->makeHeader('success', '', count($list)??0);

        $rows = [];

        foreach($list as $key => $test) {
            $rows[] = [
                'test_code' => $test->id,
                'test_name' => $test->test_name,
            ];
        }

        $data = [
            'test_info_list' => $rows,
        ];

        return response()->json(compact('header', 'data'));
    }

    /**
     * 大学コードヘッダー情報取得処理
     *
     * @return JSON 大学コードヘッダー情報
     */
    public function getUnivCodeHead(Request $request){
        $testID = $request->get('test_id');
        try{
            $result = $this->testInfoService->getUnivCodeHead($testID);
        }
        catch (\Exception $e){
            $header = $this->common->makeHeader('error', '', 0);
            $data = ['message' => $e->getMessage()];

            return response()->json(compact('header', 'data', 500));
        }

        if (empty($result)){
            $header = $this->common->makeHeader('success', '大学コード情報が登録されていません', 0);
            $data = [];
            
            return response()->json(compact('header', 'data'));
        }

        $header = $this->common->makeHeader('success', '', 1);

        $data = [
            'university_code_head_id' => $result->university_code_head_id,
            'dept_subject_div' => $result->dept_subject_div,
        ];

        return response()->json(compact('header', 'data'));
    }
}
