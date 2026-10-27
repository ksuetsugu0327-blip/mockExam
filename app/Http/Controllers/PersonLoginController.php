<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Services\MPersonService;
use App\Services\MSchoolService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * 個人（生徒）アカウント用ログインコントローラ
 */
class PersonLoginController extends Controller
{
    // Laravel標準認証trait
    use AuthenticatesUsers;

    // 遷移先
    protected $redirectTo = '';

    // サービスクラス(塾用)
    private $mSchoolService;

    // サービスクラス(個人用)
    private $mPersonService;

    /**
     * コンストラクタ
     */
    public function __construct(MSchoolService $mSchoolService, MPersonService $mPersonService)
    {
        $this->mSchoolService = $mSchoolService;
        $this->mPersonService = $mPersonService;
    }

    /**
     * 初期表示処理
     *
     * @param  type  $schCode  塾コード
     */
    public function index($schCode): type
    {
        // フラッシュデータにない場合は、不正なアクセスの為、ログイン画面に戻す
        $psn_code = session()->get('psn_code');
        if (! isset($psn_code)) {
            return redirect(route('personIdCheck.index', ['schCode' => $schCode]));
        }

        return view('login-hpass')->with('psn_code', $psn_code);
    }

    /**
     * 塾生アカウントログイン処理
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request, $schCode)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = Auth::guard(config('constants.GUARD_PERSON'))->user();
            session([config('constants.SESSION_KEY_PSNCODE') => $user->psn_code]);
            session([config('constants.SESSION_KEY_PSNNAME') => $user->name]);
            $person = session()->get(config('constants.SESSION_KEY_PSNINFO'));
            session([config('constants.SESSION_KEY_CLSCODE') => $person->cls_code]);
            session([config('constants.SESSION_KEY_CLSNAME') => $person->cls_name]);
            // メニュー画面
            $this->redirectTo = route('personTopMenu.index', ['schCode' => session()->get(config('constants.SESSION_KEY_SCHCODE'))]);

            return $this->sendLoginResponse($request);
        }

        $psn_code = $request->get('psn_code');

        // ログイン失敗時
        return back()->withErrors(['message' => trans('auth.failed')])->with('psn_code', $psn_code);
    }

    /**
     * 個人コード（認証で利用）
     *
     * @return string
     */
    public function username()
    {
        return 'psn_code';
    }

    /**
     * 認証に使う情報を塾コード、塾生コードとする
     */
    protected function credentials(Request $request): array
    {
        return $request->only($this->username(), 'sch_code', 'password');
    }

    /**
     * 塾生アカウント用のログアウト処理
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        // セッション情報を退避
        $sch_code = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $cls_code = $request->session()->get(config('constants.SESSION_KEY_CLSNAME')); // cls_name削除させる
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect(route('personLogin.index', ['schCode' => $sch_code]));
    }

    /**
     * 塾アカウント用ガード
     */
    protected function guard()
    {
        return Auth::guard(config('constants.GUARD_PERSON'));
    }

    /**
     * 塾生アカウント SSO ログイン処理
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ssologin(Request $request, $schCode)
    {
        try {
            switch ($schCode) {
                case config('constants.SSO_LOGIN')['WASEDA_ACADEMY']['SCH_CODE']: // 早稲アカの場合
                    $this->waseda_academy_sso_login($request, $schCode);
                    break;
                default: abort(401);
            }
        } catch (\Exception $e) {
            $this->error_log($request, 'config SCH_CODE エラー');
            abort(401);
        }

        if ($this->attemptLogin($request)) {
            $schools = $this->mSchoolService->selectOnebyKey((int) $schCode);
            $school = $schools->get(0);

            session([config('constants.SESSION_KEY_SCHCODE') => $school->sch_code]);
            session([config('constants.SESSION_KEY_SCHNAME') => $school->name]);
            session([config('constants.SESSION_KEY_SCHTESTNAME') => $school->testname]);
            session([config('constants.SESSION_KEY_PSN_HIDE_FLG') => $school->psn_hide_flg]);
            session([config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG') => $school->schname_hide_flg]);
            session([config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG') => $school->psncard_regist_flg]);
            session([config('constants.SESSION_KEY_PSN_PSW_CHANGE_FLG') => $school->psn_psw_update_flg]);
            session([config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG') => $school->psn_report_publish_flg]);
            session([config('constants.SESSION_KEY_SCHADMINKBN') => config('constants.ADMIN_KBN_GENERAL')]);

            $persons = $this->mPersonService->selectOneByKeyWithSchCls((int) $schCode, $request->psn_code);
            $person = $persons->get(0);
            session([config('constants.SESSION_KEY_PSNINFO') => $person]);

            $user = Auth::guard(config('constants.GUARD_PERSON'))->user();
            session([config('constants.SESSION_KEY_PSNCODE') => $user->psn_code]);
            session([config('constants.SESSION_KEY_PSNNAME') => $user->name]);

            $person = session()->get(config('constants.SESSION_KEY_PSNINFO'));
            session([config('constants.SESSION_KEY_CLSCODE') => $person->cls_code]);
            session([config('constants.SESSION_KEY_CLSNAME') => $person->cls_name]);

            // メニュー画面
            $this->redirectTo = route('personTopMenu.index', ['schCode' => session()->get(config('constants.SESSION_KEY_SCHCODE'))]);

            return $this->sendLoginResponse($request);
        } else {
            $this->error_log($request, 'ログインIDまたはパスワード不正');
            abort(401);
        }
    }

    /**
     * 早稲アカ SSO ログインパラメータ設定
     */
    private function waseda_academy_sso_login(object $request, string $schCode)
    {
        // 必須パラメータチェック
        if (! $request->has('login_id') || ! $request->has('password') || ! $request->has('tmp_key') || ! $request->has('hash_key')) {
            $this->error_log($request, 'パラメータ不正');
            abort(401);
        }

        // パラメータ妥当性チェック
        try {
            $secret_key = config('constants.SSO_LOGIN')['WASEDA_ACADEMY']['SECRET_KEY'];
        } catch (\Exception $e) {
            $this->error_log($request, 'config SECRET_KEY エラー');
            abort(401);
        }

        $login_id = $request->input('login_id'); // psn_code
        $password = $request->input('password');
        $tmp_key = $request->input('tmp_key');
        $hash_key = $request->input('hash_key');

        if (strcasecmp($hash_key, hash('sha256', $login_id.$tmp_key.$secret_key)) != 0) {
            $this->error_log($request, 'ハッシュキー不正');
            abort(401);
        }

        $content = ['sch_code' => $schCode, 'psn_code' => $login_id, 'password' => $password];
        $request->merge($content);
    }

    /**
     * 早稲アカ SSO 用エラーログ
     */
    private function error_log(object $request, string $msg)
    {
        Log::error($msg.' :'
            .' login_id : '.($request->input('login_id') ?? '') // psn_code
            .' password : '.($request->input('password') ?? '')
            .' tmp_key : '.($request->input('tmp_key') ?? '')
            .' hash_key : '.($request->input('hash_key') ?? '')
        );
    }

    /**
     * パスワード再設定画面にリダイレクト
     */
    public function passwordResetIndex(Request $request): RedirectResponse
    {
        return redirect()->route('personPasswordReset.index', ['sch_code' => $request->get('redirect_sch_code')])->with('psn_code', $request->get('redirect_psn_code'));
    }
}
