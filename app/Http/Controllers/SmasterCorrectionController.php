<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\SmasterCorrectionRequest;
use App\Services\MPersonService;
use App\Utils\RouteUtil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class SmasterCorrectionController extends Controller implements HasMiddleware
{
    private $service;

    /**
     * コンストラクタ
     *
     * @param  MPersonService  $mPersonService  塾マスタサービス
     */
    public function __construct(MPersonService $mPersonService)
    {
        // ログイン判定

        $this->service = $mPersonService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_ADMIN').','.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_CLASS')
                .','.config('constants.GUARD_SURROGATE_SCHOOL').','.config('constants.GUARD_SURROGATE_CLASS'),
        ];
    }

    /**
     * 生徒マスタ・修正画面を表示する。
     *
     * @param schCode 塾コード(ログイン塾アカウント）
     * @param editSchCode 編集処理用塾コード
     * @param editPsnCode 編集処理用個人コード
     */
    public function index($schCode, $editSchCode, $editPsnCode): view
    {

        // 塾マスタの取得
        $persons = $this->service->selectOneByKeyWithSchCls($editSchCode, $editPsnCode);
        // パスワード変更可能フラグをセッションから取得
        $psn_psw_update_flg = $persons->get(0)->psn_psw_update_flg;

        if ($persons->count() == 0) {
            // 該当データが存在しない場合、データ不整合（削除されている等）のため、一覧に戻る
            return redirect()->route('persons.index', [$schCode])
                ->with('message', trans('message.MPERSON_EXCLUSIVE_EXCEPTION'));
        }

        return view('smasterCorrection', ['person' => $persons->get(0), 'psn_psw_update_flg' => $psn_psw_update_flg]);
    }

    /**
     * 生徒マスタ更新処理を行う
     *
     * @param schCode 塾コード(ログイン塾アカウント）
     * @param editSchCode  編集処理用塾コード
     * @param editPsnCode  編集処理用個人コード
     */
    public function update(SmasterCorrectionRequest $request, $schCode, $editSchCode, $editPsnCode): RedirectResponse
    {
        // 画面情報を連想配列に設定
        $person = [
            'id' => $request->get('person_id'),
            'sch_code' => $editSchCode,
            'psn_code' => $editPsnCode,
        ];
        if ($request->session()->get(config('constants.SESSION_KEY_SCHADMINKBN')) == config('constants.ADMIN_KBN_ADMIN')) {
            // 管理者の場合、以下更新項目の設定
            $person['cls_code'] = $request->get('cls_code');
            $person['name'] = $request->get('psn_name');
            $person['birthlow4'] = $request->get('birthlow4');
            $person['postallow4'] = $request->get('postallow4');
            $person['tellow4'] = $request->get('tellow4');
        }

        // パスワード再設定用メールアドレス更新
        $email = $request->get('email');
        if (isset($email)) {
            $person['mail_address'] = $email;
        }

        // パスワード再発行判定
        $check = $request->get('temppasswordReissueCheck');
        if (isset($check)) {
            // パスワードを追加
            $person['password'] = null;
            $person['temppassword'] = Crypt::encryptString(Str::random(8));
        }

        // 更新処理
        $result = $this->service->update($person, $request->session()->get(config('constants.SESSION_KEY_SCHADMINKBN')));
        // 結果判定
        if ($result['resultCode'] == config('constants.RESULT_CODE_SYSTEM_ERROR')) {
            // システムエラーの場合（TODO:暫定）
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->route(RouteUtil::getPrefix().'.persons.index', ['schCode' => $schCode])->with('message', $result['message']);
    }

    /**
     * 生徒マスタ削除処理を行う。
     *
     * @param  Request  $request  リクエスト
     * @param  $delSchCode  塾コード
     * @param  $delPsnCode  個人コード
     */
    public function delete(Request $request, $schCode, $editSchCode, $delPsnCode): RedirectResponse
    {

        // 削除処理
        $result = $this->service->delete((int) $editSchCode, (string) $delPsnCode);
        if ($result['resultCode'] == config('constants.RESULT_CODE_SYSTEM_ERROR')) {
            // システムエラーの場合（TODO:暫定）
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->to(route(RouteUtil::getPrefix().'.persons.index', [$schCode]))->with('message', $result['message']);
    }
}
