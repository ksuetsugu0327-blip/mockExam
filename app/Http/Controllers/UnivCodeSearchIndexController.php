<?php

namespace App\Http\Controllers;

use App\Services\MHighSchoolClassService;
use App\Services\MHighSchoolInstallService;
use App\Services\MOemSchoolService;
use App\Services\MPrefectureService;
use App\Services\MUnivDeptLineService;
use App\Services\MUnivInstallService;
use Illuminate\Http\Request;

class UnivCodeSearchIndexController extends Controller
{
    public function __construct(
        MPrefectureService $mPrefectureService,
        MHighSchoolInstallService $mHighSchoolInstallService,
        MHighSchoolClassService $mHighSchoolClassService,
        MUnivInstallService $mUnivInstallService,
        MUnivDeptLineService $mUnivDeptLineService,
        MOemSchoolService $mOemSchoolService
    ) {
        $this->prefectureService = $mPrefectureService;
        $this->highSchoolInstallService = $mHighSchoolInstallService;
        $this->highSchoolClassService = $mHighSchoolClassService;
        $this->univInstallService = $mUnivInstallService;
        $this->univDeptLineService = $mUnivDeptLineService;
        $this->oemSchoolService = $mOemSchoolService;
    }

    public function index(Request $request)
    {
        $gradeList = config('selectlistarray.UNVS_GRADE_LIST');
        $gradeList = array_slice($gradeList, 1, null, true);

        $prefList = $this->prefectureService->selectList();
        $highSchoolInstallCodeList = $this->highSchoolInstallService->selectList();
        $highSchoolClassList = $this->highSchoolClassService->selectList();
        $highSchoolOptionList = config('selectlistarray.HIGH_SCHOOL_CODE_SELECT.OPTIONS');
        $univInstallCodeList = $this->univInstallService->selectList();
        $univDeptLineList = $this->univDeptLineService->selectList();
        $deviationValueList = config('selectlistarray.UNIV_CODE_DEVIATION_VALUES');

        $guideBookFolder = config('constants.UNVC_GUIDEBOOK_PATH');
        $guideBookFileName = config('constants.GUIDE_BOOK_NAME');
        $guideBookPath = $guideBookFolder.'/'.$guideBookFileName;

        $hasGuideBookLink = false;
        if (file_exists(public_path($guideBookPath)) && is_file(public_path($guideBookPath))) {
            $hasGuideBookLink = true;
        }

        return view('univ_search', compact(
            'gradeList',
            'prefList',
            'highSchoolInstallCodeList',
            'highSchoolClassList',
            'highSchoolOptionList',
            'univInstallCodeList',
            'univDeptLineList',
            'deviationValueList',
            'hasGuideBookLink',
            'guideBookPath',
        ));
    }
}
