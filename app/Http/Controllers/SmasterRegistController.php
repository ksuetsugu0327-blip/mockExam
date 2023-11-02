<?php

namespace App\Http\Controllers;

use App\Http\Requests\SmasterRegistRequest;
use App\Services\MSchoolService;
use App\Services\MPersonService;
use Illuminate\Support\Facades\Crypt;

class SmasterRegistController extends Controller {

    /**
     * コンストラクタ
     * @param MPersonService $mPersonService 個人マスタサービス
     */
    public function __construct(MPersonService $mPersonService, MSchoolService $mSchoolService){
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->service = $mPersonService;
        $this->mSchoolService = $mSchoolService;
    }

    /**
     * 生徒マスタ・登録画面を表示する。
     * @return view
     */
    public function index() {
        return view('smasterRegist');
    }

    /**
     * 生徒マスタ登録処理を行う。
     * 
     * @param SmasterRegistRequest $request 生徒マスタ登録用リクエスト
     * @return redirect
     */
    public function store(SmasterRegistRequest $request) {

        // 画面情報を連想配列に設定
        $person=[
            'sch_code'=>$request->get('sch_code'),
            'cls_code'=>$request->get('cls_code'),
            'psn_code'=>$request->get('psn_code'),
            'name'=>$request->get('psn_name'),
            'birthlow4'=>$request->get('birthlow4'),
            'postallow4'=>$request->get('postallow4'),
            'tellow4'=>$request->get('tellow4'),
            'password'=>null,
            'temppassword'=>  Crypt::encryptString(str_random(8))
        ];

        $school = $this->mSchoolService->selectOneByKey($request->get('sch_code'))->first();

        // パスワード再設定用メールアドレス追加
        $mail_address = $request->get('mail_address');
        if (!empty($mail_address)){
            if (!$school->psn_psw_update_flg){
                return back()->with('mail_update_failed','生徒パスワードが変更可能な塾ではないので、メールアドレスは入力できません。')->withInput();
            }
            $person['mail_address'] =  $mail_address;
        }
        
        // 登録処理
        $result = $this->service->register($person);
        // 結果判定
        if($result['resultCode']==-1){
            // システムエラーの場合、abort
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->route('sch.persons.index',['schCode'=>$request->session()->get(config('constants.SESSION_KEY_SCHCODE'))])
                         ->with('message',$result['message']);
    }

}
