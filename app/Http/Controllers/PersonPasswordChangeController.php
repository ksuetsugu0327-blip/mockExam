<?php

namespace App\Http\Controllers;

use App\Rules\PasswordCheck;
use App\Services\MPersonService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonPasswordChangeController extends Controller
{
    private $service;

    /**
     * コンストラクタ
     */
    public function __construct(MPersonService $mPersonService)
    {
        $this->service = $mPersonService;
    }

    /**
     * Show the password reset page.
     */
    public function index($schCode): Renderable
    {
        $psn_code = session()->get(config('constants.SESSION_KEY_PSNCODE'));
        // dd(session()->all());
        if (! isset($psn_code)) {
            return redirect(route('personIdCheck.index', ['schCode' => $schCode]));
        }

        return view('personPasswordChange')->with('psn_code', $psn_code);
    }

    /**
     * Update password.
     */
    public function changePassword($schCode, Request $request): Renderable
    {
        $schCode = (int) $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $psnCode = (string) $request->get('psn_code');
        $current = (string) $request->get('current_password');
        $password = (string) $request->get('password');

        // バリデーションチェック
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', new PasswordCheck($schCode, $psnCode)],
            'password' => 'required|string|regex:/^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])[!-~]{12,50}$/|confirmed',
            'password_confirmation' => 'required']);
        if ($password == $current) {
            return back()->with('same_as_current', '現在のパスワードと同じパスワードは入力できません。')->withInput();
        }
        if ($validator->fails()) {
            $validator->errors()->add('message', trans('message.INPUT_ERROR_MESSAGE'));

            return back()->withErrors($validator)->with('psn_code', $psnCode)->withInput();
        }

        // パスワード更新処理
        $id = $this->service->registerPassword($schCode, $psnCode, $password);

        $this->redirectTo = route('personTopMenu.index', ['sch_code' => $schCode]);

        return view('personPasswordChange')->with(['psn_code' => $psnCode, 'message' => 'パスワード変更しました']);

        // // ログイン失敗時
        // return back()->withErrors(array('message'=>trans('auth.failed')))->with('psn_code',$psn_code);
    }
}
