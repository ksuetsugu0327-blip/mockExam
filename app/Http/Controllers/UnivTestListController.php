<?php

namespace App\Http\Controllers;

// use App\Models\MTestName;
// use App\Models\MUniversityCodeName;
// use App\Models\TTestInfo;
use App\Http\Requests\UnivTestListRequest;
use App\Http\Requests\UnivTestRegisterRequest;
use App\Services\TTestInfoService;
use App\Services\MOemSchoolService;
use Illuminate\Http\Request;

class UnivTestListController extends Controller
{
    private $service;
    
    /**
     * コンストラクタ
     * @param TTestInfoService $tTestService 模試テーブルサービス
     */
    public function __construct(TTestInfoService $tTestService, MOemSchoolService $mOemSchoolService)
    {
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->service = $tTestService;
        $this->oemSchoolService = $mOemSchoolService;
    }

    public function index(Request $request) {
        $referer = url()->previous();
        // カレントURLから塾マスタ部分だけを抽出する
        $current = str_replace('-list','',url()->current());

        // 塾マスタ画面外から来た場合（ただし、代理ログイン戻りの場合は対象外）、または未検索時
        if(!$request->session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGOUT')) && 
                (!preg_match("|^".$current."|", $referer) || !$request->session()->has(config('constants.SESSION_KEY_TTESTINFO_SEARCH_CONDITION')))){
            
            // セッションをクリア
            $request->session()->forget(config('constants.SESSION_KEY_TTESTINFO_SEARCH_CONDITION'));

            //OEM塾プルダウンを用意
            $oemSchools = $this->oemSchoolService->selectList();
            
            // 年度プルダウンを用意
            $years = $this->service->generateYearsArray();
            
            // 学年変換用マップを用意
            $grades = config('selectlistarray.UNVS_GRADE_LIST');
            $message = "";
            if($request->session()->has('message')){
                $message = $request->session()->get('message');
            }

            $conditions = ['year'=>'','test_name'=>'', 'oem_school'=>''];

            return view('test_list')->with([
                'testList' => null,
                'resultCount' => 0,
                'message' => $message,
                'oemSchools' => $oemSchools,
                'grades' => $grades,
                'years' => $years,
                'selectedOEM' => $conditions['oem_school'],
                'selectedYear' => $conditions['year'],
                'testNameKeyword' => $conditions['test_name']
            ]);
        }
        else{
           // 塾マスタ管理画面から来た場合、セッション情報を引き継いで検索処理を実施する
            $conditions = $request->session()->get(config('constants.SESSION_KEY_TTESTINFO_SEARCH_CONDITION'));

            //OEM塾プルダウンを用意
            $oemSchools = $this->oemSchoolService->selectList();
            
            // 年度プルダウンを用意
            $years = $this->service->generateYearsArray();

            // 学年変換用マップを用意
            $grades = config('selectlistarray.UNVS_GRADE_LIST');
            $message = "";

            if($request->session()->has('message')){
                $message = $request->session()->get('message');
            }

            // 模試リストを取得
            $testList = $this->service->selectList($conditions);

            // 表示件数を取得
            $resultCount = $testList->count();

            return view('test_list', compact('testList', 'years','oemSchools'))
                ->with([
                    'resultCount' => $resultCount,
                    'message' => $message,
                    'grades' => $grades,
                    'selectedOEM' => $conditions['oem_school'],
                    'selectedYear' => $conditions['year'],
                    'testNameKeyword' => $conditions['test_name']
                ]);
        }
    }

    /**
     * 模擬試験・一覧画面での検索処理
     *
     * @return view
     */
    public function search(UnivTestListRequest $request) {

        // 検索条件の値を取得
        $conditions = [
            'oem_school'=>$request->get('oem_school'),
            'year'=>$request->get('year'),
            'test_name'=>$request->get('test_name'),
        ];

        //OEM塾プルダウンを用意
        $oemSchools = $this->oemSchoolService->selectList();

        // 年度プルダウンを用意
        $years = $this->service->generateYearsArray();

        // 学年変換用マップを用意
        $grades = config('selectlistarray.UNVS_GRADE_LIST');
            
        // 模試リストを取得
        $testList = $this->service->selectList($conditions);

        // 表示件数を取得
        $resultCount = $testList->count();
        
        $message='';

        if($resultCount==0){
            // 該当データがない場合
            $message = trans('message.SEARCH_NO_DATA');
        }
        else if ($resultCount > config('constants.VIEW_RECORD_COUNT')){
            // 明細表示最大行数を超えている場合、メッセージを表示
            $message = trans('message.WARN_OVER_VIEW_RECORD_COUNT',['COUNT'=>config('constants.VIEW_RECORD_COUNT')]);
            $testList = $testList->take(config('constants.VIEW_RECORD_COUNT'));
            $resultCount = config('constants.VIEW_RECORD_COUNT');
        }

        $request->session()->put(config('constants.SESSION_KEY_TTESTINFO_SEARCH_CONDITION'),$conditions);

        return view('test_list', compact('testList', 'years','oemSchools'))
            ->with('resultCount', $resultCount)
            ->with('message', $message)
            ->with('grades', $grades)
            ->with('selectedOEM', $conditions['oem_school'])
            ->with('selectedYear', $conditions['year'])
            ->with('testNameKeyword', $conditions['test_name']);
    }
}
