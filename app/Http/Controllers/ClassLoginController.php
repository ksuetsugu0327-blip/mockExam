<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ClassLoginRequest;
use App\Services\MSchoolService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 教室アカウント用ログインコントローラ
 */
class ClassLoginController extends Controller
{
    // Laravel標準認証trait
    use AuthenticatesUsers;

    // サービスクラス
    private $service;

    // 遷移先(暫定）
    protected $redirectTo = '';

    /**
     * コンストラクタ
     *
     * @param  MSchoolService  $mSchoolService  塾マスタアクセス用インスタンス
     */
    public function __construct(MSchoolService $mSchoolService)
    {
        $this->service = $mSchoolService;
    }

    /**
     * 画面初期表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $schCode)
    {
        // フラッシュデータにない場合は、不正なアクセスの為、ログイン画面に戻す
        $cls_code = session()->get('cls_code');
        if (! isset($cls_code)) {
            return redirect(route('classIdCheck.index', ['schCode' => $schCode]));
        }

        return view('login-cls')->with('cls_code', $cls_code);
    }

    /**
     * 教室アカウントログイン処理
     *
     * @param App\Http\Requests\ClassLoginRequest
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(ClassLoginRequest $request)
    {
        if ($this->attemptLogin($request)) {
            $class = Auth::guard(config('constants.GUARD_CLASS'))->user();
            session([config('constants.SESSION_KEY_CLSCODE') => $class->cls_code]);
            session([config('constants.SESSION_KEY_CLSNAME') => $class->name]);
            $this->redirectTo = route('classTopMenu.index', ['schCode' => session()->get(config('constants.SESSION_KEY_SCHCODE'))]);

            return $this->sendLoginResponse($request);
        }

        // ログイン失敗時
        return back()->withInput()->withErrors(['message' => trans('auth.failed')])->with('cls_code', $request->get('cls_code'));

    }

    /**
     * 教室コード（認証で利用）
     *
     * @return string
     */
    public function username()
    {
        return 'cls_code';
    }

    /**
     * 認証に使う情報を塾コード、教室コードとする
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'sch_code', 'password');
    }

    /**
     * 塾アカウント用のログアウト処理
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        // セッション情報を退避
        $sch_code = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect(route('classIdCheck.index', ['schCode' => $sch_code]));
    }

    /**
     * 塾アカウント用ガード
     */
    protected function guard()
    {
        return Auth::guard(config('constants.GUARD_CLASS'));
    }
}
