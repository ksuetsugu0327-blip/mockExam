<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Rules\EmailFormat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Services\MPersonService as MPersonService;
use Illuminate\Support\Facades\Validator;
//use App\Http\Requests\ChangePasswordRegistPost as ChangePasswordRegistPost;

/**
 * 個人用パスワード変更用コントローラ
 */
class ChangePasswordController extends Controller
{
    // Laravel標準認証trait
    use AuthenticatesUsers;
    
    private $service;
    
    // 遷移先(暫定）
    protected $redirectTo = '';
    
    /**
     * コンストラクタ
     */
    public function __construct(MPersonService $mPersonService){
        $this->service = $mPersonService;
    }

    /**
     * 初期表示処理
     * @param type $schCode 塾コード
     * @return type 
     */
    public function index($schCode){
        // フラッシュデータにない場合は、不正なアクセスの為、ログイン画面に戻す
        $psn_code = session()->get('psn_code');
        // パスワード変更可能フラグをセッションから取得
        $psn_psw_update_flg = session()->get(config('constants.SESSION_KEY_PSN_PSW_CHANGE_FLG'));

        if(!isset($psn_code)){
            return redirect(route('personIdCheck.index',['schCode'=>$schCode]));
        }
        return view('password')->with(['psn_code'=>$psn_code, 'psn_psw_update_flg'=>$psn_psw_update_flg]);
    }
    
   /**
     * パスワード変更登録処理
     * @param ValidateRequest $request
     * @return string
     */
    public function register(Request $request)
    {
        $schCode = (int)$request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $psnCode = (string)$request->get('psn_code');
        $password = (string)$request->get('password');
        $rules = [
            'password' => 'required|string|regex:/^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])[!-~]{12,50}$/|confirmed',
            'password_confirmation' => 'required'
        ];

        if($request->has('email')){
            $email = (string)$request->get('email');
            $emailRules = ['email' => ['required', new EmailFormat(), 'max:50', 'confirmed'], 
                        'email_confirmation' => 'required'];
            $rules = array_merge($rules, $emailRules);
        }

        // バリデーションチェック
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            $validator->errors()->add('message',trans('message.INPUT_ERROR_MESSAGE'));
            return back()->withErrors($validator)->with('psn_code',$psnCode)->withInput();
        }
        
        // パスワード更新処理
        $id = $this->service->registerPassword($schCode,$psnCode,$password);

        // メールアドレス更新処理
        if (isset($email)){
            $this->service->registerEmail($schCode,$psnCode,$email);
        }
        
        // ログイン処理
        $this->guard()->loginUsingId($id);

        $user = Auth::guard(config('constants.GUARD_PERSON'))->user();
        session([config('constants.SESSION_KEY_PSNCODE')=>$user->psn_code]);
        session([config('constants.SESSION_KEY_PSNNAME')=>$user->name]);            
        $person = session()->get(config('constants.SESSION_KEY_PSNINFO'));
        session([config('constants.SESSION_KEY_CLSCODE')=>$person->cls_code]);
        session([config('constants.SESSION_KEY_CLSNAME')=>$person->cls_name]);
        
        $this->redirectTo = route('personTopMenu.index',['sch_code'=>$schCode]);

        return $this->sendLoginResponse($request);
    }
    
    /**
     * 塾生アカウント用ガード
     */
    protected function guard(){
        return Auth::guard(config('constants.GUARD_PERSON'));
    }    

}
