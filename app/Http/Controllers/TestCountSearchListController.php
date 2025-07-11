<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TestCountSearchListService;

class TestCountSearchListController extends Controller
{
    
    /**
     * コンストラクタ
     * @param testCountSearchListService $testCountSearchListService マスタサービス
     */
    public function __construct(TestCountSearchListService $testCountSearchListService){
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_SCHOOL').','.config('constants.GUARD_CLASS')
                .','.config('constants.GUARD_SURROGATE_SCHOOL').','.config('constants.GUARD_SURROGATE_CLASS'));
        $this->service = $testCountSearchListService;
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
        $current = str_replace('/list','',url()->current());

        if(!preg_match("|^".$current."|", $referer) || 
                !$request->session()->has(config('constants.SESSION_KEY_TESTCOUNT_SEARCH_CONDITION'))){
            // 塾マスタ画面外から来た場合、または未検索時
            if($request->session()->has(config('constants.SESSION_KEY_TESTCOUNT_SEARCH_CONDITION'))){
                // 検索値がセッションに格納されている場合、セッションをクリアして初期表示
                $request->session()->forget(config('constants.SESSION_KEY_TESTCOUNT_SEARCH_CONDITION'));
            }
            $message = "";
            if($request->session()->has('message')){
                $message = $request->session()->get('message');
            }
            
            $conditions = [
                'sch_code' => null,  // 塾コード
                'cls_code' => null,  // 教室コード
                'year' => null,       // 年度
                'testcnt' => null,    // 回数
                'grade' => null,      // 学年
            ];
            
            return view('testCountSearchList')
                    ->with('directory',null)
                    ->with('resultCount',0)
                    ->with($conditions)
                    ->with($message);
            
        }else{
            
           // セッション情報を引き継いで検索処理を実施する
            $message = "";
            if($request->session()->has('message')){
                $message = $request->session()->get('message');
            }
            
            // セッションから検索条件取得
            $conditions = $request->session()->get(config('constants.SESSION_KEY_TESTCOUNT_SEARCH_CONDITION'));
            // 検索
            $screenList = $this->service->search($conditions);
            $resultCount = isset($screenList) ? count($screenList) : 0;
        
            $directory = [
                'directory' => $screenList, // 一覧
                'resultCount' => $resultCount,
                'message' => $message,
            ];
            
            return view('testCountSearchList')->with($directory)->with($conditions);
        }
    }

    /**
     * 検索処理
     * @param  \App\index  $index
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        // 検索条件の値を取得
        // 塾コード
        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        // 教室コード
        $clsCode = $request->session()->get(config('constants.SESSION_KEY_CLSCODE'));
        
        $conditions = [
            'sch_code' => $schCode,                 // 塾コード
            'cls_code' => $clsCode,                 // 教室コード
            'year' => $request->get('year'),        // 年度
            'testcnt' => $request->get('testcnt'),  // 回数
            'grade' => $request->get('grade'),      // 学年
            'path_kbn' => config('constants.PATH_KBN_FILE'), // パス区分:ファイル
            'category_kbn' => config('constants.FILE_KBN_PSN'), // 識別子:個人
        ];
 
        // 検索
        $screenList = $this->service->search($conditions);
        $resultCount = isset($screenList) ? count($screenList) : 0;
        if($resultCount==0){
            // 該当データがない場合
            $message = trans('message.SEARCH_NO_DATA');
        } else {
            $message = "";
        }
        
        $directory = [
            'directory' => $screenList ,    // 一覧
            'resultCount' => $resultCount , 
            'message' => $message ,
        ];
        
        // 検索条件退避
        $request->session()->put(config('constants.SESSION_KEY_TESTCOUNT_LIST'),$directory);
        $request->session()->put(config('constants.SESSION_KEY_MANAGEMENT_CONDITION'),$conditions);
        
        return view('testCountSearchList')->with($directory)->with($conditions);
    }

    /**
     * 個人成績表公開フラグ更新処理
     * @param  \App\index  $index
     * @return \Illuminate\Http\Response
     */
    public function updatePublishFlag(Request $request)
    {
        // 検索条件の値を取得
        $schCode = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
        $clsCode = $request->session()->get(config('constants.SESSION_KEY_CLSCODE'));
        
        $conditions = [
            'sch_code' => $schCode,
            'cls_code' => $clsCode,
            'year' => $request->get('year'),
            'cnttest' => $request->get('cnttest'),
            'grade' => $request->get('grade'),
            'path_kbn' => config('constants.PATH_KBN_FILE'), // パス区分:ファイル
            //'category_kbn' => config('constants.FILE_KBN_PSN'), // 識別子:個人
            'psn_report_publish_flg' => $request->get('psn_report_publish_flg')
        ];

        // 更新処理実行
        $result = $this->service->updatePsnReportPublishFlg($conditions);

        return redirect()->back()->with('message', $result);
    }
}
