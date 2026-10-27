<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Services\PersonReportCardListService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonReportCardListController extends Controller
{
    /**
     * コンストラクタ
     *
     * @param  MSchoolService  $reportCardListService  成績一覧サービス
     */
    public function __construct(PersonReportCardListService $personReportCardListService)
    {
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_PERSON').','.config('constants.GUARD_SURROGATE_PERSON'));
        $this->service = $personReportCardListService;
    }

    public function index(Request $request): View
    {
        $referer = url()->previous();
        // カレントURLから塾マスタ部分だけを抽出する
        $current = str_replace('/list', '', url()->current());

        if (! preg_match('|^'.$current.'|', $referer)) {
            // 塾コード
            $schCode = (int) $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
            // 教室コード
            $clsCode = (int) $request->session()->get(config('constants.SESSION_KEY_CLSCODE'));
            // 塾生ID
            $psnId = (string) $request->session()->get(config('constants.SESSION_KEY_PSNCODE'));
            // 生徒公開フラグ
            $psnReportPublishFlg = (string) $request->session()->get(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG'));
            // 検索
            $selectList = $this->service->search($schCode, $clsCode, $psnId);
            // 検索結果のレコード数を取得する
            $resultCount = isset($selectList) ? count($selectList) : 0;

            /* 生徒公開を利用する場合 */
            if (isset($selectList) && $psnReportPublishFlg != config('constants.PSN_REPORT_PUBLISH_NOT_USE_FLG')) {
                // 生徒公開フラグが 1 のレコード数をカウントする
                $resultCount = 0;
                foreach ($selectList as $item) {
                    if ($item['psn_report_publish_flg'] == '1') {
                        $resultCount++;
                    }
                }
            }

            if ($resultCount == 0) {
                // 該当データがない場合
                $message = trans('message.SEARCH_NO_DATA');
            } else {
                $message = '';
            }

            $list = [
                'directory' => $selectList,    // 一覧
                'resultCount' => $resultCount,
                'message' => $message,
            ];

            // 検索条件退避
            $request->session()->put(config('constants.SESSION_KEY_REPORTCARD_LIST'), $list);

            return view('personReportCardList')->with($list);
        } else {

            // セッション情報を引き継いで検索処理を実施する
            $message = '';
            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }

            // 塾コード
            $list = $request->session()->get(config('constants.SESSION_KEY_REPORTCARD_LIST'));

            $list['message'] = $message;
            // 検索条件退避
            $request->session()->put(config('constants.SESSION_KEY_REPORTCARD_LIST'), $list);

            return view('personReportCardList')->with($list);

        }
    }

    /**
     * 個人帳票ダウンロードを表示する。
     *
     * @param sch_code 塾コード
     */
    public function pdfDownload($sch_Code, $index, string $category_kbn): view
    {

        try {
            // セッション情報取得
            $schCode = session()->get(config('constants.SESSION_KEY_SCHCODE'));
            $clsCode = session()->get(config('constants.SESSION_KEY_CLSCODE'));
            $list = session()->get(config('constants.SESSION_KEY_REPORTCARD_LIST'));

            // 取得
            $dir = $list['directory'][$index];
            $filePath = $dir[$category_kbn]->fullpath;
            $filename = basename($filePath);

            $headers = [
                'Content-Type' => 'application/pdf; charset=UTF-8', 'Content-Disposition' => 'inline; filename*=UTF-8\'\''.rawurlencode($filename),
            ];

            $response = Storage::disk(config('constants.FILESYSTEM_PROTOCOL'))->download($filePath, '', $headers);

        } catch (Exception $ex) {
            // 該当データがない場合
            report($ex);

            return redirect()->route('personReportCardList.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])->with('message', trans('message.DOWNLODE_NOT_FILE'));
        }

        return $response;
    }
}
