<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MClassService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * 個人用パスワード変更用コントローラ
 */
class ClassChangePasswordController extends Controller
{
    // Laravel標準認証trait
    use AuthenticatesUsers;

    private $service;

    // 遷移先(暫定）
    protected $redirectTo = '';

    /**
     * コンストラクタ
     */
    public function __construct(MClassService $mClassService)
    {
        $this->service = $mClassService;
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

        return view('cls-password')->with('cls_code', $cls_code);
    }

    /**
     * パスワード変更登録処理
     *
     * @param  ValidateRequest  $request
     * @return string
     */
    public function register(Request $request)
    {
        $schCode = (int) $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $clsCode = (string) $request->get('cls_code');
        $password = (string) $request->get('password');

        // バリデーションチェック
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|regex:/^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])[!-~]{12,50}$/|confirmed',
            'password_confirmation' => 'required']);
        if ($validator->fails()) {
            $validator->errors()->add('message', trans('message.INPUT_ERROR_MESSAGE'));

            return back()->withErrors($validator)->with('cls_code', $clsCode);
        }

        // パスワード更新処理
        $id = $this->service->registerPassword($schCode, $clsCode, $password);

        // ログイン処理
        $this->guard()->loginUsingId($id);

        $class = Auth::guard(config('constants.GUARD_CLASS'))->user();
        session([config('constants.SESSION_KEY_CLSCODE') => $class->cls_code]);
        session([config('constants.SESSION_KEY_CLSNAME') => $class->name]);
        $this->redirectTo = route('classTopMenu.index', ['schCode' => session()->get(config('constants.SESSION_KEY_SCHCODE'))]);

        return $this->sendLoginResponse($request);
    }

    /**
     * 教室アカウント用ガード
     */
    protected function guard()
    {
        return Auth::guard(config('constants.GUARD_CLASS'));
    }
}
