<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use App\Services\MPersonService;
use App\Utils\PasswordUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class PersonPasswordResetController extends Controller
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
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($schCode)
    {
        // フラッシュデータにない場合は、不正なアクセスの為、ログイン画面に戻す
        $psnCode = session()->get('psn_code');
        if (! isset($psnCode) && ! session()->has('mail_reset_failed')) {
            return redirect(route('personIdCheck.index', ['schCode' => $schCode]));
        }

        return view('personPasswordReset')->with('psn_code', $psnCode);
    }

    /**
     * send a temp password through email.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function reset($schCode, Request $request)
    {
        $psnCode = $request->get('psn_code');
        if (! isset($psnCode)) {
            return redirect(route('personIdCheck.index', ['schCode' => $schCode]));
        }
        // 生徒個人情報取得
        $person = $this->service->selectOneByKey($schCode, $psnCode)->first();
        if (empty($person)) {
            return redirect(route('personIdCheck.index', ['schCode' => $schCode]));
        }

        if (isset($person->mail_address)) {
            $email = $person->mail_address;
        } else {
            return back()->with(['mail_reset_failed' => 'メールアドレスは設定されていません']);
        }
        $name = $person->name;

        // 仮パスワード生成
        $tempPassword = Crypt::encryptString(Str::random(8));
        $decryptedTempPass = PasswordUtil::decryptString($tempPassword);

        // パスワード更新処理
        $id = $this->service->registerTempPassword($schCode, $psnCode, $tempPassword);

        Mail::to($email)->send(new ResetPasswordMail($email, $name, $decryptedTempPass));

        return view('personPasswordResetComplete')->with(['psn_code' => $psnCode]);

    }
}
