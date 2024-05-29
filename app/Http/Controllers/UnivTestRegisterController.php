<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnivTestListRequest;
use App\Http\Requests\UnivTestRegisterRequest;
use App\Services\TTestInfoService;
use App\Services\MOemSchoolService;
use App\Services\MTestNameService;
use Illuminate\Http\Request;

class UnivTestRegisterController extends Controller
{
    private $service;
    private $oemSchoolService;
    private $testNameService;

    /**
     * コンストラクタ
     * @param TTestInfoService $tTestService 模試テーブルサービス
     */
    public function __construct(
        TTestInfoService            $tTestService
        ,MOemSchoolService          $mOemSchoolService
        ,MTestNameService           $mTestNameService
    )
    {
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->service = $tTestService;
        $this->oemSchoolService = $mOemSchoolService;
        $this->testNameService = $mTestNameService;
    }

    /**
     * テスト登録画面
     * @return \Illuminate\View\View テスト登録画面
     */
    public function register()
    {
        // 年度プルダウンを用意
        $years = $this->service->generateYearsArray();
        // 学年プルダウンを用意
        $grades = config('selectlistarray.UNVS_GRADE_LIST');
        // OEM塾を取得
        $oemSchoolList = $this->oemSchoolService->selectList()
                        ->pluck('oem_sch_name', 'oem_sch_code')
                        ->toArray();
        // テスト名を取得
        $testNameList = $this->testNameService->selectList()
                        ->pluck('test_name', 'test_name_cd')
                        ->toArray();

        return view('test_register', compact(
            'years',
            'grades',
            'oemSchoolList',
            'testNameList'
        ));
    }

    /**
     * テスト登録処理
     * @param UnivTestRegisterRequest $request
     * @return \Illuminate\Http\RedirectResponse テスト一覧画面
     */
    public function store(UnivTestRegisterRequest $request)
    {
        $testInfo = $request->all();
        // 登録処理
        $result = $this->service->register($testInfo);
        // 結果判定
        if ($result['resultCode'] == -1) {
            // システムエラーの場合、abort
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->route('test.list',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])->with('message',$result['message']);
    }
}
