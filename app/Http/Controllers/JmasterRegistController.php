<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\JmasterRegistRequest;
use App\Services\MSchoolService;
use Illuminate\Http\Request;

class JmasterRegistController extends Controller
{
    private $service;

    /**
     * コンストラクタ
     *
     * @param  MSchoolService  $mSchoolService  塾マスタサービス
     */
    public function __construct(MSchoolService $mSchoolService)
    {
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->service = $mSchoolService;
    }

    /**
     * 塾マスタ・登録画面を表示する。
     *
     * @return view
     */
    public function index(): View
    {
        return view('jmasterRegist');
    }

    /**
     * 塾マスタ登録処理を行う。
     *
     * @param request
     * @return redirect
     */
    public function store(JmasterRegistRequest $request): RedirectResponse
    {

        // 画面情報を連想配列に設定
        $school = [
            'sch_code' => $request->get('sch_code'),
            'name' => $request->get('sch_name'),
            'portal_account' => $request->get('portal_account'),
            'cntconvert_kbn' => $request->get('cntconvert_kbn'),
            'cnttest_kbn' => $request->get('cnttest_kbn'),
            'testname' => $request->get('testname'),
            'password' => $request->get('password'),
            'admin_kbn' => $request->get('admin_kbn'),
            'psn_hide_flg' => $request->get('psn_hide_flg'),
            'schname_hide_flg' => $request->get('schname_hide_flg'),
            'psncard_regist_flg' => $request->get('psncard_regist_flg'),
            'psn_psw_update_flg' => $request->get('psn_psw_update_flg'),
            'cls_grade_csv_flg' => $request->get('cls_grade_csv_flg'),
            'permit_ip' => $request->get('permit_ip'),
        ];

        // 登録処理
        $result = $this->service->register($school);
        // 結果判定
        if ($result['resultCode'] == -1) {
            // システムエラーの場合、abort
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->route('schools.index', ['schCode' => $request->session()->get(config('constants.SESSION_KEY_SCHCODE'))])->with('message', $result['message']);
    }
}
