<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Http\Requests\PersonIDLoginCheckRequest;
use App\Services\MPersonService;
use App\Services\MSchoolService;
use App\Utils\SessionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 個人（生徒）アカウントログインID入力用コントローラ
 */
class PersonIDLoginCheckController extends Controller
{
    // サービスクラス(塾用)
    private $mSchoolService;

    // サービスクラス(個人用)
    private $mPersonService;

    /**
     * コンストラクタ
     *
     * @param  MSchoolService  $mSchoolService  塾マスタアクセス用インスタンス
     * @param  MPersonService  $mPersonService  個人マスタアクセス用インスタンス
     */
    public function __construct(MSchoolService $mSchoolService, MPersonService $mPersonService)
    {
        $this->mSchoolService = $mSchoolService;
        $this->mPersonService = $mPersonService;
    }

    /**
     * 画面初期表示
     *
     * @param  Request  $request  リクエスト
     * @param  type  $schCode  塾コード
     */
    public function index(Request $request, $schCode): View
    {
        $validator = Validator::make(['schCode' => $schCode], ['schCode' => 'required |digits_between:1,7']);
        if ($validator->fails()) {
            abort(404);
        }

        // 前回不正終了のケースを想定し、セッションをクリアする
        SessionUtil::InitializeSession($request);

        // 過去分ログイン情報がある可能性を考慮し、ログアウトを行う
        auth()->guard(config('constants.GUARD_ADMIN'))->logout();
        auth()->guard(config('constants.GUARD_SCHOOL'))->logout();
        auth()->guard(config('constants.GUARD_CLASS'))->logout();
        auth()->guard(config('constants.GUARD_PERSON'))->logout();

        // 塾コードをもとに、塾マスタから該当データを取得する
        $schools = $this->mSchoolService->selectOnebyKey((int) $schCode);
        if ($schools->count() == 0) {
            // 該当なしの場合、不正なアクセスの為 404エラーページへ
            abort(404);
        }
        $school = $schools->get(0);
        // 取得データをセッションに格納する
        session([config('constants.SESSION_KEY_SCHCODE') => $school->sch_code]);
        session([config('constants.SESSION_KEY_SCHNAME') => $school->name]);
        session([config('constants.SESSION_KEY_SCHTESTNAME') => $school->testname]);
        session([config('constants.SESSION_KEY_PSN_HIDE_FLG') => $school->psn_hide_flg]);
        session([config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG') => $school->schname_hide_flg]);
        session([config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG') => $school->psncard_regist_flg]);
        session([config('constants.SESSION_KEY_PSN_PSW_CHANGE_FLG') => $school->psn_psw_update_flg]);
        session([config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG') => $school->psn_report_publish_flg]);
        session([config('constants.SESSION_KEY_SCHADMINKBN') => config('constants.ADMIN_KBN_GENERAL')]);      // 管理権限は一般を設定

        return view('login-ID');
    }

    /**
     * ID妥当性チェック
     */
    public function check(PersonIDLoginCheckRequest $request)
    {
        // 対象の塾の生徒機能表示フラグが 1:非表示 だったらID不正メッセージを返却
        if (session(config('constants.SESSION_KEY_PSN_HIDE_FLG')) == config('constants.PSN_HIDE_FLG')) {
            // ID不正メッセージを返却
            return back()->withInput()->withErrors(['psn_code' => trans('message.PERSON_ID_NO_EXISTS')]);
        }

        // 個人マスタ検索
        $persons = $this->mPersonService->selectOneByKeyWithSchCls((int) $request->get('sch_code'), $request->get('psn_code'));
        if ($persons->count() == 0) {
            // ID不正メッセージを返却

            return back()->withInput()->withErrors(['psn_code' => trans('message.PERSON_ID_NO_EXISTS')]);
        }
        // 仮パスの設定有無を判定し、遷移先を変更する
        $person = $persons->get(0);
        session([config('constants.SESSION_KEY_PSNINFO') => $person]);

        $next = '';
        if (isset($person->temppassword)) {
            // 仮パスワードが設定されている場合、仮パスログイン画面に遷移
            $next = 'personTempLogin.index';
        } else {
            // 仮パスワードが未設定の場合、本パスログイン画面に遷移
            $next = 'personLogin.index';
        }

        return redirect()->route($next, ['sch_code' => $request->get('sch_code')])->with('psn_code', $person->psn_code);
    }
}
