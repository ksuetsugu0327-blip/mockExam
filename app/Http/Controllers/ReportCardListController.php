<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Services\ReportCardListService;
use App\Utils\RouteUtil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportCardListController extends Controller implements HasMiddleware
{
    /**
     * コンストラクタ
     *
     * @param  MSchoolService  $reportCardListService  成績一覧サービス
     */
    public function __construct(ReportCardListService $reportCardListService)
    {
        // ログイン判定

        $this->service = $reportCardListService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_CLASS')
                .','.config('constants.GUARD_SURROGATE_SCHOOL').','.config('constants.GUARD_SURROGATE_CLASS'),
        ];
    }

    /**
     * 初期表示
     */
    public function index(Request $request, int $schCode, int $index): View
    {
        // 遷移元を判定し、セッション情報を削除する (見直しが必要）
        $referer = url()->previous();
        // カレントURLから塾マスタ部分だけを抽出する
        $current = str_replace('/list', '', url()->current());

        if (! preg_match('|^'.$current.'|', $referer)) {
            // 塾コード
            $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
            // 教室コード
            $clsCode = $request->session()->get(config('constants.SESSION_KEY_CLSCODE'));
            // 試験回数一覧
            $testCountlist = $request->session()->get(config('constants.SESSION_KEY_TESTCOUNT_LIST'));
            // テスト情報
            $param = $testCountlist['directory'][$index];
            // 検索
            $selectList = $this->service->selectTestCountPearsonList($schCode, $clsCode, $param);
            $resultCount = $selectList->count();
            if ($resultCount == 0) {
                // 該当データがない場合
                $message = trans('message.SEARCH_NO_DATA');
            } else {
                $message = '';
            }

            // クラス用プルダウン情報取得
            $classSelectBox = ['' => '---'];
            $classSelectBox += $selectList->pluck('cls_name', 'cls_code')->toArray();

            // $classBoxList = $this->service->selectClassBoxList($schCode);
            // $classSelectBox = array('' => '---');
            // $classSelectBox += $classBoxList->pluck('name','cls_code')->toArray();

            $list = [
                'directory' => $selectList,    // 一覧
                'resultCount' => $resultCount,
                'classBox' => $classSelectBox,
                'message' => $message,
            ];

            // 検索条件退避
            $request->session()->put(config('constants.SESSION_KEY_REPORTCARD_LIST'), $list);

            return view('reportCardList')->with($param)->with($list);
        }
    }

    /**
     * 個人帳票ダウンロードを表示する。
     *
     * @param  type  $sch_Code
     * @param  type  $index
     */
    public function pdfDownload(Request $request, $sch_Code, $index): type
    {

        // セッション情報取得
        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $clsCode = $request->session()->get(config('constants.SESSION_KEY_CLSCODE'));
        $docList = $request->session()->get(config('constants.SESSION_KEY_REPORTCARD_LIST'));

        try {

            // PDF情報取得
            $dir = $docList['directory'][$index];
            $param = [
                'category_kbn' => config('constants.FILE_KBN_PSN'),
                'sch_code' => $schCode,
                'cls_code' => $clsCode,
                'year' => $dir->year,
                'grade' => $dir->grade,
                'cnttest' => $dir->cnttest,
            ];
            $filename = basename($dir->fullpath);

            $headers = [
                'Content-Type' => 'application/pdf; charset=UTF-8', 'Content-Disposition' => 'inline; filename*=UTF-8\'\''.rawurlencode($filename),
            ];

            $response = Storage::disk(config('constants.FILESYSTEM_PROTOCOL'))->download($dir->fullpath, '', $headers);

        } catch (Exception $ex) {
            // 該当データがない場合
            report($ex);

            return redirect()->route(RouteUtil::getPrefix().'.testcountsearch.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])->with('message', trans('message.DOWNLODE_NOT_FILE'));
        }

        return $response;
    }
}
