<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MPrefectureService;
use App\Services\MHighSchoolClassService;
use App\Services\MHighSchoolInstallService;
use App\Services\MUnivInstallService;
use App\Services\MUnivDeptLineService;
use App\Services\MOemSchoolService;

class UnivCodeSearchIndexController extends Controller
{
    public function __construct(
        MPrefectureService $mPrefectureService,
        MHighSchoolInstallService $mHighSchoolInstallService,
        MHighSchoolClassService $mHighSchoolClassService,
        MUnivInstallService $mUnivInstallService,
        MUnivDeptLineService $mUnivDeptLineService,
        MOemSchoolService $mOemSchoolService
    ){
        $this->prefectureService = $mPrefectureService;
        $this->highSchoolInstallService = $mHighSchoolInstallService;
        $this->highSchoolClassService = $mHighSchoolClassService;
        $this->univInstallService = $mUnivInstallService;
        $this->univDeptLineService = $mUnivDeptLineService;
        $this->oemSchoolService = $mOemSchoolService;
    }

    public function index(Request $request) {
        $gradeList = config('selectlistarray.UNVS_GRADE_LIST');
        $gradeList = array_slice($gradeList, 1, NULL, true);

        $prefList = $this->prefectureService->selectList();
        $highSchoolInstallCodeList = $this->highSchoolInstallService->selectList();
        $highSchoolClassList = $this->highSchoolClassService->selectList();
        $highSchoolOptionList = config('selectlistarray.HIGH_SCHOOL_CODE_SELECT.OPTIONS');
        $univInstallCodeList = $this->univInstallService->selectList();
        $univDeptLineList = $this->univDeptLineService->selectList();
        $deviationValueList = config('selectlistarray.UNIV_CODE_DEVIATION_VALUES');
        
        return view('univ_search', compact(
            'gradeList',
            'prefList',
            'highSchoolInstallCodeList',
            'highSchoolClassList',
            'highSchoolOptionList',
            'univInstallCodeList',
            'univDeptLineList',
            'deviationValueList',
        ));
    }
}
