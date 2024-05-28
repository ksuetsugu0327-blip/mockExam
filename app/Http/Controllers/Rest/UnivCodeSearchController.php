<?php

namespace App\Http\Controllers\Rest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MOemService;

class UnivCodeSearchController extends Controller
{
    private $oemSchoolService;

    /**
     * コンストラクタ
     * @param MOemSchoolService $mOemSchoolService OEM塾マスタサービス
     */
    public function __construct(MOemService $mOemSchoolService){
        $this->oemSchoolService = $mOemSchoolService;
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

        return response()->json($data);
    }
}
