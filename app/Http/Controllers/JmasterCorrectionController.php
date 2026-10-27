<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\JmasterCorrectionRequest;
use App\Services\MSchoolService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Request;

class JmasterCorrectionController extends Controller implements HasMiddleware
{
    private $service;

    /**
     * コンストラクタ
     *
     * @param  MSchoolService  $mSchoolService  塾マスタサービス
     */
    public function __construct(MSchoolService $mSchoolService)
    {
        // ログイン判定

        $this->service = $mSchoolService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_ADMIN'),
        ];
    }

    /**
     * 塾マスタ・修正画面を表示する。
     *
     * @param sch_code 塾コード(ログイン塾アカウント）
     * @param editSchCode 編集処理用塾コード
     */
    public function index($schCode, $editSchCode): view
    {

        // 塾マスタの取得
        $schools = $this->service->selectOneByKey((int) $editSchCode);
        if ($schools->count() == 0) {
            // 該当データが存在しない場合、データ不整合（削除されている等）のため、一覧に戻る
            return redirect()->route('schools.index', [session()->get(config('constants.SESSION_KEY_SCHCODE'))])
                ->with('message', trans('message.MSCHOOL_EXCLUSIVE_EXCEPTION'));
        }

        return view('jmasterCorrection', ['school' => $schools->get(0)]);
    }

    /**
     * 塾マスタ更新処理を行う
     */
    public function update(JmasterCorrectionRequest $request): RedirectResponse
    {
        // 画面情報を連想配列に設定
        $school = [
            'id' => $request->get('id'),
            'sch_code' => $request->get('sch_code'),
            'name' => $request->get('sch_name'),
            'portal_account' => $request->get('portal_account'),
            'cntconvert_kbn' => $request->get('cntconvert_kbn'),
            'cnttest_kbn' => $request->get('cnttest_kbn'),
            'testname' => $request->get('testname'),
            'admin_kbn' => $request->get('admin_kbn'),
            'psn_hide_flg' => $request->get('psn_hide_flg'),
            'schname_hide_flg' => $request->get('schname_hide_flg'),
            'psncard_regist_flg' => $request->get('psncard_regist_flg'),
            'psn_psw_update_flg' => $request->get('psn_psw_update_flg'),
            'cls_grade_csv_flg' => $request->get('cls_grade_csv_flg'),
            'psn_report_publish_flg' => $request->get('psn_report_publish_flg'),
            'permit_ip' => $request->get('permit_ip'),
        ];

        // パスワード更新判定
        $check = $request->get('jpchange');
        if (isset($check)) {
            // パスワードを追加
            $school['password'] = $request->get('password');
        }

        // 更新処理
        $result = $this->service->update($school);
        // 結果判定
        if ($result['resultCode'] == config('constants.RESULT_CODE_SYSTEM_ERROR')) {
            // システムエラーの場合（TODO:暫定）
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->route('schools.index', ['schCode' => $request->session()->get(config('constants.SESSION_KEY_SCHCODE'))])->with('message', $result['message']);
    }

    /**
     * 塾マスタ削除処理を行う。
     *
     * @param  $schCode  塾コード
     * @param  $delSchCode  削除対象塾コード
     */
    public function delete(Request $request, $schCode, $delSchCode): RedirectResponse
    {

        // 削除処理
        $result = $this->service->delete((int) $delSchCode);
        if ($result['resultCode'] == config('constants.RESULT_CODE_SYSTEM_ERROR')) {
            // システムエラーの場合（TODO:暫定）
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->to(route('schools.index', [$schCode]))->with('message', $result['message']);
    }
}
