<?php

namespace App\Http\Controllers;

use App\Services\MClassService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class TicketDetailController extends Controller implements HasMiddleware
{
    // 志望校関連用サービス
    private $service;

    // 教室マスタサービス
    private $clsService;

    /**
     * コンストラクタ
     *
     * @param  TicketService  $TicketService  志望校関連用サービス
     * @param  MClassService  $mClassService  教室マスタサービス
     */
    public function __construct(TicketService $TicketService,
        MClassService $mClassService)
    {
        // ログイン判定

        $this->service = $TicketService;
        $this->clsService = $mClassService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_CLASS')
                .','.config('constants.GUARD_SURROGATE_SCHOOL').','.config('constants.GUARD_SURROGATE_CLASS'),
        ];
    }

    /**
     * 初期表示処理
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $sch_code, int $index)
    {
        if ($request->session()->has(config('constants.SESSION_KEY_TICKET_LIST'))) {
            $data = $request->session()->get(config('constants.SESSION_KEY_TICKET_LIST'));

            // テスト情報
            $conditions = $data['directory'][$index];
            $conditions += [
                'sch_code' => $sch_code,
            ];
            // POSTデータの場合、教室の条件を追加
            if ($request->isMethod('post')) {
                $conditions += [
                    'cls_code' => $request->get('cls_code'),
                ];
            } else {
                $conditions += [
                    'cls_code' => session(config('constants.SESSION_KEY_CLSCODE')),
                ];
            }
            // 志望校一覧
            $socList = $this->service->TicketList($conditions);
            $resultCount = $socList->count();
            if ($resultCount == 0) {
                // 該当データがない場合
                $message = trans('message.SEARCH_NO_DATA');
            } else {
                $message = '';
            }

            $viewMode = 3;
            if (auth(config('constants.GUARD_ADMIN'))->check()) {
                if (session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) {
                    // 代理塾
                    $viewMode = 2;
                } elseif (session()->has(config('SESSION_KEY_SURROGATE_LOGIN_CLASS'))) {
                    // 代理教室
                    $viewMode = 3;
                } else {
                    // 管理者
                    $viewMode = 1;
                }
            } else {
                if (auth(config('constants.GUARD_SCHOOL'))->check()) {
                    // 塾
                    $viewMode = 2;
                } elseif (auth(config('constants.GUARD_CLASS'))->check()) {
                    // 教室
                    $viewMode = 3;
                }
            }

            // 教室リスト
            $clsList = $this->clsService->selectClassBoxList($sch_code)
                ->pluck('name', 'cls_code')
                ->toArray();

            // 表示用情報
            $params = [
                'key' => $index,
                'year' => $conditions['year'],
                'testName' => $conditions['testName'],
                'grade' => $conditions['grade'],
                'clsCode' => $conditions['cls_code'],
                'clsList' => $clsList,
                'viewMode' => $viewMode,
            ];

            return view('ticketDetail')
                ->with($params)
                ->with('socList', $socList)
                ->with('message', $message);
        } else {
            return redirect()
                ->route(RouteUtil::getPrefix().'.ticketList.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))]);
        }
    }
}
