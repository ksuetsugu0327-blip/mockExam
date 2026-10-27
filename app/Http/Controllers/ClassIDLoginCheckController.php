<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ClassIDLoginCheckRequest;
use App\Services\MClassService;
use App\Services\MSchoolService;
use App\Utils\SessionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 教室アカウントログイン用クラスIDチェック
 */
class ClassIDLoginCheckController extends Controller
{
    // サービスクラス
    private $service;

    private $mClassService;

    // 遷移先(暫定）
    protected $redirectTo = '';

    /**
     * コンストラクタ
     *
     * @param  MSchoolService  $mSchoolService  塾マスタアクセス用インスタンス
     * @param  MClassService  $mClassService  教室マスタアクセス用インスタンス
     */
    public function __construct(MSchoolService $mSchoolService, MClassService $mClassService)
    {
        $this->service = $mSchoolService;
        $this->mClassService = $mClassService;
    }

    /**
     * 画面初期表示
     *
     * @param  Request  $request  リクエスト
     * @param  type  $schCode  塾コード
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $schCode)
    {
        $validator = Validator::make(['schCode' => $schCode], ['schCode' => 'required |digits_between:1,7']);
        if ($validator->fails()) {
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
        $schools = $this->service->selectOnebyKey((int) $schCode);
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
        session([config('constants.SESSION_KEY_SCHADMINKBN') => config('constants.ADMIN_KBN_GENERAL')]);  // 管理権限は一般を設定
        session([config('constants.SESSION_KEY_CLS_GRADE_CSV_FLG') => $school->cls_grade_csv_flg]); // ★20240423 教室CSVダウンロードフラグ
        session([config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG') => $school->psn_report_publish_flg]);

        return view('login-cls-ID');
    }

    /**
     * ID妥当性チェック
     */
    public function check(ClassIDLoginCheckRequest $request)
    {
        /*
         * Todo：仮パスワード処理
         *
         * 1）教室マスタを検索し、レコードが存在しなければエラー（不正ID）を表示
         * 2）仮パスの設定有無を判定し、遷移先を変更
         *      仮パスワードが設定されている場合：仮パスログイン画面 => 新ルート tmplogin ->classTempLoginController::index)
         *      仮パスワードが設定されていない場合：本パスログイン画面 => 既存ルート：login -> ClassLoginController::index()
         *
         */

        $classes = $this->mClassService->selectOneByKeyWithSch((int) $request->get('sch_code'), (int) $request->get('cls_code'));
        if ($classes->count() == 0) {
            // ID不正メッセージを返却
            return back()->withInput()->withErrors(['cls_code' => trans('message.CLASS_CODE_NO_EXISTS')]);
        }

        $class = $classes->get(0);

        if (isset($class->temppassword)) {
            // 仮パスワードが設定されている場合、仮パスログイン画面に遷移
            $next = 'classTempLogin.index';
        } else {
            // 仮パスワードが未設定の場合、本パスログイン画面に遷移
            $next = 'classLogin.index';
        }

        return redirect()->route($next, ['sch_code' => $request->get('sch_code')])->with('cls_code', $request->get('cls_code'));
    }
}
