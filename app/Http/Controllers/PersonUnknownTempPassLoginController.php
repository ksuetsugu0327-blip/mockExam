<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MPersonService as MPersonService;
use Illuminate\Support\Facades\Validator as Validator;

/**
 * 仮パスワード不明時のログイン認証コントローラ
 */
class PersonUnknownTempPassLoginController extends Controller
{
    
    // サービスクラス(個人用)
    private $mPersonService;
    
    /**
     * コンストラクタ
     * @param MPersonService $mPersonService    個人マスタアクセス用インスタンス
     */
    public function __construct(MPersonService $mPersonService){
        $this->mPersonService = $mPersonService;
    }

    /**
     * 初期画面表示
     * @param Request $request リクエスト
     */
    public function index(Request $request){

        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        // フラッシュデータにない場合は、不正なアクセスの為、ログイン画面に戻す
        $psnCode = $request->session()->get('psn_code');
        if(!isset($psnCode)){
            return redirect(route('personIdCheck.index',['schCode'=>$schCode]));
        }
        
        $mode = $request->session()->has('mode')?$request->session()->get('mode'):null;

        // 個人コードが存在しない場合、不正なアクセスの為、ログイン画面に戻す
        if(!isset($psnCode)){
            return redirect(route('personIdCheck.index',['schCode'=>$schCode]));
        }

        // 処理モード  0:生年月日、郵便番号  1:生年月日、電話番号 2:郵便番号、電話番号
        if(!isset($mode)){
            $mode = rand(0,2);
        }
        return view('login-unknown')->with('psn_code',$psnCode)->with('mode',$mode);
    }    
    
    /**
     * 認証情報での仮ログイン
     * @param PersonIDLoginCheckRequest $request
     */
    public function login(Request $request){

        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $psnCode = $request->get('psn_code');
        $mode = $request->get('mode');
       
        // 個人マスタ検索
        $persons = $this->mPersonService->selectOneByKey((int)$schCode, (string)$psnCode);
        if($persons->count() == 0){
            // ありえないので、発生時はログイン画面に強制的に戻す
            return redirect(route('personIdCheck.index',['schCode'=>$schCode]));
        }
        $person = $persons->get(0);
        
        if($mode==0){
            // 0:生年月日、郵便番号
            $birthlow4 = (string)$request->get('birthlow4');
            $postallow4 = (string)$request->get('postallow4');
            $validator = Validator::make($request->all(),[
                'birthlow4' => 'required|birth_low4',
                'postallow4' => 'required|postal_low4']);
            if($validator->fails()){
                $validator->errors()->add('message',trans('message.INPUT_ERROR_MESSAGE'));
                return back()->withInput()->withErrors($validator)->with('psn_code',$psnCode)->with('mode',$mode);
            }
            
            if(!(strcmp($person->birthlow4,$birthlow4) == 0 && strcmp($person->postallow4,$postallow4) == 0)){
                // 認証エラー
                return back()->withInput()->withErrors(array('message'=>trans('auth.failed')))->with('psn_code',$psnCode)->with('mode',$mode);
            }            
        }else if($mode==1){
            // 1:生年月日、電話番号
            $birthlow4 = (string)$request->get('birthlow4');
            $tellow4 = (string)$request->get('tellow4');

            $validator = Validator::make($request->all(),[
                'birthlow4' => 'required|birth_low4',
                'tellow4' => 'required|tel_low4']);
            if($validator->fails()){
                $validator->errors()->add('message',trans('message.INPUT_ERROR_MESSAGE'));
                return back()->withInput()->withErrors($validator)->with('psn_code',$psnCode)->with('mode',$mode);
            }

            if((!isset($person->postallow4) || !isset($person->tellow4)) || !(strcmp($person->birthlow4,$birthlow4) == 0 && strcmp($person->tellow4,$tellow4) == 0 )){
                // 認証エラー
                return back()->withInput()->withErrors(array('message'=>trans('auth.failed')))->with('psn_code',$psnCode)->with('mode',$mode);
            }
            
        }else{
            // 2:郵便番号、電話番号
            $postallow4 = (string)$request->get('postallow4');
            $tellow4 = (string)$request->get('tellow4');
            
            $validator = Validator::make($request->all(),[
                'postallow4' => 'required|postal_low4',
                'tellow4' => 'required|tel_low4']);
            if($validator->fails()){
                $validator->errors()->add('message',trans('message.INPUT_ERROR_MESSAGE'));
                return back()->withInput()->withErrors($validator)->with('psn_code',$psnCode)->with('mode',$mode);
            }

            if((!isset($person->postallow4) || !isset($person->tellow4)) || !(strcmp($person->postallow4,$postallow4) == 0 && strcmp($person->tellow4,$tellow4) == 0 )){
                // 認証エラー
                return back()->withInput()->withErrors(array('message'=>trans('auth.failed')))->with('psn_code',$psnCode)->with('mode',$mode);;
            }            
        }
        
        return redirect()->route('changePassword.index',['sch_code'=>$schCode])->with('psn_code',$psnCode);
    }
    


}
