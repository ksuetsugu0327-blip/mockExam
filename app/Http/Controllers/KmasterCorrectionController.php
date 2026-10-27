<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\KmasterCorrectionRequest;
use App\Services\MClassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Request;

class KmasterCorrectionController extends Controller implements HasMiddleware
{
    private $service;

    /**
     * コンストラクタ
     *
     * @param  MClassService  $mClassService  教室マスタサービス
     */
    public function __construct(MClassService $mClassService)
    {
        // ログイン判定

        $this->service = $mClassService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_ADMIN').','.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_SURROGATE_SCHOOL'),
        ];
    }

    /**
     * 教室マスタ・修正画面を表示する。
     *
     * @param cls_code 教室コード(ログイン教室アカウント）
     * @param edit_cls_code 編集処理用教室コード
     */
    // public function index(Request $request, $sch_code, $cls_code): view {
    public function index($sch_code, $editSchCode, $editClsCode): view
    {
        // 教室マスタの取得
        //  $classs = $this->service->selectList(isset($schCode) ? (int) $schCode : null, $schName, isset($clsCode) ? (int) $clsCode : null, $clsName);
        // $classes = $this->service->selectOneByKey((int) $edit_sch_code,(int) $edit_cls_code);
        // 塾名を取り、塾コードと塾名をくっつける
        $m_school = DB::table('m_school')
            ->select(DB::raw('CONCAT(sch_code, SPACE(1), name) AS name'))
            ->where('sch_code', $editSchCode)
            ->first();

        $sch_name = $m_school->name;

        // 教室マスタの取得
        $classes = $this->service->selectOneByKey((int) $editSchCode, (int) $editClsCode);
        if ($classes->count() == 0) {
            // 該当データが存在しない場合、データ不整合（削除されている等）のため、一覧に戻る
            return \redirect()->route('classes.index', [session()->get(config('constants.SESSION_KEY_SCHCODE'))])
                ->with('message', trans('message.MCLASS_EXCLUSIVE_EXCEPTION'));
        }

        return view('kmasterCorrection', ['class' => $classes->get(0)])->with('sch_name', $sch_name);
    }

    /**
     * 教室マスタ修正・削除処理を行う。
     */
    public function update(KmasterCorrectionRequest $request): RedirectResponse
    {
        // 画面情報を連想配列に設定
        $class = [
            'id' => $request->get('id'),
            'cls_code' => $request->get('cls_code'),
            'cls_name' => $request->get('cls_name'),
        ];
        if ($request->session()->get(config('constants.SESSION_KEY_SCHADMINKBN')) == config('constants.ADMIN_KBN_ADMIN')) {
            // 管理者の場合、以下更新項目の設定
            $class['cls_code'] = $request->get('cls_code');
            $class['name'] = $request->get('cls_name');
        }
        // パスワード更新判定
        $check = $request->get('jpchange');
        if (isset($check)) {
            // パスワードを追加
            $class['password'] = $request->get('password');
        }

        // パスワード再発行判定
        $temppassCheck = $request->get('temppasswordReissueCheck');
        // dd($check);
        if (isset($temppassCheck)) {
            // パスワードを追加
            $class['temppassword'] = Crypt::encryptString(Str::random(8));
        }

        // 更新処理
        $result = $this->service->update($class);
        // 結果判定
        if ($result['resultCode'] == config('constants.RESULT_CODE_SYSTEM_ERROR')) {
            // システムエラーの場合（TODO:暫定）
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->route('classes.index', ['schCode' => $request->session()->get(config('constants.SESSION_KEY_SCHCODE')), 'clsCode' => $request->session()->get(config('constants.SESSION_KEY_CLSCODE'))])->with('message', $result['message']);
    }

    /**
     * 塾マスタ削除処理を行う。
     *
     * @param  $clsCode  塾コード
     * @param  $delClsCode  削除対象塾コード
     */
    public function delete(Request $request, $sch_code, $delSchCode, $delClsCode): RedirectResponse
    {

        // 削除処理
        $result = $this->service->delete((int) $delSchCode, (int) $delClsCode);
        if ($result['resultCode'] == config('constants.RESULT_CODE_SYSTEM_ERROR')) {
            // システムエラーの場合（TODO:暫定）
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->to(route('classes.index', [$sch_code]))->with('message', $result['message']);
    }
}
