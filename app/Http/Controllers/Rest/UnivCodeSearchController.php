<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Services\MHighSchoolCodeService;
use App\Services\MOemSchoolService;
use App\Services\MUnivCodeService;
use App\Services\TTestInfoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnivCodeSearchController extends Controller
{
    private $oemSchoolService;

    private $testNameService;

    private $highSchoolCodeService;

    private $univCodeService;

    private $common;

    public function __construct(
        MOemSchoolService $mOemSchoolService,
        TTestInfoService $mTestInfoService,
        MHighSchoolCodeService $mHighSchoolCodeService,
        MUnivCodeService $mUnivCodeService,
        RestCommon $common
    ) {
        $this->oemSchoolService = $mOemSchoolService;
        $this->testInfoService = $mTestInfoService;
        $this->highSchoolCodeService = $mHighSchoolCodeService;
        $this->univCodeService = $mUnivCodeService;
        $this->common = $common;
    }

    /**
     * 塾名マスタリスト取得処理
     *
     * @return JSON 塾名マスタリスト
     */
    public function getOemSchoolName(Request $request)
    {
        $OEMSchCode = $request->get('oem');

        try {
            $OEMSchool = $this->oemSchoolService->getOemSchoolByCode($OEMSchCode);
        } catch (\Exception $e) {
            $header = $this->common->makeHeader('error', '', 0);
            $data = [];

            return response()->json(compact('header', 'data', 500));
        }

        if (empty($OEMSchool)) {  // 塾マスターがみつからない場合、デフォルト塾名を返す
            $header = $this->common->makeHeader('success', '', 1);
            $defaultOEMCode = config('constants.UNVC_COMMON_SCHOOL.SCH_CODE');
            $defaultOEMName = $this->oemSchoolService->getOemSchoolByCode($defaultOEMCode)->oem_sch_name;
            $data = [
                'oem_sch_code' => $defaultOEMCode,
                'oem_sch_name' => $defaultOEMName,
            ];

            return response()->json(compact('header', 'data'));
        } else {

            $header = $this->common->makeHeader('success', '', 1);

            $data = [
                'oem_sch_code' => $OEMSchool->oem_sch_code,
                'oem_sch_name' => $OEMSchool->oem_sch_name,
            ];

            return response()->json(compact('header', 'data'));
        }
    }

    /**
     * テスト名リスト取得処理
     *
     * @return JSON テスト名リストマスタリスト
     */
    public function getTestInfo(Request $request)
    {
        $OEMSchCode = $request->get('oem');
        $grade = $request->get('grd');
        // return response()->json(compact('OEMSchCode', 'grade'));

        try {
            $list = $this->testInfoService->getTestInfo($OEMSchCode, $grade);
        } catch (\Exception $e) {
            $header = $this->common->makeHeader('error', '', 0);
            $data = [];

            return response()->json(compact('header', 'data', 500));
        }

        if (empty($list)) {
            $header = $this->common->makeHeader('success', 'テスト情報が登録されていません', 0);
            $data = [];

            return response()->json(compact('header', 'data'));
        }

        $header = $this->common->makeHeader('success', '', count($list) ?? 0);

        $rows = [];

        foreach ($list as $key => $test) {
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
    public function getUnivCodeHead(Request $request)
    {
        $testID = $request->get('testid');
        try {
            $result = $this->testInfoService->getUnivCodeHead($testID);
        } catch (\Exception $e) {
            $header = $this->common->makeHeader('error', '', 0);
            $data = [];

            return response()->json(compact('header', 'data', 500));
        }

        if (empty($result)) {
            $header = $this->common->makeHeader('success', '大学コード情報が登録されていません', 0);
            $data = [];

            return response()->json(compact('header', 'data'));
        }

        $header = $this->common->makeHeader('success', '', 1);

        $data = [
            'university_code_head_id' => $result->university_code_head_id,
            'codebook_file_name' => $result->codebook_file_name,
            'dept_subject_div' => $result->dept_subject_div,
        ];

        return response()->json(compact('header', 'data'));
    }

    /**
     * 高校コード検索用
     *
     * @return JSON 高校コード検索用
     */
    public function getHighSchoolCodeList(Request $request)
    {
        $prefecture = $request->get('prf');
        $highSchoolName = $request->get('hsn');
        $founder = $request->get('hinst');
        $highSchoolClass = $request->get('hcls');

        $conditions = [
            'prefecture_cd' => $prefecture,
            'highschool_name' => $highSchoolName,
            'install_cd' => $founder,
            'class_cd' => $highSchoolClass,
        ];

        try {
            $result = $this->highSchoolCodeService->getHighSchoolCode($conditions);
        } catch (\Exception $e) {
            $header = $this->common->makeHeader('error', '', 0);
            $data = [];

            return response()->json(compact('header', 'data', 500));
        }

        $header = $this->common->makeHeader('success', '', count($result) ?? 0);
        $rows = [];

        if (count($result) > 0) {

            foreach ($result as $highSchoolCode) {
                $rows[] = [
                    'highschool_code' => $highSchoolCode->highschool_code,
                    'highschool_name' => $highSchoolCode->highschool_name,
                ];
            }
        } else {
            $conditions = [
                'prefecture_cd' => $prefecture,
                'install_cd' => 9,
                'class_cd' => 9,
            ];

            $result = $this->highSchoolCodeService->getHighSchoolCode($conditions);

            if (count($result) == 1) {
                $header = $this->common->makeHeader('success', '', 1);

                $rows[] = [
                    'highschool_code' => $result->first()->highschool_code,
                    'highschool_name' => $result->first()->highschool_name,
                ];

            } else {
                $header = $this->common->makeHeader('fail', '上記の以外の高等学校等正しく取得できなかった。', count($result));
            }
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
    public function getUniversityCodeList(Request $request)
    {
        $mode = intval($request->get('mode'));
        $offset_end = $request->get('next');
        $page_unit = config('constants.UNVC_PER_PAGE');
        $deptSubjDiv = $request->get('dptsub');
        $univ_num = '';
        $dept_num = '';
        $subj_num = '';
        $conditions = [];
        // Debug用
        // Log::Warning('Mode: '. $mode);

        switch ($mode) {
            // 大学一覧(名称検索)
            case 1:  // 検索ボタン
            case 11: // 大学一覧のもっと見る
                $conditions = [
                    'univHeaderID' => $request->get('unvhd'),
                    'uInst1' => $request->get('uinst1'),
                    'univName' => $request->get('unvm'),
                    'deptName' => $request->get('udpnm'),
                    'subjName' => $request->get('usubnm'),
                    'offset' => $request->get('next'),
                ];
                break;
                // 大学一覧(条件検索)
            case 2:  // 検索ボタン
            case 12: // 大学一覧のもっと見る
                $conditions = [
                    'univHeaderID' => $request->get('unvhd'),
                    'uInst2' => $request->get('uinst2'),
                    'prefCode' => $request->get('uprf'),
                    'deptLineCode' => $request->get('udptl'),
                    'commonTest' => $request->get('ucmt'),
                    'offset' => $request->get('next'),
                ];
                if ($request->get('udevf') > 0) {
                    $conditions['deviFrom'] = $request->get('udevf');
                }
                if ($request->get('udevt') > 0) {
                    $conditions['deviTo'] = $request->get('udevt');
                }
                break;
                // 学部一覧 (名称検索)
            case 3:  // 大学一覧の大学明細クリック
            case 13: // 学部一覧のもっと見る
                $conditions = [
                    'univHeaderID' => $request->get('unvhd'),
                    'univCode' => $request->get('unvcd'),
                    'deptName' => $request->get('udpnm'),
                    'subjName' => $request->get('usubnm'),
                ];

                return $this->getDepList($mode, $conditions, $offset_end, $deptSubjDiv);
                // 学部一覧 (条件検索)
            case 23: // 大学一覧の大学明細クリック
            case 33: // 学部一覧のもっと見る
                $conditions = [
                    'univHeaderID' => $request->get('unvhd'),
                    'univCode' => $request->get('unvcd'),
                    'deptLineCode' => $request->get('udptl'),
                ];
                if ($request->get('udevf') > 0) {
                    $conditions['deviFrom'] = $request->get('udevf');
                }
                if ($request->get('udevt') > 0) {
                    $conditions['deviTo'] = $request->get('udevt');
                }

                return $this->getDepList($mode, $conditions, $offset_end, $deptSubjDiv);
                // 学科一覧（名称検索）
            case 4: // 学部一覧の学部明細クリック
            case 14: // 学科一覧のもっと見る
                $conditions = [
                    'univHeaderID' => $request->get('unvhd'),
                    'univCode' => $request->get('unvcd'),
                    'deptCode' => $request->get('udpcd'),
                    'subjName' => $request->get('usubnm'),
                ];

                return $this->getSubjList($mode, $conditions, $offset_end);
                // 学科一覧（条件検索）
            case 24: // 学部一覧の学部明細クリック
            case 34: // 学科一覧のもっと見る
                $conditions = [
                    'univHeaderID' => $request->get('unvhd'),
                    'univCode' => $request->get('unvcd'),
                    'deptCode' => $request->get('udpcd'),
                    'deptLineCode' => $request->get('udptl'),
                    'commonTest' => $request->get('ucmt'),
                ];
                if ($request->get('udevf') > 0) {
                    $conditions['deviFrom'] = $request->get('udevf');
                }
                if ($request->get('udevt') > 0) {
                    $conditions['deviTo'] = $request->get('udevt');
                }

                return $this->getSubjList($mode, $conditions, $offset_end);
        }
        // Debug用
        // Log::Warning('Conditions', $conditions);

        $univCodeCount = $this->univCodeService->getUnivCount($conditions);
        $univDeptCount = $this->univCodeService->getDeptCount($conditions);
        $univSubjCount = $this->univCodeService->getSubjCount($conditions);

        $univ_num = empty($univCodeCount) ? 0 : $univCodeCount->count;
        $dept_num = empty($univDeptCount) ? 0 : $univDeptCount->count;
        $subj_num = empty($univSubjCount) ? 0 : $univSubjCount->count;

        if ($univ_num == 0) {
            $header = $this->common->makeHeader('success', '大学コード情報が登録されていません。', $result ?? 0);
            $data_head = [];
            $data = [];

            return response()->json(compact('header', 'data_head', 'data'));
        }

        if ($univ_num == 1) {  // 大学の件数は1件の場合、自動的に学部の検索を行う
            $univCodeList = $this->univCodeService->getUnivCodeList($conditions);
            $conditions['univCode'] = $univCodeList[0]->university_cd;

            // 学部の件数は1件かつ学科判定の場合、自動的に学科の検索を行う
            if ($dept_num == 1 && $deptSubjDiv == 2) {
                $deptList = $this->univCodeService->getDeptList($conditions, $deptSubjDiv);

                $conditions['deptCode'] = $deptList[0]->department_cd;

                // 一覧表示区分：学科一覧
                $subjList = $this->univCodeService->getSubjList($conditions);

                if (count($subjList) > 0) {
                    $list_cat_div = 3;  // 表示区分=学科
                    $list_dsp_title = $subjList[0]->university_name.'　'.$subjList[0]->department_name.'　学科一覧';

                    $header = $this->common->makeHeader('success', '', $subj_num ?? 0);
                    $data_head = compact(
                        'mode',
                        'page_unit',
                        'list_cat_div',
                        'univ_num',
                        'dept_num',
                        'subj_num',
                        'list_dsp_title',
                    );

                    $result = [];
                    foreach ($subjList as $dtl) {
                        $row = [];
                        $row['dtl_name'] = $dtl->subject_name;
                        $row['dtl_code'] = $dtl->university_cd.'-'.$dtl->department_cd.'-'.$dtl->subject_cd;
                        $row['dtl_deviation_val'] = $dtl->deviation_value;
                        $row['university_cd'] = $dtl->university_cd;
                        $row['department_cd'] = $dtl->department_cd;
                        $row['subject_cd'] = $dtl->subject_cd;

                        $result[] = $row;
                    }
                    $data = $result;

                    return response()->json(compact('header', 'data_head', 'data'));
                }
            }

            // 一覧表示区分：学部一覧
            // $univCodeList = $this->univCodeService->getUnivCodeList($conditions);
            // $conditions['univCode'] = $univCodeList[0]->university_cd;

            $deptList = $this->univCodeService->getDeptList($conditions, $deptSubjDiv);

            $list_cat_div = 2;  // 表示区分=学部
            $list_dsp_title = $deptList[0]->university_name.'　学部一覧';

            $header = $this->common->makeHeader('success', '', $dept_num ?? 0);
            $data_head = compact(
                'mode',
                // 'offset_end',
                'page_unit',
                // 'next_page',
                'list_cat_div',
                'univ_num',
                'dept_num',
                'subj_num',
                'list_dsp_title',
            );
            $result = [];
            foreach ($deptList as $dtl) {
                $row = [];
                $row['dtl_name'] = $dtl->department_name;
                $row['dtl_code'] = $dtl->university_cd.'-'.$dtl->department_cd;
                $row['dtl_deviation_val'] = $dtl->deviation_value;
                $row['university_cd'] = $dtl->university_cd;
                $row['department_cd'] = $dtl->department_cd;

                $result[] = $row;
            }
            $data = $result;

            return response()->json(compact('header', 'data_head', 'data'));

        }

        $list_cat_div = 1;
        $list_dsp_title = '大学一覧';
        $univCodeList = $this->univCodeService->getUnivCodeList($conditions);

        // $result = $matchedCount;

        $header = $this->common->makeHeader('success', '', $univ_num ?? 0);

        $data_head = compact(
            'mode',
            // 'offset_end',
            'page_unit',
            // 'next_page',
            'list_cat_div',
            'univ_num',
            'dept_num',
            'subj_num',
            'list_dsp_title',
        );

        $result = [];
        foreach ($univCodeList as $dtl) {
            $row = [];
            $row['dtl_name'] = $dtl->university_name;
            $row['dtl_code'] = '';
            $row['dtl_deviation_val'] = $dtl->deviation_value;
            $row['university_cd'] = $dtl->university_cd;

            $result[] = $row;
        }

        $data = $result;

        return response()->json(compact('header', 'data_head', 'data'));

    }

    private function getDepList($mode, $conditions, $offset_end, $deptSubjDiv)
    {
        // debug用
        // Log::Warning('Conditions: ', $conditions);
        $page_unit = config('constants.UNVC_PER_PAGE');

        $univCodeCount = $this->univCodeService->getUnivCount($conditions);
        $univDeptCount = $this->univCodeService->getDeptCount($conditions);
        $univSubjCount = $this->univCodeService->getSubjCount($conditions);
        // Log::Warning('univCodeCount: '. $univCodeCount->count);
        // Log::Warning('univDeptCount: '. $univDeptCount->count);
        // Log::Warning('univSubjCount: '. $univSubjCount->count);
        $univ_num = empty($univCodeCount) ? 0 : $univCodeCount->count;
        $dept_num = empty($univDeptCount) ? 0 : $univDeptCount->count;
        $subj_num = empty($univSubjCount) ? 0 : $univSubjCount->count;

        if ($mode == 13 || $mode == 33) {
            $conditions['offset'] = $offset_end;
            $deptList = $this->univCodeService->getDeptList($conditions, $deptSubjDiv);
        } else {
            $deptList = $this->univCodeService->getDeptList($conditions, $deptSubjDiv);
        }
        // Log::Warning('dept: '. count($deptList));
        $list_cat_div = 2;
        $list_dsp_title = '0件';

        if (count($deptList) > 0) {
            $list_dsp_title = $deptList[0]->university_name.'　学部一覧';
        }

        $header = $this->common->makeHeader('success', '', $dept_num ?? 0);
        $data_head = compact(
            'mode',
            'page_unit',
            'list_cat_div',
            'univ_num',
            'dept_num',
            'subj_num',
            'list_dsp_title',
        );

        $result = [];
        foreach ($deptList as $dtl) {
            $row = [];
            $row['dtl_name'] = $dtl->department_name;
            $row['dtl_code'] = $dtl->university_cd.'-'.$dtl->department_cd;
            $row['dtl_deviation_val'] = $dtl->deviation_value;
            $row['university_cd'] = $dtl->university_cd;
            $row['university_cd'] = $dtl->university_cd;
            $row['department_cd'] = $dtl->department_cd;

            $result[] = $row;
        }

        $data = $result;

        return response()->json(compact('header', 'data_head', 'data'));
    }

    private function getSubjList($mode, $conditions, $offset_end)
    {
        // debug用
        // Log::Warning('Conditions: ', $conditions);
        $page_unit = config('constants.UNVC_PER_PAGE');

        $univCodeCount = $this->univCodeService->getUnivCount($conditions);
        $univDeptCount = $this->univCodeService->getDeptCount($conditions);
        $univSubjCount = $this->univCodeService->getSubjCount($conditions);

        $univ_num = empty($univCodeCount) ? 0 : $univCodeCount->count;
        $dept_num = empty($univDeptCount) ? 0 : $univDeptCount->count;
        $subj_num = empty($univSubjCount) ? 0 : $univSubjCount->count;

        if ($mode == 14 || $mode == 34) {
            $conditions['offset'] = $offset_end;
            $subjList = $this->univCodeService->getSubjList($conditions);
        } else {
            $subjList = $this->univCodeService->getSubjList($conditions);
        }

        $list_cat_div = 3;
        $list_dsp_title = '0件';
        if (count($subjList) > 0) {
            $list_dsp_title = $subjList[0]->university_name.'　'.$subjList[0]->department_name.'　学科一覧';
        }

        $header = $this->common->makeHeader('success', '', $subj_num ?? 0);
        $data_head = compact(
            'mode',
            'page_unit',
            'list_cat_div',
            'univ_num',
            'dept_num',
            'subj_num',
            'list_dsp_title',
        );

        $result = [];
        foreach ($subjList as $dtl) {
            $row = [];
            $row['dtl_name'] = $dtl->subject_name;
            $row['dtl_code'] = $dtl->university_cd.'-'.$dtl->department_cd.'-'.$dtl->subject_cd;
            $row['dtl_deviation_val'] = $dtl->deviation_value;
            $row['university_cd'] = $dtl->university_cd;
            $row['university_cd'] = $dtl->university_cd;
            $row['department_cd'] = $dtl->department_cd;
            $row['subject_cd'] = $dtl->subject_cd;

            $result[] = $row;
        }

        $data = $result;

        return response()->json(compact('header', 'data_head', 'data'));
    }
}
