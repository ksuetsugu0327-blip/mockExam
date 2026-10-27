<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\KmasterListRequest;
use App\Services\MClassService;
use App\Utils\FileUtil;
use App\Utils\PasswordUtil;
use Illuminate\Http\Request;

class KmasterListController extends Controller
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
        $this->middleware('auth:'.config('constants.GUARD_ADMIN').','.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_SURROGATE_SCHOOL'));
        $this->service = $mClassService;
    }

    /* 教室マスタ・一覧画面を表示する。
     *
     * @return view
     */

    public function index(Request $request): View
    {

        // 遷移元を判定し、セッション情報を削除する (見直しが必要）
        $referer = url()->previous();
        // カレントURLから塾マスタ部分だけを抽出する
        $current = str_replace('/list', '', url()->current());
        if (! $request->session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGOUT')) &&
                (! preg_match('|^'.$current.'|', $referer) || ! $request->session()->has(config('constants.SESSION_KEY_MCLASS_SEARCH_CONDITION')))) {
            // 塾マスタ画面外から来た場合（ただし、代理ログイン戻りの場合は対象外）、または未検索時
            // セッションをクリア
            $request->session()->forget(config('constants.SESSION_KEY_MCLASS_SEARCH_CONDITION'));
            $message = '';
            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }
            $conditions = ['sch_code' => '', 'sch_name' => '', 'cls_code' => '', 'cls_name' => ''];

            return view('kmasterList')->with('classes', null)->with('resultCount', 0)->with('message', $message)
                ->with($conditions)->with('sch_code', '');
        } else {
            // 教室マスタ管理画面から来た場合、セッション情報を引き継いで検索処理を実施する
            $conditions = $request->session()->get(config('constants.SESSION_KEY_MCLASS_SEARCH_CONDITION'));
            $message = '';
            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }
            // 塾マスタを取得
            $classes = $this->service->selectList($conditions);
            // 表示件数を取得
            $resultCount = $classes->count();
            if ($resultCount == 0) {
                // 該当データがない場合
                $message = trans('message.SEARCH_NO_DATA');
            } elseif ($resultCount > config('constants.VIEW_RECORD_COUNT')) {
                // 明細表示最大行数を超えている場合、メッセージを表示
                $message = trans('message.WARN_OVER_VIEW_RECORD_COUNT', ['COUNT' => config('constants.VIEW_RECORD_COUNT')]);
                $classes = $classes->take(config('constants.VIEW_RECORD_COUNT'));
                $resultCount = config('constants.VIEW_RECORD_COUNT');
            }
            // 検索結果をセッションに保存
            $request->session()->put(config('constants.SESSION_KEY_MPERSON_LIST'), $classes);

            return view('kmasterList')->with('classes', $classes)->with('resultCount', $resultCount)->with('message', $message)
                ->with($conditions);
        }
    }

    /**
     * 教室マスタ・一覧画面での検索処理
     *
     * @return view
     */
    public function search(KmasterListRequest $request): View
    {

        // 管理ユーザー以外はセッション値を使用する。
        if (auth(config('constants.GUARD_ADMIN'))->check() && ! session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) {
            $schCode = $request->get('sch_code');
        } else {
            $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        }
        // 検索条件の値を取得
        $conditions = [
            'sch_code' => $schCode,
            'sch_name' => $request->get('sch_name'),
            'cls_code' => $request->get('cls_code'),
            'cls_name' => $request->get('cls_name'),
        ];
        if ($request->has('sch_name')) {
            $conditions['sch_name'] = $request->get('sch_name');
        }
        if ($request->has('cls_name')) {
            $conditions['cls_name'] = $request->get('cls_name');
        }

        // 塾マスタを取得
        $classes = $this->service->selectList($conditions);
        // 表示件数を取得
        $resultCount = $classes->count();
        $message = '';
        if ($resultCount == 0) {
            // 該当データがない場合
            $message = trans('message.SEARCH_NO_DATA');
        } elseif ($resultCount > config('constants.VIEW_RECORD_COUNT')) {
            // 明細表示最大行数を超えている場合、メッセージを表示
            $message = trans('message.WARN_OVER_VIEW_RECORD_COUNT', ['COUNT' => config('constants.VIEW_RECORD_COUNT')]);
            $classes = $classes->take(config('constants.VIEW_RECORD_COUNT'));
            $resultCount = config('constants.VIEW_RECORD_COUNT');
        }
        // 検索条件をセッションに設定
        $request->session()->put(config('constants.SESSION_KEY_MCLASS_SEARCH_CONDITION'), $conditions);
        $request->session()->put(config('constants.SESSION_KEY_MPERSON_LIST'), $classes);

        return view('kmasterList')->with('classes', $classes)->with('resultCount', $resultCount)->with('message', $message)->with($conditions);
    }

    /**
     * 塾マスタ削除処理を行う。
     *
     * @param  $clsCode  塾コード
     * @param  $delClsCode  削除対象塾コード
     * @return redirect
     */
    public function delete(Request $request, $schCode, $delSchCode, $delClsCode): RedirectResponse
    {

        // 削除処理
        $result = $this->service->delete((int) $delSchCode, (int) $delClsCode);
        if ($result['resultCode'] == config('constants.RESULT_CODE_SYSTEM_ERROR')) {
            // システムエラーの場合（TODO:暫定）
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->to(route('classes.index', [$schCode]))->with('message', $result['message']);
    }

    /**
     * CSVダウンロード押下時処理
     *
     * @param  Request  $request  リクエスト
     */
    public function downloadCsv(Request $request): StreamedResponse
    {
        $outputFlg = 0;
        // 出力内容設定とヘッダの設定
        if (auth(config('constants.GUARD_ADMIN'))->check()
            && ! session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) {
            // 代理ログインしていない管理者の場合
            $outputFlg = 1;
            $csvHeader = ['塾コード', '塾名', '教室コード', '教室名', '仮パスワード'];
        } elseif ((auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL')))
            || auth(config('constants.GUARD_SCHOOL'))->check()
        ) { // 塾アカウント または 塾アカウントで代理ログインしている管理者
            $outputFlg = 2;
            $csvHeader = ['塾コード', '塾名', '教室コード', '教室名', '仮パスワード'];
        }

        // 出力データ
        if ($request->session()->has(config('constants.SESSION_KEY_MPERSON_LIST'))) {
            $classes = $request->session()->get(config('constants.SESSION_KEY_MPERSON_LIST'));

            // 出力用に編集
            $outputDataList = [];
            foreach ($classes as $classe) {
                // if($outputFlg == 1){
                array_push($outputDataList, [
                    'sch_code' => $classe->sch_code,
                    'sch_name' => $classe->sch_name,
                    'cls_code' => $classe->cls_code,
                    'cls_name' => $classe->name,
                    // 'password'     => $classe->password,
                    'temppassword' => PasswordUtil::decryptString($classe->temppassword),
                ]);
                //                }elseif($outputFlg == 2){
                //                    array_push($outputDataList,[
                //                        'sch_code'     => $classe->sch_code,
                //                        'sch_name'     => $classe->sch_name,
                //                        'cls_code'     => $classe->cls_code,
                //                        'cls_name'     => $classe->name,
                //                        //'password'     => $classe->password,
                //                        'temppassword' => PasswordUtil::decryptString($classe->temppassword)
                //                    ]);
                //                }
            }
        } else {
            $outputDataList = [];
        }

        $responseHeader = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachement;filename='.config('constants.CLASS_EXPORT_CSV_FILENAME'),
        ];

        return response()->streamDownload(function () use ($outputDataList, $csvHeader) {
            echo FileUtil::CreateCSV($outputDataList, $csvHeader);
        }, config('constants.CLASS_EXPORT_CSV_FILENAME'), $responseHeader);

    }
}
