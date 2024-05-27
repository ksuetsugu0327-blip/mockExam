<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnivTestListRequest;
use App\Http\Requests\UnivTestEditRequest;
use App\Services\TTestInfoService;
use App\Services\MOemSchoolService;
use App\Services\MTestNameService;
use App\Services\MUniversityCodeNameService;
use Illuminate\Http\Request;

class UnivTestEditController extends Controller
{
    private $service;
    private $oemSchoolService;
    private $testNameService;
    private $universityCodeNameService;
    
    /**
     * コンストラクタ
     * @param TTestInfoService $tTestService 模試テーブルサービス
     */
    public function __construct(
        TTestInfoService            $tTestService
        ,MOemSchoolService          $mOemSchoolService
        ,MTestNameService           $mTestNameService
        ,MUniversityCodeNameService $mUniversityCodeNameService
    )
    {
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->service = $tTestService;
        $this->oemSchoolService = $mOemSchoolService;
        $this->testNameService = $mTestNameService;
        $this->universityCodeNameService = $mUniversityCodeNameService;
    }

    /**
     * テスト登録画面
     * @param int $id
     * @param int $schCode
     * @return \Illuminate\View\View テスト修正画面
     */
    public function edit(int $schCode, int $id)
    {
        $testInfo = $this->service->getTestInfoById($id);
        $grades = config('selectlistarray.UNVS_GRADE_LIST');
        // 大学コードデータ名を取得
        $universityCodeNameList = $this->universityCodeNameService->selectList()
                        ->pluck('university_code_name', 'university_code_head_id')
                        ->toArray();

        return view('test_edit', compact(
            'testInfo',
            'grades',
            'universityCodeNameList',
        ));
    }

    /**
     * テスト登録処理
     * @param UnivTestEditRequest $request
     * @param int $schCode
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse テスト一覧画面
     */
    public function update(UnivTestEditRequest $request, int $schCode, int $id)
    {
        $testInfo = $request->all();
        // 登録処理
        $result = $this->service->update($testInfo, $id);
        // 結果判定
        if ($result['resultCode'] == -1) {
            // システムエラーの場合、abort
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->route('test.list',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])->with('message',$result['message']);
    }

    /**
     * テスト情報削除処理を行う。
     * @param int $schCode
     * @param int $id 削除対象ID
     * @return redirect
     */
    public function delete(Request $request, int $schCode, int $id) {
        // 削除処理
        $result = $this->service->delete($id);
        if($result['resultCode']==config('constants.RESULT_CODE_SYSTEM_ERROR')){
            // システムエラーの場合
            abort(500);
        }
        
        // 一覧画面へリダイレクト
        return redirect()->to(route('test.list',[$schCode]))->with('message',$result['message']);
    }
}
