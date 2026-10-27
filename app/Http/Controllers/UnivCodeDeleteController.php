<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\UnivCodeDataDeleteRequest;
use App\Services\UnivCodeHeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnivCodeDeleteController extends Controller implements HasMiddleware
{
    private $service;

    /**
     * コンストラクタ
     *
     * @param  UnivCodeHeadService  $mUnivCodeHeadService  模試テーブルサービス
     */
    public function __construct(UnivCodeHeadService $mUnivCodeHeadService)
    {
        // ログイン判定

        $this->service = $mUnivCodeHeadService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_ADMIN'),
        ];
    }

    /**
     * 【大学コードデータ削除】一覧画面
     */
    public function index(Request $request): View
    {
        $referer = url()->previous();
        // カレントURLから塾マスタ部分だけを抽出する
        $current = str_replace('-list', '', url()->current());
        // 年度プルダウンを用意
        $years = $this->service->generateYearsArray();
        // 学部/学科リスト
        $deptSubjSelect = config('selectlistarray.DEPT_SUBJ_SELECT');
        // メッセージ
        $message = '';
        if ($request->session()->has('message')) {
            $message = $request->session()->get('message');
        }

        // 大学コードデータ初期値
        $univCodeList = null;
        // 表示件数初期値
        $resultCount = 0;

        // 塾マスタ画面外から来た場合（ただし、代理ログイン戻りの場合は対象外）、または未検索時
        if (! $request->session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGOUT')) &&
                (! preg_match('|^'.$current.'|', $referer) || ! $request->session()->has(config('constants.SESSION_KEY_UNIVCODE_SEARCH_CONDITION')))) {
            // セッションをクリア
            $request->session()->forget(config('constants.SESSION_KEY_UNIVCODE_SEARCH_CONDITION'));

            $conditions = ['year' => '', 'data_name' => '', 'dept_subject_div' => ''];
        } else {
            // 塾マスタ管理画面から来た場合、セッション情報を引き継いで検索処理を実施する
            $conditions = $request->session()->get(config('constants.SESSION_KEY_UNIVCODE_SEARCH_CONDITION'));

            // 大学コードデータを取得
            $univCodeList = $this->service->univCodeDataList($conditions);

            // 表示件数を取得
            $resultCount = $univCodeList->count();
        }

        return view('univ_delete')->with([
            'univCodeList' => $univCodeList,
            'resultCount' => $resultCount,
            'message' => $message,
            'years' => $years,
            'deptSubjSelect' => $deptSubjSelect,
            'selectedYear' => $conditions['year'],
            'dataNameKeyword' => $conditions['data_name'],
            'selectedDeptSubjectDiv' => $conditions['dept_subject_div'],
        ]);
    }

    /**
     * 【大学コードデータ削除】一覧画面検索処理
     */
    public function search(UnivCodeDataDeleteRequest $request): View
    {

        // 検索条件の値を取得
        $conditions = [
            'year' => $request->get('year'),
            'data_name' => $request->get('data_name'),
            'dept_subject_div' => $request->get('dept_subject_div'),
        ];

        // 年度プルダウンを用意
        $years = $this->service->generateYearsArray();

        // 学部/学科リスト
        $deptSubjSelect = config('selectlistarray.DEPT_SUBJ_SELECT');

        // 大学コードデータを取得
        $univCodeList = $this->service->univCodeDataList($conditions);

        // 表示件数を取得
        $resultCount = $univCodeList->count();

        $message = '';
        if ($resultCount == 0) {
            // 該当データがない場合
            $message = trans('message.SEARCH_NO_DATA');
        } elseif ($resultCount > config('constants.VIEW_RECORD_COUNT')) {
            // 明細表示最大行数を超えている場合、メッセージを表示
            $message = trans('message.WARN_OVER_VIEW_RECORD_COUNT', ['COUNT' => config('constants.VIEW_RECORD_COUNT')]);
            $univCodeList = $univCodeList->take(config('constants.VIEW_RECORD_COUNT'));
            $resultCount = config('constants.VIEW_RECORD_COUNT');
        }

        $request->session()->put(config('constants.SESSION_KEY_UNIVCODE_SEARCH_CONDITION'), $conditions);

        return view('univ_delete')->with([
            'univCodeList' => $univCodeList,
            'resultCount' => $resultCount,
            'message' => $message,
            'years' => $years,
            'deptSubjSelect' => $deptSubjSelect,
            'selectedYear' => $conditions['year'],
            'dataNameKeyword' => $conditions['data_name'],
            'selectedDeptSubjectDiv' => $conditions['dept_subject_div'],
        ]);
    }

    /**
     * 【大学コードデータ削除】削除処理
     *
     * @param  int  $id  削除対象ID
     */
    public function delete(Request $request, int $schCode, int $id): RedirectResponse
    {
        // 削除処理
        $result = $this->service->delete($id);
        if ($result['resultCode'] == config('constants.RESULT_CODE_SYSTEM_ERROR')) {
            // システムエラーの場合
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->to(route('univ_code.list', [$schCode]))->with('message', $result['message']);
    }
}
