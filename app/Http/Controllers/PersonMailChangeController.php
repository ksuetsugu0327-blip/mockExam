<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use App\Rules\EmailFormat;
use App\Services\MPersonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonMailChangeController extends Controller
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
        $person = $this->service->selectOneByKey($schCode, $psn_code)->first();

        return view('personMailChange')->with(['psn_code' => $psn_code, 'current_mail' => $person->mail_address]);
    }

    /**
     * Update email.
     */
    public function changeMail($schCode, Request $request): Renderable
    {
        $schCode = (int) $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $psnCode = (string) $request->get('psn_code');
        $mail_address = (string) $request->get('mail_address');

        // バリデーションチェック
        $validator = Validator::make($request->all(), [
            'mail_address' => ['required', new EmailFormat, 'max:50', 'confirmed'],
            'mail_address_confirmation' => 'required',
        ]);
        if ($validator->fails()) {
            $validator->errors()->add('message', trans('message.INPUT_ERROR_MESSAGE'));

            return back()->withErrors($validator)->with('psn_code', $psnCode);
        }

        // パスワード更新処理
        $id = $this->service->registerEmail($schCode, $psnCode, $mail_address);

        $this->redirectTo = route('personMailChange.index', ['sch_code' => $schCode]);

        return view('personMailChange')->with(['psn_code' => $psnCode, 'message' => 'メールアドレス変更しました', 'current_mail' => $mail_address]);
    }
}
