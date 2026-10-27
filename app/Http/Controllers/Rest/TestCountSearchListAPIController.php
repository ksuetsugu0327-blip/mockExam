<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Services\TestCountSearchListService;
use Illuminate\Http\Request;

class TestCountSearchListAPIController extends Controller
{
    private $service;

    /**
     * コンストラクタ
     *
     * @param  testCountSearchListService  $testCountSearchListService  マスタサービス
     */
    public function __construct(TestCountSearchListService $testCountSearchListService)
    {
        // No session-based auth middleware for API
        $this->service = $testCountSearchListService;
    }

    /**
     * 公開フラグチェックAPI
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPublishFlg(Request $request)
    {
        \Log::info('checkPublishFlg', $request->all());
        $schCode = $request->get('sch_code');
        $clsCode = $request->get('cls_code');
        $year = $request->get('year');
        $cnttest = $request->get('cnttest');
        $grade = $request->get('grade');

        $conditions = [
            'sch_code' => $schCode,
            'cls_code' => $clsCode,
            'year' => $year,
            'cnttest' => $cnttest,
            'grade' => $grade,
            'path_kbn' => config('constants.PATH_KBN_FILE'), // パス区分:ファイル
            'category_kbn' => config('constants.FILE_KBN_PSN'), // 識別子:個人
        ];

        $result = $this->service->checkPublishFlg($conditions);

        return response()->json($result);
    }
}
