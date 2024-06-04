<?php

namespace App\Http\Controllers\Rest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MOemSchoolService;
use App\Services\TTestInfoService;
use App\Services\MUnivInstallService;
use App\Services\UnivCodeHeadService;
use App\Services\MUnivCodeService;
use App\Services\MPrefectureService;
use App\Services\MHighSchoolInstallService;
use App\Services\MHighSchoolClassService;
use App\Services\MHighSchoolCodeService;


class UnivCodeSearchController extends Controller
{
    private $oemSchoolService;
    private $testNameService;
    private $prefectureService;
    private $highSchoolInstallService;
    private $highSchoolClassService;
    private $highSchoolCodeService;
    private $univInstallService;
    private $univCodeService;

    private $common;

    /**
     * コンストラクタ
     * @param MOemSchoolService $mOemSchoolService OEM塾マスタサービス
     */
    public function __construct(
        MOemSchoolService $mOemSchoolService,
        TTestInfoService $mTestInfoService,
        MPrefectureService $mPrefectureService,
        MHighSchoolInstallService $mHighSchoolInstallService,
        MHighSchoolClassService $mHighSchoolClassService,
        MHighSchoolCodeService $mHighSchoolCodeService,
        MUnivInstallService $mUnivInstallService,
        MUnivCodeService $mUnivCodeService,
        RestCommon $common
    ){
        $this->oemSchoolService = $mOemSchoolService;
        $this->testInfoService = $mTestInfoService;
        $this->prefectureService = $mPrefectureService;
        $this->highSchoolInstallService = $mHighSchoolInstallService;
        $this->highSchoolClassService = $mHighSchoolClassService;
        $this->highSchoolCodeService = $mHighSchoolCodeService;
        $this->univInstallService = $mUnivInstallService;
        $this->univCodeService = $mUnivCodeService;
        $this->common = $common;
    }

    public function index(Request $request) {
        $prefList = $this->prefectureService->selectList();
        $highSchoolInstallCodeList = $this->highSchoolInstallService->selectList();
        $highSchoolClassList = $this->highSchoolClassService->selectList();
        $highSchoolOptionList = config('selectlistarray.HIGH_SCHOOL_CODE_SELECT.OPTIONS');

        $univInstallCodeList = $this->univInstallService->selectList();
dd($univInstallCodeList);
        return view('univ_search', compact(
            'prefList',
            'highSchoolInstallCodeList',
            'highSchoolClassList',
            'highSchoolOptionList',
            'univInstallCodeList',
        ));
    }

    /**
     * 塾名マスタリスト取得処理
     *
     * @return JSON 塾名マスタリスト
     */
    public function getOemSchoolName(Request $request){
        $OEMSchCode = $request->get('oem');

        try{
            $list = $this->oemSchoolService->getOemSchoolName($OEMSchCode);
        }
        catch (\Exception $e){        
            $header = $this->common->makeHeader('error', '', 0);
            $data = [];

            return response()->json(compact('header', 'data', 500));
        }

        if( empty($list) || count($list) > 1){
            $header = $this->common->makeHeader('success', '', 1);
            $data = [
                'oem_sch_code' => config('constants.UNVC_COMMON_SCHOOL.SCH_CODE'),
                'oem_sch_name' => config('constants.UNVC_COMMON_SCHOOL.SCH_NAME'),
            ];

            return response()->json(compact('header', 'data'));
        }
        else{
            $item = $list[0];

            $header = $this->common->makeHeader('success', '', 1);

            $data = [
                'oem_sch_code' => $item->oem_sch_code,
                'oem_sch_name' => $item->oem_sch_name,
            ];
    
            return response()->json(compact('header', 'data'));
        }
    }

    /**
     * テスト名リスト取得処理
     *
     * @return JSON テスト名リストマスタリスト
     */
    public function getTestInfo(Request $request){
        $OEMSchCode = $request->get('oem');
        $grade = $request->get('grd');
        //return response()->json(compact('OEMSchCode', 'grade'));

        try{
            $list = $this->testInfoService->getTestInfo($OEMSchCode, $grade);
        }
        catch (\Exception $e){
            $header = $this->common->makeHeader('error', '', 0);
            $data = [];

            return response()->json(compact('header', 'data', 500));
        }

        if (empty($list)){
            $header = $this->common->makeHeader('success', 'テスト情報が登録されていません', 0);
            $data = [];
            
            return response()->json(compact('header', 'data'));
        }

        $header = $this->common->makeHeader('success', '', count($list)??0);

        $rows = [];

        foreach($list as $key => $test) {
            $rows[] = [
                'test_code' => $test->id,
                'test_name' => $test->test_name,
            ];
        }

        $data = [
            'test_info_list' => $rows,
        ];

        return response()->json(compact('header', 'data'));
    }

    /**
     * 大学コードヘッダー情報取得処理
     *
     * @return JSON 大学コードヘッダー情報
     */
    public function getUnivCodeHead(Request $request){
        $testID = $request->get('testid');
        try{
            $result = $this->testInfoService->getUnivCodeHead($testID);
        }
        catch (\Exception $e){
            $header = $this->common->makeHeader('error', '', 0);
            $data = [];

            return response()->json(compact('header', 'data', 500));
        }

        if (empty($result)){
            $header = $this->common->makeHeader('success', '大学コード情報が登録されていません', 0);
            $data = [];
            
            return response()->json(compact('header', 'data'));
        }

        $header = $this->common->makeHeader('success', '', 1);

        $data = [
            'university_code_head_id' => $result->university_code_head_id,
            'dept_subject_div' => $result->dept_subject_div,
        ];

        return response()->json(compact('header', 'data'));
    }

    /**
     * 高校コード検索用
     *
     * @return JSON 高校コード検索用
     */
    public function getHighSchoolCodeList(Request $request){
        $prefecture = $request->get('prf');
        $highSchoolName = $request->get('hsn');
        $founder = $request->get('hinst');
        $highSchoolClass = $request->get('hcls');

        $conditions = [
            'prefecture_cd'   => $prefecture,
            'highschool_name' => $highSchoolName,
            'install_cd'      => $founder,
            'class_cd'        => $highSchoolClass,
        ];

        try{
            $result = $this->highSchoolCodeService->getHighSchoolCode($conditions);
        }
        catch (\Exception $e){
            $header = $this->common->makeHeader('error', '', 0);
            $data = [];

            return response()->json(compact('header', 'data', 500));
        }

        $header = $this->common->makeHeader('success', '', count($result)??0);

        $rows = [];

        foreach($result as $highSchoolCode) {
            $rows[] = [
                'highschool_code' => $highSchoolCode->highschool_code,
                'highschool_name' => $highSchoolCode->highschool_name,
            ];
        }

        $data = [
            'high_school_code_list' => $rows,
        ];

        return response()->json(compact('header', 'data'));
    }
    
    /**
     * 大学コード検索用
     *
     * @return JSON 大学コード検索用
     */
    public function getUniversityCodeList(Request $request){
        $mode = $request->get('mode');
        if (mode == 1){
            $conditions = [
                'univHeaderID' => $request->get('unvhd'),
                'deptSubjDiv' => $request->get('dptsub'),
                'uInst1' => $request->get('uinst1'),
                'univName' => $request->get('unvm'),
                'deptName' => $request->get('udpnm'),
                'subjName' => $request->get('usubnm'),
                'nextOffset' => $request->get('next'),
            ];
        }

        if(mode == 2){

        }

        $result = $this->univCodeService->getUnivCount($conditions);

        $header = $this->common->makeHeader('success', '', $result??0);

        return response()->json(compact('header', 'data'));

    }
}
