<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ClassTemporaryLoginRequest;
use App\Services\MClassService;
use App\Utils\PasswordUtil;

/**
 * 仮パスワードログインコントローラ
 */
class ClassTemporaryLoginController extends Controller
{
    // サービスクラス(教室用)
    private $mClassService;

    /**
     * コンストラクタ
     *
     * @param  MClassService  $mClassService  個人マスタアクセス用インスタンス
     */
    public function __construct(MClassService $mClassService)
    {
        $this->mClassService = $mClassService;
    }

    /**
     * 初期表示処理
     *
     * @param  type  $schCode  塾コード
     * @return type
     */
    public function index($schCode)
    {
        // フラッシュデータにない場合は、不正なアクセスの為、ログイン画面に戻す
        $cls_code = session()->get('cls_code');
        if (! isset($cls_code)) {
            return redirect(route('classIdCheck.index', ['schCode' => $schCode]));
        }

        // 教室マスタ検索
        $classes = $this->mClassService->selectOneByKeyWithSch((int) $schCode, (int) $cls_code);
        if ($classes->count() == 0) {
            // ありえないので、発生時はログイン画面に強制的に戻す
            return redirect(route('classIdCheck.index', ['schCode' => $schCode]));
        }

        return view('cls-login-kpass')->with('cls_code', $cls_code);
    }

    /**
     * 仮パスワードログインチェック
     */
    public function login(ClassTemporaryLoginRequest $request)
    {

        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $clsCode = $request->get('cls_code');
        // 教室マスタ検索
        $classes = $this->mClassService->selectOneByKey((int) $schCode, (int) $clsCode);
        if ($classes->count() == 0) {
            // ありえないので、発生時はログイン画面に強制的に戻す
            return redirect(route('classIdCheck.index', ['schCode' => $schCode]));
        }

        $class = $classes->get(0);

        // 仮パスワードでのログイン認証
        if (! PasswordUtil::temporaryValidateCredentials($request->get('password'), $class->temppassword)) {
            // パスワード不正メッセージを返却
            return back()->withErrors(['message' => trans('auth.failed')])->with('cls_code', $clsCode);
        }

        return redirect()->route('ClassChangePassword.index', ['sch_code' => $schCode])->with('cls_code', $clsCode);
    }
}
