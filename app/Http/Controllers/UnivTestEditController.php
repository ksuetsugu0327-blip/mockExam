<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UnivTestEditRequest;
use App\Services\MCodeBookService;
use App\Services\MOemSchoolService;
use App\Services\MTestNameService;
use App\Services\TTestInfoService;
use Illuminate\Http\Request;

class UnivTestEditController extends Controller
{
    private $service;

    private $oemSchoolService;

    private $testNameService;

    /**
     * コンストラクタ
     *
     * @param  TTestInfoService  $tTestService  模試テーブルサービス
     */
    public function __construct(
        TTestInfoService $tTestService,
        MOemSchoolService $mOemSchoolService,
        MTestNameService $mTestNameService,
        MCodeBookService $mCodeBookService
    ) {
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->service = $tTestService;
        $this->oemSchoolService = $mOemSchoolService;
        $this->testNameService = $mTestNameService;
        $this->codeBookService = $mCodeBookService;
    }

    /**
     * テスト登録画面
     *
     * @return \Illuminate\View\View テスト修正画面
     */
    public function edit(int $schCode, int $id): View
    {
        $testInfo = $this->service->getTestInfoById($id);
        $grades = config('selectlistarray.UNVS_GRADE_LIST');

        // コード表PDF
        $codeBookList = $this->codeBookService->selectList()
            ->pluck('codebook_name', 'codebook_cd')
            ->toArray();

        return view('test_edit', compact(
            'testInfo',
            'grades',
            'codeBookList'
        ));
    }

    /**
     * テスト登録処理
     *
     * @return \Illuminate\Http\RedirectResponse テスト一覧画面
     */
    public function update(UnivTestEditRequest $request, int $schCode, int $id): RedirectResponse
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
        return redirect()->route('test.list', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])->with('message', $result['message']);
    }

    /**
     * テスト情報削除処理を行う。
     *
     * @param  int  $id  削除対象ID
     */
    public function delete(Request $request, int $schCode, int $id): RedirectResponse
    {
        // 削除処理
        $result = $this->service->delete($id);
        if ($result['resultCode'] == config('constants.RESULT_CODE_SYSTEM_ERROR')) {
            // システムエラーの場合
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->to(route('test.list', [$schCode]))->with('message', $result['message']);
    }
}
