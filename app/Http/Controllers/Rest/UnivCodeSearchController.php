<?php

namespace App\Http\Controllers\Rest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MOemService;
use App\Services\TTestInfoService;

class UnivCodeSearchController extends Controller
{
    private $oemSchoolService;
    private $testNameService;

    /**
     * コンストラクタ
     * @param MOemSchoolService $mOemSchoolService OEM塾マスタサービス
     */
    public function __construct(MOemService $mOemSchoolService, TTestInfoService $mTestInfoService){
        $this->oemSchoolService = $mOemSchoolService;
        $this->testInfoService = $mTestInfoService;
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
            $header = [
                'status' => 'error',
                'message' => '',
                'data_num' => 0,
            ];
            $data = [];

            return response()->json(compact('header', 'data', 500));
        }

        $header = [
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data_num' => 1,
        ];

        if (empty($list)){
            $header = [
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data_num' => 0,
            ];
            $data = [];
            return response()->json(compact('header', 'data'));
        }

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
            $header = [
                'status' => 'error',
                'message' => '',
                'data_num' => 0,
            ];
            $data = ['message' => $e->getMessage()];

            return response()->json(compact('header', 'data', 500));
        }

        $header = [
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data_num' => count($list)??0,
        ];

        if (empty($list)){
            $header = [
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data_num' => 0,
            ];
            $data = [];
            return response()->json(compact('header', 'data'));
        }
        $rows = [];

        foreach($list as $key => $test) {
            $rows[] = [
                'test_code' => $test->test_name_cd,
                'test_name' => $test->test_name,
            ];
        }

        $data = [
            'test_info_list' => $rows,
        ];

        return response()->json(compact('header', 'data'));
    }
}
