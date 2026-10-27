<?php

namespace App\Http\Controllers;

use App\Services\MPersonService;
use App\Services\MSchoolService;
use App\Services\TicketService;
use App\Utils\RouteUtil;
use DateTime;
use Illuminate\Http\Request;

class TicketSearchListController extends Controller
{
    // 志望校関連用サービス
    private $service;

    // 生徒マスタサービス
    private $psnService;

    // 塾リスト
    private $schList;

    /**
     * コンストラクタ
     *
     * @param  TicketService  $TicketService  志望校関連用サービス
     * @param  MPersonService  $mPersonService  生徒マスタサービス
     * @param  MSchoolService  $mSchoolService  塾マスタサービス
     */
    public function __construct(TicketService $TicketService,
        MPersonService $mPersonService,
        MSchoolService $mSchoolService)
    {
        // ログイン判定(管理者、塾、教室、代理塾、代理教室）
        $this->middleware('auth:'.config('constants.GUARD_ADMIN').','.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_CLASS').
            ','.config('constants.GUARD_SURROGATE_SCHOOL').','.config('constants.GUARD_SURROGATE_CLASS')
            .','.config('constants.GUARD_PERSON')
        );
        $this->service = $TicketService;
        $this->psnService = $mPersonService;
        $this->schList = $mSchoolService->selectList([])
            ->pluck('name', 'sch_code')->toArray();
    }

    /**
     * 初期表示処理
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 遷移元を判定し、セッション情報を削除する (見直しが必要）
        $referer = url()->previous();
        // カレントURLから塾マスタ部分だけを抽出する
        $current = str_replace('/list', '', url()->current());
        // 表示タイプ設定
        $viewMode = $this->getViewMode();

        if (! preg_match('|^'.$current.'|', $referer) ||
            ! $request->session()->has(config('constants.SESSION_KEY_TICKET_SEARCH_CONDITION'))) {

            if ($request->session()->has(config('constants.SESSION_KEY_TICKET_SEARCH_CONDITION'))) {
                // 検索値がセッションに格納されている場合、セッションをクリアして初期表示
                $request->session()->forget(config('constants.SESSION_KEY_TICKET_SEARCH_CONDITION'));
            }
            $message = '';
            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }

            // 生徒の場合は年度固定
            $year = $request->get('year');
            if ($viewMode == 4) {
                $date = new DateTime;
                $month = $date->format('n');
                if ($month < 4) {
                    $year = $date->format('Y') - 1;
                } else {
                    $year = $date->format('Y');
                }
            }

            $params = [
                'sch_code' => null,                 // 塾コード
                'year' => $year,                    // 年度
                'grade' => null,                    // 学年

                'viewMode' => $viewMode,            // 表示タイプ
                'sch_list' => $this->schList,       // 塾リスト
            ];

            return view('ticketSearchList')
                ->with('resultCount', 0)
                ->with($params)
                ->with($message);
        } else {
            // セッション情報を引き継いで検索処理を実施する
            $message = '';
            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }

            // セッションから検索条件取得
            $conditions = $request->session()->get(config('constants.SESSION_KEY_TICKET_SEARCH_CONDITION'));
            // 検索
            $screenList = $this->service->search($viewMode, $conditions);
            $resultCount = isset($screenList) ? count($screenList) : 0;

            $directory = [
                'directory' => $screenList, // 一覧
                'resultCount' => $resultCount,
                'message' => $message,
            ];

            // 表示用パラメータを追加
            $param = [
                'sch_list' => $this->schList,       // 塾リスト
                'viewMode' => $viewMode,            // 表示タイプ
            ]
                + $conditions;

            return view('ticketSearchList')->with($directory)->with($param);
        }
    }

    /**
     * 検索処理
     *
     * @param  \App\index  $index
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        // 表示タイプ設定
        $viewMode = $this->getViewMode();

        // 検索条件の値を取得
        // 塾コード
        if ($viewMode == 1) {
            $schCode = $request->get('sch_code');
        } else {
            $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        }
        $conditions = [
            'sch_code' => $schCode,                     // 塾コード
            'year' => $request->get('year'),            // 年度
            'grade' => $request->get('grade'),          // 学年
        ];

        // 生徒サイトの場合は検索条件に個人情報などを追加
        if ($viewMode == 4) {
            $psnCode = $request->session()->get(config('constants.SESSION_KEY_PSNCODE'));
            $conditions += [
                'psn_code' => $psnCode,
                'id_person' => $this->psnService->selectOneByKey($schCode, $psnCode)->get(0)['id'],
            ];
        }

        // 教室サイトの場合は検索条件に教室コードを追加
        if ($viewMode == 3) {
            $conditions += [
                'cls_code' => $request->session()->get(config('constants.SESSION_KEY_CLSCODE')),
            ];
        }

        // 検索
        $screenList = $this->service->search($viewMode, $conditions);
        $resultCount = isset($screenList) ? count($screenList) : 0;
        if ($resultCount == 0) {
            // 該当データがない場合
            $message = trans('message.SEARCH_NO_DATA');
        } else {
            $message = '';
        }

        $directory = [
            'directory' => $screenList,    // 一覧
            'resultCount' => $resultCount,
            'message' => $message,
        ];

        // 検索条件退避
        $request->session()->put(config('constants.SESSION_KEY_TICKET_LIST'), $directory);
        $request->session()->put(config('constants.SESSION_KEY_TICKET_SEARCH_CONDITION'), $conditions);

        // 表示用パラメータを追加
        $param = [
            'sch_list' => $this->schList,       // 塾リスト
            'viewMode' => $viewMode,            // 表示タイプ
        ]
            + $conditions;

        return view('ticketSearchList')
            ->with($directory)
            ->with($param);
    }

    /**
     * 画面表示タイプ設定
     *
     * @return 1:管理者、2:塾、3:教室、4:生徒
     */
    private function getViewMode()
    {
        if (auth(config('constants.GUARD_ADMIN'))->check()) {
            $prefix = RouteUtil::getPrefix();
            if ($prefix == 'sch') {
                // 代理塾
                $ret = 2;
            } elseif ($prefix == 'cls') {
                // 代理教室
                $ret = 3;
            } elseif ($prefix == 'psn') {
                // 代理生徒
                $ret = 4;
            } else {
                // 管理者
                $ret = 1;
            }
        } else {
            if (auth(config('constants.GUARD_SCHOOL'))->check()) {
                // 塾
                $ret = 2;
            } elseif (auth(config('constants.GUARD_CLASS'))->check()) {
                // 教室
                $ret = 3;
            } else {
                // 生徒
                $ret = 4;
            }
        }

        return $ret;
    }
}
