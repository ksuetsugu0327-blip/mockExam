<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Services\MPersonService;
use App\Services\MSchoolService;
use App\Utils\FileUtil;
use App\Utils\PasswordUtil;
use App\Utils\RouteUtil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SmasterListController extends Controller implements HasMiddleware
{
    private $service;

    /**
     * コンストラクタ
     *
     * @param  MPersonService  $mPersonService  個人マスタサービス
     * @param  MSchoolService  $mSchoolService  塾マスタサービス
     */
    public function __construct(MPersonService $mPersonService, MSchoolService $mSchoolService)
    {
        // ログイン判定

        $this->service = $mPersonService;
        $this->mSchoolService = $mSchoolService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_ADMIN').','.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_CLASS')
                .','.config('constants.GUARD_SURROGATE_SCHOOL').','.config('constants.GUARD_SURROGATE_CLASS'),
        ];
    }

    /**
     * 生徒マスタ・一覧画面を表示する。
     *
     * @param  Request  $request  リクエスト
     */
    public function index(Request $request): View
    {
        // 遷移元を判定し、セッション情報を削除する (見直しが必要）
        $referer = url()->previous();
        // カレントURLから塾マスタ部分だけを抽出する
        $current = str_replace('/list', '', url()->current());

        if (! $request->session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGOUT')) &&
                (! preg_match('|^'.$current.'|', $referer) || ! $request->session()->has(config('constants.SESSION_KEY_MPERSON_SEARCH_CONDITION')))) {
            // 生徒マスタ画面外から来た場合（ただし、代理ログイン戻りの場合は対象外）、または未検索時
            // セッションクリア
            $request->session()->forget(config('constants.SESSION_KEY_MPERSON_SEARCH_CONDITION'));
            $request->session()->forget(config('constants.SESSION_KEY_MPERSON_LIST'));

            $message = '';
            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }

            // 検索条件用
            $conditions = ['sch_code' => '', 'sch_name' => '',
                'cls_code' => '', 'cls_name' => '',
                'psn_code' => '', 'psn_name' => '',
            ];

            return view('smasterList')->with('persons', null)->with('resultCount', 0)->with('message', $message)
                ->with($conditions)->with('sch_code', '');

        } else {
            // 生徒マスタ管理画面から来た場合、セッション情報を引き継いで検索処理を実施する
            $conditions = $request->session()->get(config('constants.SESSION_KEY_MPERSON_SEARCH_CONDITION'));
            $message = '';
            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }

            // 生徒マスタを取得
            $persons = $this->service->selectList($conditions);
            // 表示件数を取得
            $resultCount = $persons->count();

            if ($resultCount == 0) {
                // 該当データがない場合
                $message = trans('message.SEARCH_NO_DATA');
            } elseif ($resultCount > config('constants.VIEW_RECORD_COUNT')) {
                // 明細表示最大行数を超えている場合、メッセージを表示
                $message = trans('message.WARN_OVER_VIEW_RECORD_COUNT', ['COUNT' => config('constants.VIEW_RECORD_COUNT')]);
                $persons = $persons->take(config('constants.VIEW_RECORD_COUNT'));
                $resultCount = config('constants.VIEW_RECORD_COUNT');
            }
            // 検索結果をセッションに保存
            $request->session()->put(config('constants.SESSION_KEY_MPERSON_LIST'), $persons);

            return view('smasterList')->with('persons', $persons)->with('resultCount', $resultCount)->with('message', $message)
                ->with($conditions);
        }
    }

    /**
     * 生徒マスタ・一覧画面での検索処理
     */
    public function search(Request $request): View
    {
        // 管理ユーザー以外はセッション値を使用する。
        if (auth(config('constants.GUARD_ADMIN'))->check() && ! session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) {
            // 管理ユーザのみ（代理ログインは除く）
            $schCode = $request->get('sch_code');
            $clsCode = $request->get('cls_code');
        } elseif (auth(config('constants.GUARD_SCHOOL'))->check() || session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) {
            // 塾ユーザ || 管理ユーザが塾代理ログイン
            $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
            $clsCode = $request->get('cls_code');
        } else {
            // 教室
            $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
            $clsCode = $request->session()->get(config('constants.SESSION_KEY_CLSCODE'));
        }
        // 検索条件を格納
        $conditions = [
            'sch_code' => $schCode,
            'cls_code' => $clsCode,
            'psn_code' => $request->get('psn_code'),
            'psn_name' => $request->get('psn_name'),
        ];
        if ($request->has('sch_name')) {
            $conditions['sch_name'] = $request->get('sch_name');
        }
        if ($request->has('cls_name')) {
            $conditions['cls_name'] = $request->get('cls_name');
        }

        // 個人マスタを取得
        $persons = $this->service->selectList($conditions);
        // 表示件数を取得
        $resultCount = $persons->count();

        $message = '';
        if ($resultCount == 0) {
            // 該当データがない場合
            $message = trans('message.SEARCH_NO_DATA');
        } elseif ($resultCount > config('constants.VIEW_RECORD_COUNT')) {
            // 明細表示最大行数を超えている場合、メッセージを表示
            $message = trans('message.WARN_OVER_VIEW_RECORD_COUNT', ['COUNT' => config('constants.VIEW_RECORD_COUNT')]);
            $persons = $persons->take(config('constants.VIEW_RECORD_COUNT'));
            $resultCount = config('constants.VIEW_RECORD_COUNT');
        }
        // 検索条件をセッションに設定
        $request->session()->put(config('constants.SESSION_KEY_MPERSON_SEARCH_CONDITION'), $conditions);
        $request->session()->put(config('constants.SESSION_KEY_MPERSON_LIST'), $persons);

        return view('smasterList')->with('persons', $persons)->with('resultCount', $resultCount)->with('message', $message)
            ->with($conditions);
    }

    /**
     * CSVダウンロード押下時処理
     *
     * @param  Request  $request  リクエスト
     */
    public function downloadCsv(Request $request): StreamedResponse
    {
        $outputFlg = 0;
        $isAdmin = false;
        // 出力内容設定とヘッダの設定
        if (auth(config('constants.GUARD_ADMIN'))->check() && ! session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) {
            // 代理ログインしていない管理者の場合
            $isAdmin = true;
            $outputFlg = 1;
            $csvHeader = ['塾生ID', '氏名', '塾', '教室', '仮パスワード'];
        } elseif (
            (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) ||
             auth(config('constants.GUARD_SCHOOL'))->check()
        ) {
            // 管理者の場合
            if (auth(config('constants.GUARD_ADMIN'))->check()) {
                $isAdmin = true;
            }
            // 塾アカウント または 塾アカウントで代理ログインしている管理者
            $outputFlg = 2;
            $csvHeader = ['塾生ID', '氏名', '教室コード', '教室', '仮パスワード'];
        } else {
            // 教室アカウント または 教室アカウントで代理ログインしている管理者
            $outputFlg = 3;
            $csvHeader = ['塾生ID', '氏名', '仮パスワード'];
        }

        // 出力データ
        if ($request->session()->has(config('constants.SESSION_KEY_MPERSON_LIST'))) {
            $persons = $request->session()->get(config('constants.SESSION_KEY_MPERSON_LIST'));
            // 出力用に編集
            $outputDataList = [];
            foreach ($persons as $person) {
                $school = $this->mSchoolService->selectOneByKey($person->sch_code)->first();
                $psnPswChangeFlag = $school->psn_psw_update_flg;

                if ($outputFlg == 1) {
                    $csvHeader = ['塾生ID', '氏名', '塾', '教室', 'メールアドレス', '仮パスワード'];
                    array_push($outputDataList, [
                        'psn_code' => $person->psn_code,
                        'psn_name' => $person->psn_name,
                        'sch_name' => $person->sch_name,
                        'cls_name' => $person->cls_name,
                        'mail_address' => $person->mail_address,
                        'temppassword' => PasswordUtil::decryptString($person->temppassword),
                    ]);
                } elseif ($outputFlg == 2) {
                    if ($psnPswChangeFlag || $isAdmin) {
                        $csvHeader = ['塾生ID', '氏名', '教室コード', '教室', 'メールアドレス', '仮パスワード'];
                        array_push($outputDataList, [
                            'psn_code' => $person->psn_code,
                            'psn_name' => $person->psn_name,
                            'cls_code' => $person->cls_code,
                            'cls_name' => $person->cls_name,
                            'mail_address' => $person->mail_address,
                            'temppassword' => PasswordUtil::decryptString($person->temppassword),
                        ]);
                    } else {
                        array_push($outputDataList, [
                            'psn_code' => $person->psn_code,
                            'psn_name' => $person->psn_name,
                            'cls_code' => $person->cls_code,
                            'cls_name' => $person->cls_name,
                            'temppassword' => PasswordUtil::decryptString($person->temppassword),
                        ]);
                    }
                } else {
                    if ($psnPswChangeFlag) {
                        $csvHeader = ['塾生ID', '氏名', 'メールアドレス', '仮パスワード'];
                        array_push($outputDataList, [
                            'psn_code' => $person->psn_code,
                            'psn_name' => $person->psn_name,
                            'mail_address' => $person->mail_address,
                            'temppassword' => PasswordUtil::decryptString($person->temppassword),
                        ]);
                    } else {
                        array_push($outputDataList, [
                            'psn_code' => $person->psn_code,
                            'psn_name' => $person->psn_name,
                            'temppassword' => PasswordUtil::decryptString($person->temppassword),
                        ]);
                    }
                }
            }
        } else {
            $outputDataList = [];
        }

        $responseHeader = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachement;filename='.config('constants.PERSON_DOWNLOAD_CSV_FILENAME'),
        ];

        return response()->streamDownload(function () use ($outputDataList, $csvHeader) {
            echo FileUtil::CreateCSV($outputDataList, $csvHeader);
        }, config('constants.PERSON_DOWNLOAD_CSV_FILENAME'), $responseHeader);

    }

    /**
     * 生徒マスタ削除処理を行う。
     *
     * @param  Request  $request  リクエスト
     * @param  $delSchCode  塾コード
     * @param  $delPsnCode  個人コード
     */
    public function delete(Request $request, $schCode, $delSchCode, $delPsnCode): RedirectResponse
    {

        // 削除処理
        $result = $this->service->delete((int) $delSchCode, (string) $delPsnCode);
        if ($result['resultCode'] == config('constants.RESULT_CODE_SYSTEM_ERROR')) {
            // システムエラーの場合（TODO:暫定）
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->to(route(RouteUtil::getPrefix().'.persons.index', [$schCode]))->with('message', $result['message']);
    }
}
