<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Services\UnivCodeHeadService;
use Illuminate\Http\Request;

class TestManagementApiController extends Controller
{
    private $univCodeHeadService;

    /**
     * コンストラクタ
     * @param UnivCodeHeadService $mUnivCodeHeadService
     */
    public function __construct(UnivCodeHeadService $mUnivCodeHeadService)
    {
        $this->univCodeHeadService = $mUnivCodeHeadService;
    }

    /**
     * 大学コードヘッダー情報取得API(テスト・登録用)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnivCodeHeadList(Request $request)
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
                'data_num' => $data_num
            ];

            return response()->json(compact('header', 'data'));
        } catch (\Exception $e) {
            // エラーヘッダー設定
            $header = [
                'status' => 'error',
                'message' => '',
                'data_num' => 0
            ];

            return response()->json(compact('header', [], 500));
        }
    }
}
