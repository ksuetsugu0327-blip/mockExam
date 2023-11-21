<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SmasterCorrectionRequest;
use App\Services\MPersonService;
use Illuminate\Support\Facades\Crypt;
use App\Utils\RouteUtil;

class SmasterCorrectionController extends Controller {

    private $service;
    
    /**
     * コンストラクタ
     * @param MPersonService $mPersonService 塾マスタサービス
     */
    public function __construct(MPersonService $mPersonService){
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN').','.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_CLASS')
                .','.config('constants.GUARD_SURROGATE_SCHOOL').','.config('constants.GUARD_SURROGATE_CLASS'));
        $this->service = $mPersonService;
    }
    

    /**
     * 生徒マスタ・修正画面を表示する。
     *
     * @param schCode 塾コード(ログイン塾アカウント）
     * @param editSchCode 編集処理用塾コード
     * @param editPsnCode 編集処理用個人コード
     * @return view
     */
    public function index($schCode,$editSchCode,$editPsnCode) {
        
        // 塾マスタの取得
        $persons = $this->service->selectOneByKeyWithSchCls($editSchCode, $editPsnCode);
        // パスワード変更可能フラグをセッションから取得
        $psn_psw_update_flg = $persons->get(0)->psn_psw_update_flg;

        if($persons->count()==0){
            // 該当データが存在しない場合、データ不整合（削除されている等）のため、一覧に戻る
            return redirect()->route('persons.index',[$schCode])
                    ->with('message',trans('message.MPERSON_EXCLUSIVE_EXCEPTION'));
        }

        return view('smasterCorrection', ['person' => $persons->get(0), 'psn_psw_update_flg'=>$psn_psw_update_flg]);
    } 
    
    /**
     * 生徒マスタ更新処理を行う
     * @param SmasterCorrectionRequest $request
     * @param schCode 塾コード(ログイン塾アカウント）
     * @param editSchCode  編集処理用塾コード
     * @param editPsnCode  編集処理用個人コード
     * @return redirect
     */
    public function update(SmasterCorrectionRequest $request,$schCode,$editSchCode,$editPsnCode){
        // 画面情報を連想配列に設定
        $person=[
            'id'=>$request->get('person_id'),
            'sch_code'=>$editSchCode,
            'psn_code'=>$editPsnCode,
        ];
        if($request->session()->get(config('constants.SESSION_KEY_SCHADMINKBN'))==config('constants.ADMIN_KBN_ADMIN')){
            // 管理者の場合、以下更新項目の設定
            $person['cls_code']=$request->get('cls_code');
            $person['name']=$request->get('psn_name');
            $person['birthlow4']=$request->get('birthlow4');
            $person['postallow4']=$request->get('postallow4');
            $person['tellow4']=$request->get('tellow4');
        }

        // パスワード再設定用メールアドレス更新
        $email = $request->get('email');
        if (isset($email)){
            $person['mail_address']= $email;
        }
        
        // パスワード再発行判定
        $check = $request->get('temppasswordReissueCheck');
        if(isset($check)){
            // パスワードを追加
            $person['password']=null;
            $person['temppassword']=Crypt::encryptString(str_random(8));
        }
        
        // 更新処理
        $result = $this->service->update($person,$request->session()->get(config('constants.SESSION_KEY_SCHADMINKBN')));
        // 結果判定
        if($result['resultCode']==config('constants.RESULT_CODE_SYSTEM_ERROR')){
            // システムエラーの場合（TODO:暫定）
            abort(500);
        }
        
        // 一覧画面へリダイレクト
        return redirect()->route(RouteUtil::getPrefix().'.persons.index',['schCode'=>$schCode])->with('message',$result['message']);        
    }

    /**
     * 生徒マスタ削除処理を行う。
     * 
     * @param Request $request リクエスト
     * @param $delSchCode 塾コード
     * @param $delPsnCode 個人コード
     * @return redirect
     */
    public function delete(Request $request, $schCode,$editSchCode,$delPsnCode) {

        // 削除処理
        $result = $this->service->delete((int)$editSchCode,(string)$delPsnCode);
        if($result['resultCode']==config('constants.RESULT_CODE_SYSTEM_ERROR')){
            // システムエラーの場合（TODO:暫定）
            abort(500);
        }
        
        // 一覧画面へリダイレクト
        return redirect()->to(route(RouteUtil::getPrefix().'.persons.index',[$schCode]))->with('message',$result['message']);
    }
}
