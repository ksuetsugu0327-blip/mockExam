<?php

namespace App\Http\Controllers\Rest;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\UnivCodeHeadService;
use Illuminate\Http\Request;

class TestManagementApiController extends Controller
{
    private $univCodeHeadService;

    /**
     * コンストラクタ
     */
    public function __construct(UnivCodeHeadService $mUnivCodeHeadService)
    {
        $this->univCodeHeadService = $mUnivCodeHeadService;
    }

    /**
     * 大学コードヘッダー情報取得API(テスト・登録用)
     */
    public function getUnivCodeHeadList(Request $request): JsonResponse
    {
        try {
            $year = $request->fy;
            // 大学コードヘッダー情報取得
            $data = $this->univCodeHeadService->selectList($year)->toArray();
            // ヘッダー設定
            $data_num = count($data);
            $header = [
                'status' => 'success',
                'message' => ($data_num === 0) ? '大学情報が登録されていません。' : '',
                'data_num' => $data_num,
            ];

            return response()->json(compact('header', 'data'));
        } catch (\Exception $e) {
            // エラーヘッダー設定
            $header = [
                'status' => 'error',
                'message' => '',
                'data_num' => 0,
            ];

            return response()->json(compact('header', [], 500));
        }
    }

    public function chkExistUnivCodeHead(Request $request): JsonResponse
    {
        try {
            $fiscalYear = $request->fy;
            $univCodeNameCode = $request->dtcd;
            $deptSubjDiv = $request->dptsub;

            $data = $this->univCodeHeadService->chkExistUnivCodeHead($fiscalYear, $univCodeNameCode, $deptSubjDiv)->toArray();

            // ヘッダー設定
            $data_num = count($data);
            $header = [
                'status' => 'success',
                'message' => '',
                'data_num' => $data_num,
            ];

            return response()->json(compact('header', 'data'));
        } catch (\Exception $e) {
            // エラーヘッダー設定
            $header = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'data_num' => 0,
            ];

            return response()->json(compact('header', [], 500));
        }
    }
}
