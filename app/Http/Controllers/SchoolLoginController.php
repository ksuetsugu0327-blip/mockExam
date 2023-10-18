<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\RouteUtil;
use App\Services\MSchoolService as MSchoolService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;


/**
 * 塾アカウント用ログインコントローラ
 */
class SchoolLoginController extends Controller
{
    // Laravel標準認証trait
    use AuthenticatesUsers;

    // サービスクラス
    private $service;
    // 遷移先
    protected $redirectTo = '';

    /**
     * コンストラクタ
     * @param MSchoolService $mSchoolService    塾マスタアクセス用インスタンス
     */
    public function __construct(MSchoolService $mSchoolService){
        $this->service = $mSchoolService;
    }

    /**
     * 画面初期表示
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$schCode)
    {
        // フラッシュデータにない場合は、不正なアクセスの為、ログイン画面に戻す
        $sch_code = session()->get('sch_code');
        if(!isset($sch_code)){
            return redirect(route('schoolIdCheck.index',['schCode'=>$schCode]));
        }

        return view("login-sch")->with('sch_code',$sch_code);
    }

    /**
     * ログイン処理
     * @param Request $request リクエスト
     * @return type
     */
    public function login(Request $request,$schCode)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $this->redirectTo = route('schoolTopMenu.index',['schCode'=>$schCode]);
            return $this->sendLoginResponse($request);
        }

        // ログイン失敗時
        return back()->withErrors(array('message'=>trans('auth.failed')))->with('sch_code',$request->get('sch_code'));
    }

    /**
     * 塾コード（認証で利用）
     *
     * @return string
     */
    public function username()
    {
        return 'sch_code';
    }

    /**
     * 塾アカウント用のログアウト処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        // セッション情報を退避
        $sch_code = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect(route('schoolIdCheck.index',['schCode'=>$sch_code]));
    }

    /**
     * 塾アカウント用ガード
     */
    protected function guard(){
        // セッションの管理者区分を判定
        if(session()->get(config('constants.SESSION_KEY_SCHADMINKBN'))==config('constants.ADMIN_KBN_ADMIN')){
            // 管理者権限ユーザの場合
            return Auth::guard(config('constants.GUARD_ADMIN'));
        }

        return Auth::guard(config('constants.GUARD_SCHOOL'));
    }


}
