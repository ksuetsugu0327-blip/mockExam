<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\KmasterRegistRequest;
use App\Services\MClassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class KmasterRegistController extends Controller
{
    /**
     * コンストラクタ
     *
     * @param  MClassService  $mClassService  塾マスタサービス
     */
    public function __construct(MClassService $mClassService)
    {
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN').','.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_SURROGATE_SCHOOL'));

        $this->service = $mClassService;
    }

    // 教室マスタ・登録画面を表示する。
    // @return view
    public function index(Request $request): View
    {

        return view('kmasterRegist');
    }

    // 教室マスタ登録処理を行う。
    //
    // @param SmasterRegistRequest request
    // @return redirect
    public function store(KmasterRegistRequest $request): RedirectResponse
    {

        // 管理ユーザー以外はセッション値を使用する。
        if (auth(config('constants.GUARD_ADMIN'))->check() && ! session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) {
            $schCode = $request->get('sch_code');
        } else {
            $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        }
        // 画面情報を連想配列に設定
        $class = [
            'sch_code' => $schCode,
            'cls_code' => $request->get('cls_code'),
            'name' => $request->get('cls_name'),
        ];
        // パスワード更新判定
        $check = $request->get('jpchange');
        if (isset($check)) {
            // パスワードを追加
            $class['password'] = $request->get('password');
        }

        // パスワード再発行判定
        $temppassCheck = $request->get('temppasswordReissueCheck');
        // dd($check);
        if (isset($temppassCheck)) {
            // パスワードを追加
            $class['temppassword'] = Crypt::encryptString(Str::random(8));
        }
        // 登録処理
        $result = $this->service->register($class);
        // 結果判定
        if ($result['resultCode'] == -1) {
            // システムエラーの場合、abort
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->route('classes.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])->with('message', $result['message']);
    }
}
