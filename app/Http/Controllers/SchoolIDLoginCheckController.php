<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MSchoolService as MSchoolService;
use Illuminate\Support\Facades\Validator as Validator;
use App\Utils\SessionUtil;
use App\Http\Requests\SchoolIDLoginCheckRequest;



/**
 * 塾アカウント用ログインコントローラ
 */
class SchoolIDLoginCheckController extends Controller
{
    
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
     * @param type $request                 リクエスト
     * @param type $schCode                 塾コード
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$schCode)
    {
        $validator = Validator::make(['schCode'=>(int)$schCode],['schCode' => 'required |digits_between:1,7']);
        if($validator->fails()){
            abort(404);
        }
        
        // 前回不正終了のケースを想定し、セッションをクリアする
        SessionUtil::InitializeSession($request);
        
        // 過去分ログイン情報がある可能性を考慮し、ログアウトを行う(別ガードも考慮）
        auth()->guard(config('constants.GUARD_ADMIN'))->logout();
        auth()->guard(config('constants.GUARD_SCHOOL'))->logout();
        auth()->guard(config('constants.GUARD_CLASS'))->logout();
        auth()->guard(config('constants.GUARD_PERSON'))->logout();
        
        // 塾コードをもとに、塾マスタから該当データを取得する
        $schools = $this->service->selectOnebyKey((int)$schCode);
        if($schools->count() == 0 ){
            // 該当なしの場合、不正なアクセスの為 404エラーページへ
            abort(404);
        }
        $school = $schools->get(0);

        // 取得データをセッションに格納する
        session([config('constants.SESSION_KEY_SCHCODE')=>$school->sch_code]);
        session([config('constants.SESSION_KEY_SCHNAME')=>$school->name]);
        session([config('constants.SESSION_KEY_SCHTESTNAME')=>$school->testname]);
        session([config('constants.SESSION_KEY_SCHADMINKBN')=>$school->admin_kbn]);        
        session([config('constants.SESSION_KEY_PSN_HIDE_FLG')=>$school->psn_hide_flg]);        
        session([config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG')=>$school->schname_hide_flg]);        
        session([config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG')=>$school->psncard_regist_flg]);
        
        return view("login-sch-ID");
    }

    /**
     * ID妥当性チェック
     * @param SchoolIDLoginCheckRequest $request
     */
    public function check(SchoolIDLoginCheckRequest $request,$schCode){
        return redirect()->route('schoolLogin.index',['sch_code'=>$request->get('sch_code')])
                         ->with('sch_code',$request->get('sch_code'));
    }    

}
