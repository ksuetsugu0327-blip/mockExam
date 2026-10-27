<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Services\MClassService;
use App\Services\MPersonService;
use App\Services\MSchoolService;
use Illuminate\Http\Request;

/**
 * 代理ログイン用コントローラ
 */
class SurrogateLoginController extends Controller
{
    private $mSchoolService;

    private $mClassService;

    private $mPersonService;

    /**
     * コンストラクタ
     *
     * @param  MSchoolService  $mSchoolService  塾マスタサービス
     * @param  MClassService  $mClassService  教室マスタサービス
     * @param  MPersonService  $mPersonService  個人マスタサービス
     */
    public function __construct(MSchoolService $mSchoolService, MClassService $mClassService, MPersonService $mPersonService)
    {
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->mSchoolService = $mSchoolService;
        $this->mClassService = $mClassService;
        $this->mPersonService = $mPersonService;
    }

    /**
     * 塾代理ログインを行う
     *
     * @param  type  $schCode  塾コード(管理者ログイン)
     * @param  type  $loginSchCode  代理ログイン塾コード
     */
    public function schoolLogin(Request $request, $schCode, $loginSchCode): RedirectResponse
    {

        // 代理ログイン先の塾情報を取得する
        $schools = $this->mSchoolService->selectOneByKey((int) $loginSchCode);
        if (count($schools) == 0) {
            // 該当なしの場合（処理中に別処理で削除されるケース）、一覧画面へリダイレクト
            return redirect()->to(route('schools.index', ['schCode' => $schCode]))->with('message', trans('message.MSCHOOL_EXCLUSIVE_EXCEPTION', ['sch_code' => $loginSchCode]));
        }
        $school = $schools->get(0);

        // セッションに管理者情報を退避させる
        if ($request->session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) {
            // 代理ログイン済みの場合、エラーとして一覧画面へリダイレクト（ありえない想定）
            return redirect()->to(route('schools.index', ['schCode' => $schCode]))->with('message', trans('message.ALREADY_SURROGATE_LOGINDED'));
        }

        // 今回管理者の情報を連想配列として格納
        $adminInfo = [
            config('constants.SESSION_KEY_SCHCODE') => $request->session()->get(config('constants.SESSION_KEY_SCHCODE')),
            config('constants.SESSION_KEY_SCHNAME') => $request->session()->get(config('constants.SESSION_KEY_SCHNAME')),
            config('constants.SESSION_KEY_SCHADMINKBN') => $request->session()->get(config('constants.SESSION_KEY_SCHADMINKBN')),
            config('constants.SESSION_KEY_SCHTESTNAME') => $request->session()->get(config('constants.SESSION_KEY_SCHTESTNAME')),
            config('constants.SESSION_KEY_MSCHOOL_SEARCH_CONDITION') => $request->session()->get(config('constants.SESSION_KEY_MSCHOOL_SEARCH_CONDITION')),
            config('constants.SESSION_KEY_PSN_HIDE_FLG') => $request->session()->get(config('constants.SESSION_KEY_PSN_HIDE_FLG')),
            config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG') => $request->session()->get(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG')),
            config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG') => $request->session()->get(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG')),
            config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG') => $request->session()->get(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')),
        ];
        $request->session()->put(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'), $adminInfo);

        // 代理ログイン実施フラグの設定
        $request->session()->put(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'), config('constants.SURROGATE_LOGINED'));
        // 代理ログイン情報を設定する
        $request->session()->put(config('constants.SESSION_KEY_SCHCODE'), $school->sch_code);
        $request->session()->put(config('constants.SESSION_KEY_SCHNAME'), $school->name);
        $request->session()->put(config('constants.SESSION_KEY_SCHTESTNAME'), $school->testname);
        $request->session()->put(config('constants.SESSION_KEY_SCHADMINKBN'), $school->admin_kbn);
        $request->session()->put(config('constants.SESSION_KEY_PSN_HIDE_FLG'), $school->psn_hide_flg);
        $request->session()->put(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG'), $school->schname_hide_flg);
        $request->session()->put(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG'), $school->psncard_regist_flg);
        $request->session()->put(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG'), $school->psn_report_publish_flg);
        // セッションの塾マスタ検索条件をクリア
        $request->session()->forget(config('constants.SESSION_KEY_MSCHOOL_SEARCH_CONDITION'));

        // メニュー画面へリダイレクト
        return redirect()->to(route('schoolTopMenu.index', ['schCode' => $school->sch_code]));
    }

    /**
     * 塾代理ログアウトを行う
     */
    public function schoolLogout(Request $request): RedirectResponse
    {
        // 代理ログイン前のセッション情報を取得する
        $adminInfo = $request->session()->get(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'));

        // ログイン情報を元の情報に戻す
        $request->session()->put(config('constants.SESSION_KEY_SCHCODE'), $adminInfo[config('constants.SESSION_KEY_SCHCODE')]);
        $request->session()->put(config('constants.SESSION_KEY_SCHNAME'), $adminInfo[config('constants.SESSION_KEY_SCHNAME')]);
        $request->session()->put(config('constants.SESSION_KEY_SCHTESTNAME'), $adminInfo[config('constants.SESSION_KEY_SCHTESTNAME')]);
        $request->session()->put(config('constants.SESSION_KEY_SCHADMINKBN'), $adminInfo[config('constants.SESSION_KEY_SCHADMINKBN')]);
        $request->session()->put(config('constants.SESSION_KEY_MSCHOOL_SEARCH_CONDITION'), $adminInfo[config('constants.SESSION_KEY_MSCHOOL_SEARCH_CONDITION')]);
        $request->session()->put(config('constants.SESSION_KEY_PSN_HIDE_FLG'), $adminInfo[config('constants.SESSION_KEY_PSN_HIDE_FLG')]);
        $request->session()->put(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG'), $adminInfo[config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG')]);
        $request->session()->put(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG'), $adminInfo[config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG')]);
        $request->session()->put(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG'), $adminInfo[config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')]);

        // 代理ログイン情報をクリアする
        $request->session()->forget(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'));
        $request->session()->forget(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'));

        // 塾マスタ一覧画面へリダイレクト
        return redirect()->to(route('schools.index', ['schCode' => $adminInfo[config('constants.SESSION_KEY_SCHCODE')]]))->with(config('constants.SESSION_KEY_IS_SURROGATE_LOGOUT'), true);

    }

    /**
     * 教室代理ログインを行う
     *
     * @param  type  $schCode  塾コード(管理者ログイン)
     * @param  type  $loginSchCode  代理ログイン塾コード
     * @param  type  $loginClsCode  代理ログイン教室コード
     */
    public function classLogin(Request $request, $schCode, $loginSchCode, $loginClsCode): RedirectResponse
    {

        // 代理ログイン先の塾情報を取得する
        $schools = $this->mSchoolService->selectOneByKey((int) $loginSchCode);
        if (count($schools) == 0) {
            // 該当なしの場合（処理中に別処理で削除されるケース）、一覧画面へリダイレクト
            return redirect()->to(route('schools.index', ['schCode' => $schCode]))->with('message', trans('message.MSCHOOL_EXCLUSIVE_EXCEPTION', ['sch_code' => $loginSchCode]));
        }
        $school = $schools->get(0);

        // 代理ログイン先の教室情報を取得する
        $classes = $this->mClassService->selectOneByKeyWithSch((int) $loginSchCode, (int) $loginClsCode);
        if (count($classes) == 0) {
            // 該当なしの場合（処理中に別処理で削除されるケース）、一覧画面へリダイレクト
            return redirect()->to(route('classes.index', ['schCode' => $schCode]))->with('message', trans('message.MCLASS_EXCLUSIVE_EXCEPTION', ['sch_code' => $loginSchCode, 'cls_code' => $loginClsCode]));
        }
        $class = $classes->get(0);

        // セッションに管理者情報を退避させる
        if ($request->session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'))) {
            // 代理ログイン済みの場合、エラーとして一覧画面へリダイレクト
            return redirect()->to(route('classes.index', ['schCode' => $schCode]))->with('message', trans('message.ALREADY_SURROGATE_LOGINDED'));
        }

        // 今回管理者の情報を連想配列として格納
        $adminInfo = [
            config('constants.SESSION_KEY_SCHCODE') => $request->session()->get(config('constants.SESSION_KEY_SCHCODE')),
            config('constants.SESSION_KEY_SCHNAME') => $request->session()->get(config('constants.SESSION_KEY_SCHNAME')),
            config('constants.SESSION_KEY_SCHADMINKBN') => $request->session()->get(config('constants.SESSION_KEY_SCHADMINKBN')),
            config('constants.SESSION_KEY_SCHTESTNAME') => $request->session()->get(config('constants.SESSION_KEY_SCHTESTNAME')),
            config('constants.SESSION_KEY_MCLASS_SEARCH_CONDITION') => $request->session()->get(config('constants.SESSION_KEY_MCLASS_SEARCH_CONDITION')),
            config('constants.SESSION_KEY_PSN_HIDE_FLG') => $request->session()->get(config('constants.SESSION_KEY_PSN_HIDE_FLG')),
            config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG') => $request->session()->get(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG')),
            config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG') => $request->session()->get(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG')),
            config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG') => $request->session()->get(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')),
        ];
        $request->session()->put(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'), $adminInfo);

        // 代理ログイン実施フラグの設定
        $request->session()->put(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'), config('constants.SURROGATE_LOGINED'));
        // 代理ログイン情報を設定する
        $request->session()->put(config('constants.SESSION_KEY_SCHCODE'), $class->sch_code);
        $request->session()->put(config('constants.SESSION_KEY_SCHNAME'), $class->sch_name);
        $request->session()->put(config('constants.SESSION_KEY_SCHTESTNAME'), $class->testname);
        $request->session()->put(config('constants.SESSION_KEY_CLSCODE'), $class->cls_code);
        $request->session()->put(config('constants.SESSION_KEY_CLSNAME'), $class->cls_name);
        $request->session()->put(config('constants.SESSION_KEY_SCHADMINKBN'), config('constants.ADMIN_KBN_GENERAL'));
        $request->session()->put(config('constants.SESSION_KEY_PSN_HIDE_FLG'), $class->psn_hide_flg);
        $request->session()->put(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG'), $class->schname_hide_flg);
        $request->session()->put(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG'), $class->psncard_regist_flg);
        $request->session()->put(config('constants.SESSION_KEY_CLS_GRADE_CSV_FLG'), $class->cls_grade_csv_flg);
        $request->session()->put(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG'), $school->psn_report_publish_flg);

        // セッションの教室マスタ検索条件をクリア
        $request->session()->forget(config('constants.SESSION_KEY_MCLASS_SEARCH_CONDITION'));

        // メニュー画面へリダイレクト
        return redirect()->to(route('classTopMenu.index', ['schCode' => $class->sch_code]));
    }

    /**
     * 教室代理ログアウトを行う
     */
    public function classLogout(Request $request): RedirectResponse
    {
        // 代理ログイン前のセッション情報を取得する
        $adminInfo = $request->session()->get(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'));

        // ログイン情報を元の情報に戻す
        $request->session()->put(config('constants.SESSION_KEY_SCHCODE'), $adminInfo[config('constants.SESSION_KEY_SCHCODE')]);
        $request->session()->put(config('constants.SESSION_KEY_SCHNAME'), $adminInfo[config('constants.SESSION_KEY_SCHNAME')]);
        $request->session()->put(config('constants.SESSION_KEY_SCHTESTNAME'), $adminInfo[config('constants.SESSION_KEY_SCHTESTNAME')]);
        $request->session()->put(config('constants.SESSION_KEY_SCHADMINKBN'), $adminInfo[config('constants.SESSION_KEY_SCHADMINKBN')]);
        $request->session()->put(config('constants.SESSION_KEY_MCLASS_SEARCH_CONDITION'), $adminInfo[config('constants.SESSION_KEY_MCLASS_SEARCH_CONDITION')]);
        $request->session()->put(config('constants.SESSION_KEY_PSN_HIDE_FLG'), $adminInfo[config('constants.SESSION_KEY_PSN_HIDE_FLG')]);
        $request->session()->put(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG'), $adminInfo[config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG')]);
        $request->session()->put(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG'), $adminInfo[config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG')]);
        $request->session()->put(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG'), $adminInfo[config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')]);

        // 代理ログイン情報をクリアする
        $request->session()->forget(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'));
        $request->session()->forget(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'));
        $request->session()->forget(config('constants.SESSION_KEY_CLSCODE'));
        $request->session()->forget(config('constants.SESSION_KEY_CLSNAME'));

        // 教室マスタ一覧画面へリダイレクト
        return redirect()->to(route('classes.index', ['schCode' => $adminInfo[config('constants.SESSION_KEY_SCHCODE')]]))->with(config('constants.SESSION_KEY_IS_SURROGATE_LOGOUT'), true);

    }

    /**
     * 生徒ログインを行う
     *
     * @param  type  $schCode  塾コード(管理者ログイン)
     * @param  type  $loginSchCode  代理ログイン塾コード
     * @param  type  $loginPsnCode  代理ログイン生徒コード
     */
    public function personLogin(Request $request, $schCode, $loginSchCode, $loginPsnCode): RedirectResponse
    {

        // 代理ログイン先の生徒情報を取得する
        $persons = $this->mPersonService->selectOneByKeyWithSchCls((int) $loginSchCode, $loginPsnCode);
        if (count($persons) == 0) {
            // 該当なしの場合（処理中に別処理で削除されるケース）、一覧画面へリダイレクト
            return redirect()->to(route('sch.persons.index', ['schCode' => $schCode]))->with('message', trans('message.MPERSON_EXCLUSIVE_EXCEPTION', ['sch_code' => $loginSchCode, 'psn_code' => $loginPsnCode]));
        }
        $person = $persons->get(0);

        // セッションに管理者情報を退避させる
        if ($request->session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_PERSON'))) {
            // 代理ログイン済みの場合、エラーとして一覧画面へリダイレクト
            return redirect()->to(route('sch.persons.index', ['schCode' => $schCode]))->with('message', trans('message.ALREADY_SURROGATE_LOGINDED'));
        }

        // 今回管理者の情報を連想配列として格納
        $adminInfo = [
            config('constants.SESSION_KEY_SCHCODE') => $request->session()->get(config('constants.SESSION_KEY_SCHCODE')),
            config('constants.SESSION_KEY_SCHNAME') => $request->session()->get(config('constants.SESSION_KEY_SCHNAME')),
            config('constants.SESSION_KEY_SCHADMINKBN') => $request->session()->get(config('constants.SESSION_KEY_SCHADMINKBN')),
            config('constants.SESSION_KEY_SCHTESTNAME') => $request->session()->get(config('constants.SESSION_KEY_SCHTESTNAME')),
            config('constants.SESSION_KEY_MPERSON_SEARCH_CONDITION') => $request->session()->get(config('constants.SESSION_KEY_MPERSON_SEARCH_CONDITION')),
            config('constants.SESSION_KEY_PSN_HIDE_FLG') => $request->session()->get(config('constants.SESSION_KEY_PSN_HIDE_FLG')),
            config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG') => $request->session()->get(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG')),
            config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG') => $request->session()->get(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG')),
            config('constants.SESSION_KEY_PSN_PSW_CHANGE_FLG') => $request->session()->get(config('constants.SESSION_KEY_PSN_PSW_CHANGE_FLG')),
            config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG') => $request->session()->get(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')),
        ];
        $request->session()->put(config('constants.SESSION_KEY_SURROGATE_LOGIN_PERSON'), $adminInfo);

        // 代理ログイン実施フラグの設定
        $request->session()->put(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'), config('constants.SURROGATE_LOGINED'));
        // 代理ログイン情報を設定する
        $request->session()->put(config('constants.SESSION_KEY_SCHCODE'), $person->sch_code);
        $request->session()->put(config('constants.SESSION_KEY_SCHNAME'), $person->sch_name);
        $request->session()->put(config('constants.SESSION_KEY_SCHTESTNAME'), $person->testname);
        $request->session()->put(config('constants.SESSION_KEY_CLSCODE'), $person->cls_code);
        $request->session()->put(config('constants.SESSION_KEY_CLSNAME'), $person->cls_name);
        $request->session()->put(config('constants.SESSION_KEY_SCHADMINKBN'), config('constants.ADMIN_KBN_GENERAL'));
        $request->session()->put(config('constants.SESSION_KEY_PSNCODE'), $person->psn_code);
        $request->session()->put(config('constants.SESSION_KEY_PSNNAME'), $person->psn_name);
        $request->session()->put(config('constants.SESSION_KEY_PSN_HIDE_FLG'), $person->psn_hide_flg);
        $request->session()->put(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG'), $person->schname_hide_flg);
        $request->session()->put(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG'), $person->psncard_regist_flg);
        $request->session()->put(config('constants.SESSION_KEY_PSN_PSW_CHANGE_FLG'), $person->psn_psw_update_flg);
        $request->session()->put(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG'), $person->psn_report_publish_flg);

        // セッションの生徒マスタ検索条件をクリア
        $request->session()->forget(config('constants.SESSION_KEY_MPERSON_SEARCH_CONDITION'));

        // メニュー画面へリダイレクト
        return redirect()->to(route('personTopMenu.index', ['schCode' => $person->sch_code]));
    }

    /**
     * 生徒代理ログアウトを行う
     */
    public function personLogout(Request $request): RedirectResponse
    {
        // 代理ログイン前のセッション情報を取得する
        $adminInfo = $request->session()->get(config('constants.SESSION_KEY_SURROGATE_LOGIN_PERSON'));

        // ログイン情報を元の情報に戻す
        $request->session()->put(config('constants.SESSION_KEY_SCHCODE'), $adminInfo[config('constants.SESSION_KEY_SCHCODE')]);
        $request->session()->put(config('constants.SESSION_KEY_SCHNAME'), $adminInfo[config('constants.SESSION_KEY_SCHNAME')]);
        $request->session()->put(config('constants.SESSION_KEY_SCHTESTNAME'), $adminInfo[config('constants.SESSION_KEY_SCHTESTNAME')]);
        $request->session()->put(config('constants.SESSION_KEY_SCHADMINKBN'), $adminInfo[config('constants.SESSION_KEY_SCHADMINKBN')]);
        $request->session()->put(config('constants.SESSION_KEY_MPERSON_SEARCH_CONDITION'), $adminInfo[config('constants.SESSION_KEY_MPERSON_SEARCH_CONDITION')]);
        $request->session()->put(config('constants.SESSION_KEY_PSN_HIDE_FLG'), $adminInfo[config('constants.SESSION_KEY_PSN_HIDE_FLG')]);
        $request->session()->put(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG'), $adminInfo[config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG')]);
        $request->session()->put(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG'), $adminInfo[config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG')]);
        $request->session()->put(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG'), $adminInfo[config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')]);

        // 代理ログイン情報をクリアする
        $request->session()->forget(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'));
        $request->session()->forget(config('constants.SESSION_KEY_SURROGATE_LOGIN_PERSON'));
        $request->session()->forget(config('constants.SESSION_KEY_CLSCODE'));
        $request->session()->forget(config('constants.SESSION_KEY_CLSNAME'));
        $request->session()->forget(config('constants.SESSION_KEY_PSNCODE'));
        $request->session()->forget(config('constants.SESSION_KEY_PSNNAME'));

        // 生徒マスタ一覧画面へリダイレクト
        return redirect()->to(route('sch.persons.index', ['schCode' => $adminInfo[config('constants.SESSION_KEY_SCHCODE')]]))->with(config('constants.SESSION_KEY_IS_SURROGATE_LOGOUT'), true);

    }
}
