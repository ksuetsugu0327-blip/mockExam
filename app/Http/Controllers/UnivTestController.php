<?php

namespace App\Http\Controllers;

// use App\Models\MTestName;
// use App\Models\MUniversityCodeName;
// use App\Models\TTestInfo;
use App\Services\TTestInfoService;
use Illuminate\Http\Request;

class UnivTestController extends Controller
{
    private $service;
    
    /**
     * コンストラクタ
     * @param TTestInfoService $mSchoolService 塾マスタサービス
     */
    public function __construct(TTestInfoService $tTestService){
        // ログイン判定
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->service = $tTestService;
    }

    public function index() {
        $years = $this->generateYearsArray();
        $conditions = [];
        $testLists = $this->service->selectList($conditions);
//dd($testLists);
        return view('test_list', compact(
            'testLists',
            'years',
            //'mUnivCodeName',
            //'deptSubjSelect'
        ));
    }

    private function generateYearsArray() {
        $currentYear = date('Y');
        $years = [];

        // Add next year
        $years[] = $currentYear + 1;

        // Add previous 4 years
        for ($i = 0; $i < 5; $i++) {
            $years[] = $currentYear - $i;
        }

        return $years;
    }
}
