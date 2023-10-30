<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Utils\PasswordUtil;
use App\Services\MPersonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class PersonPasswordResetController extends Controller
{
    private $service;

    /**
     * コンストラクタ
     */
    public function __construct(MPersonService $mPersonService){
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
        if(!isset($psnCode)){
            return redirect(route('personIdCheck.index',['schCode'=>$schCode]));
        }

        return view('personPasswordReset')->with('psn_code',$psnCode);
    }

    /**
     * send a temp password through email.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function reset($schCode, Request $request)
    {
        $psnCode = $request->get('psn_code');
        if(!isset($psnCode)){
            return redirect(route('personIdCheck.index',['schCode'=>$schCode]));
        }

        // 仮パスワード生成
        $tempPassword = Crypt::encryptString(str_random(8));
        $decryptedTempPass = PasswordUtil::decryptString($tempPassword);

        // パスワード更新処理
        $id = $this->service->registerTempPassword($schCode,$psnCode,$tempPassword);


        // 生徒個人情報取得
        $person = $this->service->selectOneByKey($schCode, $psnCode)->first();
        if (empty($person)){
            return redirect(route('personIdCheck.index',['schCode'=>$schCode]));
        }
        $email= isset($person->email)? $person->email:'xxxxxx@gmail.com';
        $name = isset($person->name)? $person->name:'生徒1';


        Mail::to($email)->send(new ResetPasswordMail($email, $name, $decryptedTempPass));
        return view('personPasswordResetComplete')->with(['psn_code'=> $psnCode]);
 
    }
}
