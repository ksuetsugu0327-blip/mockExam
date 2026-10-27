<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Services\ManagementListService;
use App\Utils\RouteUtil;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ManagementDocController extends Controller implements HasMiddleware
{
    // 管理資料一覧サービス
    private $service;

    /**
     * コンストラクタ
     *
     * @param  MSchoolService  $mSchoolService  塾マスタサービス
     */
    public function __construct(ManagementListService $managementListService)
    {
        // ログイン判定

        $this->service = $managementListService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_CLASS')
                .','.config('constants.GUARD_SURROGATE_SCHOOL').','.config('constants.GUARD_SURROGATE_CLASS'),
        ];
    }

    /**
     * 管理資料一覧画面を表示する。
     *
     * @param sch_code 塾コード
     */
    public function index(Request $request, int $sch_code): view
    {
        // dd($request);
        // 遷移元を判定し、セッション情報を削除する (見直しが必要）
        $referer = url()->previous();
        // カレントURLから塾マスタ部分だけを抽出する
        $current = str_replace('/list', '', url()->current());
        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $clsCode = $request->session()->get(config('constants.SESSION_KEY_CLSCODE'));
        $clsGradeCsvFlag = $request->session()->get(config('constants.SESSION_KEY_CLS_GRADE_CSV_FLG'));

        if ($request->session()->has(config('constants.SESSION_KEY_MANAGEMENTDOC_LIST'))) {

            $param = $request->session()->get(config('constants.SESSION_KEY_MANAGEMENTDOC_LIST'));
            $message = '';

            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }

            $checkParam = [
                'year' => $param['year'],
                'cnttest' => $param['cnttest'],
                'grade' => $param['grade'],
                'schCode' => $schCode,
                'categoryKbn' => 'sch',
            ];

            $dir = $this->service->checkDirectory($checkParam);
            // $checkParam["categoryKbn"] = 'csv'; ★20240423
            // Log::debug("function index クラスコード：". $clsCode." -clsGradeCsvFlag- ". $clsGradeCsvFlag. "\n");
            if (! empty($clsCode)) {
                $checkParam['categoryKbn'] = 'csv_cls';
            } else {
                $checkParam['categoryKbn'] = 'csv';
            }
            $csv_dir = $this->service->checkDirectory($checkParam);

            if ($dir > 0) {
                $param['dir'] = true;
            }
            if (isset($clsGradeCsvFlag)) {
                if ($clsGradeCsvFlag && $csv_dir > 0) {
                    $param['csv_dir'] = true;
                }
            } else {
                if ($csv_dir > 0) {
                    $param['csv_dir'] = true;
                }
            }

            $input = [
                'category_kbn' => ! isset($clsCode) ? config('constants.CATEGORY_KBN_SCH') : config('constants.CATEGORY_KBN_CLS'),
                'year' => $param['year'],
                'cnttest' => $param['cnttest'],
                'grade' => $param['grade'],
                'sch_code' => $schCode,
                'cls_code' => $clsCode,
            ];

            // zip情報取得
            $zipInfo = $this->service->zipDownloadPath($input);
            $param['zip'] = $zipInfo;
            $param['zipfilepass'] = isset($zipInfo) && isset($zipInfo->zipfilepass) ? $this->service->decrypt($zipInfo->zipfilepass) : null;

            // CSV zip情報取得
            if (! empty($clsCode)) {
                $input['category_kbn'] = 'csv_cls';
            } else {
                $input['category_kbn'] = 'csv';
            }
            $zipInfo_csv = $this->service->zipDownloadPath($input);
            $param['zip_csv'] = $zipInfo_csv;
            $param['zipfilepass_csv'] = isset($zipInfo_csv) && isset($zipInfo_csv->zipfilepass) ? $this->service->decrypt($zipInfo_csv->zipfilepass) : null;
            if ($dir == 0 && $csv_dir == 0) {

                return view('managementDoc')->with('message', '現在、ダウンロードできる仕様はありません')->with($param);
            }

            return view('managementDoc')->with($param)->with('message', $message);

        } else {

            return redirect()->route(RouteUtil::getPrefix().'.management.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])->with('message', trans('message.DOWNLODE_NOT_FILE'));

        }
    }

    /**
     * 管理資料一覧画面を表示する。
     *
     * @param sch_code 塾コード
     */
    public function open(Request $request, int $sch_code, int $index): View
    {
        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $clsCode = $request->session()->get(config('constants.SESSION_KEY_CLSCODE'));
        $clsGradeCsvFlag = $request->session()->get(config('constants.SESSION_KEY_CLS_GRADE_CSV_FLG'));

        // 試験回数一覧
        $managementList = $request->session()->get(config('constants.SESSION_KEY_MANAGEMENT_LIST'));
        // テスト情報
        $param = $managementList['directory'][$index];

        $checkParam = [
            'year' => $param['year'],
            'cnttest' => $param['cnttest'],
            'grade' => $param['grade'],
            'schCode' => $schCode,
            'categoryKbn' => 'sch',
        ];

        $dir = $this->service->checkDirectory($checkParam);
        // $checkParam["categoryKbn"] = 'csv'; ★20240423
        // Log::debug("function open クラスコード：". $clsCode." -clsGradeCsvFlag- ". $clsGradeCsvFlag. "\n");
        if (! empty($clsCode)) {
            $checkParam['categoryKbn'] = 'csv_cls';
        } else {
            $checkParam['categoryKbn'] = 'csv';
        }
        // var_dump($checkParam);
        $csv_dir = $this->service->checkDirectory($checkParam);

        if ($dir > 0) {
            $param['dir'] = true;
        }
        if (isset($clsGradeCsvFlag)) {
            if ($clsGradeCsvFlag && $csv_dir > 0) {
                $param['csv_dir'] = true;
            }
        } else {
            if ($csv_dir > 0) {
                $param['csv_dir'] = true;
            }
        }

        // 画面情報を連想配列に設定
        $input = [
            'category_kbn' => ! isset($clsCode) ? config('constants.CATEGORY_KBN_SCH') : config('constants.CATEGORY_KBN_CLS'),
            'year' => $param['year'],
            'cnttest' => $param['cnttest'],
            'grade' => $param['grade'],
            'sch_code' => $schCode,
            'cls_code' => $clsCode,
        ];

        // ZIP情報取得 ファイル情報退避
        $zipInfo = $this->service->zipDownloadPath($input);
        $param['zip'] = $zipInfo;
        $param['zipfilepass'] = isset($zipInfo) && isset($zipInfo->zipfilepass) ? $this->service->decrypt($zipInfo->zipfilepass) : null;

        // CSV zip情報取得
        if (! empty($clsCode)) {
            $input['category_kbn'] = 'csv_cls';
        } else {
            $input['category_kbn'] = 'csv';
        }
        $zipInfo_csv = $this->service->zipDownloadPath($input);
        $param['zip_csv'] = $zipInfo_csv;
        $param['zipfilepass_csv'] = isset($zipInfo_csv) && isset($zipInfo_csv->zipfilepass) ? $this->service->decrypt($zipInfo_csv->zipfilepass) : null;

        $request->session()->put(config('constants.SESSION_KEY_MANAGEMENTDOC_LIST'), $param);

        if ($dir == 0 && $csv_dir == 0) {

            return view('managementDoc')->with('message', '現在、ダウンロードできる仕様はありません')->with($param);
        }

        return view('managementDoc')->with($param);
    }

    /**
     * PDFダウンロードイベント
     *
     * @param sch_code 塾コード
     * @param categoryKbn 識別子
     */
    public function pdfDownload(Request $request, int $sch_Code, string $categoryKbn): response
    {

        // セッション情報取得
        $docList = $request->session()->get(config('constants.SESSION_KEY_MANAGEMENTDOC_LIST'));
        try {
            $directoryFile = $docList[$categoryKbn];
            $filename = basename($directoryFile->fullpath);

            $headers = [
                'Content-Type' => 'application/pdf; charset=UTF-8', 'Content-Disposition' => 'inline; filename*=UTF-8\'\''.rawurlencode($filename),
            ];

            $response = Storage::disk(config('constants.FILESYSTEM_PROTOCOL'))->download($directoryFile->fullpath, '', $headers);
        } catch (Exception $ex) {
            // 該当データがない場合
            report($ex);

            return redirect()->route(RouteUtil::getPrefix().'.management.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])->with('message', trans('message.DOWNLODE_NOT_FILE'));
        }

        return $response;
    }

    /**
     * ダウンロード指示を表示する。
     *
     * @param sch_code 塾コード
     */
    public function orderCSV(Request $request)
    {
        $request->request->add(['CATEGORY_KBN_CSV' => 'csv']);

        return self::order($request);
        // dd($request);
    }

    /**
     * ダウンロード指示を表示する。
     *
     * @param sch_code 塾コード
     */
    public function zipDownloadCSV(Request $request)
    {
        $request->request->add(['CATEGORY_KBN_CSV' => 'csv']);

        return self::zipDownload($request);
        // dd($request);
    }

    /**
     * ダウンロード指示を表示する。
     *
     * @param sch_code 塾コード
     */
    public function order(Request $request): RedirectResponse
    {

        // セッション情報取得
        $param = $request->session()->get(config('constants.SESSION_KEY_MANAGEMENTDOC_LIST'));
        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $clsCode = $request->session()->get(config('constants.SESSION_KEY_CLSCODE'));
        if ($request->has('CATEGORY_KBN_CSV')) {
            if (! empty($clsCode)) {
                $category_kbn = 'csv_cls';
            } else {
                $category_kbn = 'csv';
            }
        } else {
            $category_kbn = ! isset($clsCode) ? config('constants.CATEGORY_KBN_SCH') : config('constants.CATEGORY_KBN_CLS');
        }
        // dd($category_kbn);
        try {
            $param = [
                'year' => $param['year'],
                'cnttest' => $param['cnttest'],
                'grade' => $param['grade'],
                'category_kbn' => $category_kbn,
                'sch_code' => $schCode,
            ];

            // 画面情報を連想配列に設定
            $order = [
                'category_kbn' => $category_kbn,
                'process_kbn' => '0',
                'year' => $param['year'],
                'cnttest' => $param['cnttest'],
                'grade' => $param['grade'],
                'sch_code' => $schCode,
                'cls_code' => $clsCode,
                'zipfilepath' => null,
                'zipfilepass' => null,
                'order_at' => Carbon::now(),
                'zipfile_at' => null,
                'download_at' => null,
                'error_kbn' => '0',
            ];

            // 登録処理
            $result = $this->service->registerOrder($order);
            // dd($result);
            // ZIP情報取得 ファイル情報退避
            $zipInfo = $this->service->zipDownloadPath($order);
            // dd($zipInfo);
            $param['zip'] = $zipInfo;
            $param['zipfilepass'] = isset($zipInfo) && isset($zipInfo->zipfilepass) ? $this->service->decrypt($zipInfo->zipfilepass) : null;

            return redirect()->route(RouteUtil::getPrefix().'.managementdoc.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])->with($param)->with($result);
        } catch (Exception $ex) {
            // 該当データがない場合
            dd($ex);
            report($ex);

            return redirect()->route(RouteUtil::getPrefix().'.managementdoc.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])->with('message', trans('message.DOWNLODE_NOT_FILE'));
        }
    }

    /**
     * 管理資料一覧画面を表示する。
     *
     * @param sch_code 塾コード
     */
    public function zipDownload(Request $request): view
    {

        // セッション情報取得
        $param = $request->session()->get(config('constants.SESSION_KEY_MANAGEMENTDOC_LIST'));
        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $clsCode = $request->session()->get(config('constants.SESSION_KEY_CLSCODE'));

        if ($request->has('CATEGORY_KBN_CSV')) {
            if (! empty($clsCode)) {
                $category_kbn = 'csv_cls';
            } else {
                $category_kbn = 'csv';
            }
        } else {
            $category_kbn = ! isset($clsCode) ? config('constants.CATEGORY_KBN_SCH') : config('constants.CATEGORY_KBN_CLS');
        }

        try {
            $order = [
                'year' => $param['year'],
                'cnttest' => $param['cnttest'],
                'grade' => $param['grade'],
                'category_kbn' => $category_kbn,
                'sch_code' => $schCode,
                'cls_code' => $clsCode,
            ];

            // ZIP情報取得 ファイル情報退避
            $zipInfo = $this->service->zipDownloadPath($order);
            $param['zip'] = $zipInfo;
            $param['zipfilepass'] = isset($zipInfo) && isset($zipInfo->zipfilepass) ? $this->service->decrypt($zipInfo->zipfilepass) : null;
            if (! isset($zipInfo->zipfilepath)) {
                // 該当データがない場合
                return redirect()->route(RouteUtil::getPrefix().'.managementdoc.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])->with('message', trans('message.DOWNLODE_NOT_FILE'));
            }
            $basename = basename($zipInfo->zipfilepath);
            $filename = substr($basename, strpos($basename, '@') + 1);

            // 初回のみダウンロード日付更新処理
            $result = $this->service->updateOrder($zipInfo->id);

            // 接続先
            $headers = [
                'Content-Type' => 'application/zip; charset=UTF-8', 'Content-Disposition' => 'attachment; filename*=UTF-8\'\''.rawurlencode($filename),
            ];

            return Storage::disk(config('constants.FILESYSTEM_PROTOCOL'))->download($zipInfo->zipfilepath, '', $headers);

        } catch (Exception $ex) {
            // 該当データがない場合
            report($ex);

            return redirect()->route(RouteUtil::getPrefix().'.managementdoc.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))])->with('message', trans('message.DOWNLODE_NOT_FILE'));
        }
    }
}
