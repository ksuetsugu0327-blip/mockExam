<?php

namespace App\Http\Controllers;

// use App\Models\MTestName;
// use App\Models\MUniversityCodeName;
// use App\Models\TTestInfo;
use App\Http\Requests\UnivTestListRequest;
use App\Http\Requests\UnivTestRegisterRequest;
use App\Services\TTestInfoService;
use App\Services\MOemSchoolService;
use App\Services\MTestNameService;
use App\Services\MUniversityCodeNameService;
use Illuminate\Http\Request;

class UnivTestController extends Controller
{
    private $service;
    private $oemSchoolService;
    private $testNameService;
    private $universityCodeNameService;
    
    /**
     * コンストラクタ
     * @param TTestInfoService $tTestService 模試テーブルサービス
     */
    public function __construct(
        TTestInfoService            $tTestService
        ,MOemSchoolService          $mOemSchoolService
        ,MTestNameService           $mTestNameService
        ,MUniversityCodeNameService $mUniversityCodeNameService
    )
    {
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->service = $tTestService;
        $this->oemSchoolService = $mOemSchoolService;
        $this->testNameService = $mTestNameService;
        $this->universityCodeNameService = $mUniversityCodeNameService;
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
            
            // 年度プルダウンを用意
            $years = $this->generateYearsArray();
            
            // 学年変換用マップを用意
            $grades = config('selectlistarray.UNVS_GRADE_LIST');
            $message = "";
            if($request->session()->has('message')){
                $message = $request->session()->get('message');
            }

            $conditions = ['year'=>'','test_name'=>''];

            return view('test_list')->with([
                'testList' => null,
                'resultCount' => 0,
                'message' => $message,
                'grades' => $grades,
                'years' => $years,
                'selectedYear' => $conditions['year'],
                'testNameKeyword' => $conditions['test_name']
            ]);
        }
        else{
           // 塾マスタ管理画面から来た場合、セッション情報を引き継いで検索処理を実施する
            $conditions = $request->session()->get(config('constants.SESSION_KEY_TTESTINFO_SEARCH_CONDITION'));
            
            // 年度プルダウンを用意
            $years = $this->generateYearsArray();

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

            return view('test_list', compact('testList', 'years'))
                ->with([
                    'resultCount' => $resultCount,
                    'message' => $message,
                    'grades' => $grades,
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
            'year'=>$request->get('year'),
            'test_name'=>$request->get('test_name'),
        ];

        // 年度プルダウンを用意
        $years = $this->generateYearsArray();

        // 学年変換用マップを用意
        $grades = config('selectlistarray.UNVS_GRADE_LIST');
            
        // 模試リストを取得
        $testList = $this->service->selectList($conditions);
//dd($testList);
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

        return view('test_list', compact('testList', 'years'))
            ->with('resultCount', $resultCount)
            ->with('message', $message)
            ->with('grades', $grades)
            ->with('selectedYear', $conditions['year'])
            ->with('testNameKeyword', $conditions['test_name']);
    }

    /**
     * テスト登録画面
     * @return \Illuminate\View\View テスト登録画面
     */
    public function register()
    {
        // 年度プルダウンを用意
        $years = $this->generateYearsArray();
        // 学年プルダウンを用意
        $grades = config('selectlistarray.UNVS_GRADE_LIST');
        // OEM塾を取得
        $oemSchoolList = $this->oemSchoolService->selectList()
                        ->pluck('oem_sch_name', 'oem_sch_code')
                        ->toArray();
        // テスト名を取得
        $testNameList = $this->testNameService->selectList()
                        ->pluck('test_name', 'test_name_cd')
                        ->toArray();
        // 大学コードデータ名を取得
        $universityCodeNameList = $this->universityCodeNameService->selectList()
                        ->pluck('university_code_name', 'university_code_name_cd')
                        ->toArray();

        return view('test_register', compact(
            'years',
            'grades',
            'oemSchoolList',
            'testNameList',
            'universityCodeNameList',
        ));
    }

    /**
     * テスト登録処理
     * @param UnivTestRegisterRequest $request
     */
    public function store(UnivTestRegisterRequest $request)
    {
        $testInfo = $request->all();
        // 登録処理
        $result = $this->service->register($testInfo);
        // 結果判定
        if ($result['resultCode'] == -1) {
            // システムエラーの場合、abort
            abort(500);
        }

        // 一覧画面へリダイレクト
        return redirect()->route('test.list',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])->with('message',$result['message']);
        // return redirect()->route(
        //     RouteUtil::getPrefix() . '.ticketList.index', [
        //         'schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))
        //     ]
        // );
    }

    private function generateYearsArray() {
        $currentYear = date('Y');
        $noOfFutureYears = config('constants.FUTURE_YEAR');
        $noOfPastYears = config('constants.PAST_YEAR');
        $years = [];

        // 年度未指定のoption
        $years[''] = "";
        
        // Add next year
        for ($i = 1; $i < $noOfFutureYears + 1; $i++) {
            $years[strval($currentYear + $i)] = $currentYear + $i;
        }

        // Add previous 4 years
        for ($i = 0; $i < $noOfPastYears + 1; $i++) {
            $years[strval($currentYear - $i)] = $currentYear - $i;
        }

        return $years;
    }
}
