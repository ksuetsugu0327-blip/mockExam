<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\JmasterListRequest;
use App\Services\MSchoolService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JmasterListController extends Controller implements HasMiddleware
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
     * 塾マスタ・一覧画面を表示する。
     */
    public function index(Request $request): View
    {

        // 遷移元を判定し、セッション情報を削除する (見直しが必要）
        $referer = url()->previous();
        // カレントURLから塾マスタ部分だけを抽出する
        $current = str_replace('/list', '', url()->current());

        if (! $request->session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGOUT')) &&
                (! preg_match('|^'.$current.'|', $referer) || ! $request->session()->has(config('constants.SESSION_KEY_MSCHOOL_SEARCH_CONDITION')))) {
            // 塾マスタ画面外から来た場合（ただし、代理ログイン戻りの場合は対象外）、または未検索時
            // セッションをクリア
            $request->session()->forget(config('constants.SESSION_KEY_MSCHOOL_SEARCH_CONDITION'));

            $message = '';
            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }
            $conditions = ['sch_code' => '', 'sch_name' => ''];

            return view('jmasterList')->with('schools', null)->with('resultCount', 0)->with('message', $message)
                ->with($conditions);
        } else {
            // 塾マスタ管理画面から来た場合、セッション情報を引き継いで検索処理を実施する
            $conditions = $request->session()->get(config('constants.SESSION_KEY_MSCHOOL_SEARCH_CONDITION'));
            $message = '';
            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }

            // 塾マスタを取得
            $schools = $this->service->selectList($conditions);
            // 表示件数を取得
            $resultCount = $schools->count();

            if ($resultCount == 0) {
                // 該当データがない場合
                $message = trans('message.SEARCH_NO_DATA');
            } elseif ($resultCount > config('constants.VIEW_RECORD_COUNT')) {
                // 明細表示最大行数を超えている場合、メッセージを表示
                $message = trans('message.WARN_OVER_VIEW_RECORD_COUNT', ['COUNT' => config('constants.VIEW_RECORD_COUNT')]);
                $schools = $schools->take(config('constants.VIEW_RECORD_COUNT'));
                $resultCount = config('constants.VIEW_RECORD_COUNT');
            }

            return view('jmasterList')->with('schools', $schools)->with('resultCount', $resultCount)->with('message', $message)
                ->with($conditions);
        }
    }

    /**
     * 塾マスタ・一覧画面での検索処理
     */
    public function search(JmasterListRequest $request): View
    {

        // 検索条件の値を取得
        $conditions = [
            'sch_code' => $request->get('sch_code'),
            'sch_name' => $request->get('sch_name'),
        ];

        // 塾マスタを取得
        $schools = $this->service->selectList($conditions);
        // 表示件数を取得
        $resultCount = $schools->count();

        $message = '';
        if ($resultCount == 0) {
            // 該当データがない場合
            $message = trans('message.SEARCH_NO_DATA');
        } elseif ($resultCount > config('constants.VIEW_RECORD_COUNT')) {
            // 明細表示最大行数を超えている場合、メッセージを表示
            $message = trans('message.WARN_OVER_VIEW_RECORD_COUNT', ['COUNT' => config('constants.VIEW_RECORD_COUNT')]);
            $schools = $schools->take(config('constants.VIEW_RECORD_COUNT'));
            $resultCount = config('constants.VIEW_RECORD_COUNT');
        }
        $request->session()->put(config('constants.SESSION_KEY_MSCHOOL_SEARCH_CONDITION'), $conditions);

        return view('jmasterList')->with('schools', $schools)->with('resultCount', $resultCount)->with('message', $message)
            ->with($conditions);

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
