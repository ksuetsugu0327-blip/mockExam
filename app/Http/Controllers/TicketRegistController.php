<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\TicketRegistRequest;
use App\Services\MHighSchoolService;
use App\Services\MPersonService;
use App\Services\MPrefectureService;
use App\Services\TicketService;
use App\Utils\RouteUtil;
use Illuminate\Http\Request;

class TicketRegistController extends Controller
{
    // 志望校関連用サービス
    private $service;

    // 個人マスタサービス
    private $psnService;

    // 都道府県マスタサービス
    private $prefService;

    //  高校マスタサービス
    private $highSchoolService;

    // 塾コード
    private $schCode;

    // 生徒コード
    private $psnCode;

    /**
     * コンストラクタ
     *
     * @param  TicketService  $TicketService  志望校関連用サービス
     * @param  MPersonService  $mPersonService  個人マスタサービス
     * @param  MPrefectureService  $mPrefectureService  都道府県マスタサービス
     * @param  MHighSchoolService  $mHighSchoolService  高校マスタサービス
     */
    public function __construct(TicketService $TicketService,
        MPersonService $mPersonService,
        MPrefectureService $mPrefectureService,
        MHighSchoolService $mHighSchoolService)
    {
        // ログイン判定(管理者、個人）
        $this->middleware('auth:'.config('constants.GUARD_ADMIN').','.config('constants.GUARD_PERSON'));

        $this->service = $TicketService;
        $this->psnService = $mPersonService;
        $this->prefService = $mPrefectureService;
        $this->highSchoolService = $mHighSchoolService;
    }

    /**
     * 初期表示処理
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $sch_code, int $index)
    {
        if ($request->session()->has(config('constants.SESSION_KEY_TICKET_LIST'))) {
            $message = '';
            if ($request->session()->has('message')) {
                $message = $request->session()->get('message');
            }
            $data = $request->session()->get(config('constants.SESSION_KEY_TICKET_LIST'));

            $sch_code = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
            $psn_code = $request->session()->get(config('constants.SESSION_KEY_PSNCODE'));

            // テスト情報
            $conditions = $data['directory'][$index];

            $hsc_pref_code = null;
            $hsc_list = [];
            $univData = [];

            // バリデート失敗時の値取得
            if ($request->session()->has('validErr')) {
                $inputData = $request->session()->get('inputData');
                $hsc_code = $this->getIptData('hsc_code', $inputData);
                $hsc_name = $this->getIptData('hsc_name', $inputData);
                $hsc_pref_code = $this->getIptData('hsc_pref_code', $inputData);

                // 大学データ取得
                for ($i = 1; $i <= 6; $i++) {
                    $univData += [
                        'c_inst_class'.$i => $this->getIptData('inst_class'.$i, $inputData),
                        'c_pref_code'.$i => $this->getIptData('univ_pref'.$i, $inputData),
                        'c_univ_code'.$i => $this->getIptData('univ_code'.$i, $inputData),
                        'c_faculty_code'.$i => $this->getIptData('faculty_code'.$i, $inputData),
                        'c_department_code'.$i => $this->getIptData('department_code'.$i, $inputData),
                        'search_univ_name'.$i => $this->getIptData('search_univ_name'.$i, $inputData),
                    ];
                }

            } else {
                $hsc_code = $this->psnService->selectOneByKey($sch_code, $psn_code)->get(0)['hsc_code'];
                $hsc_name = null;
                // 高校コードから都道府県コードを取得
                if (! empty($hsc_code)) {
                    $hsc_pref_code = $this->highSchoolService->getPrefCode($hsc_code);
                    // 番号1～47以外は一律99:その他とする
                    if ($hsc_pref_code < 1 || $hsc_pref_code > 47) {
                        $hsc_pref_code = 99;
                    }
                }

                // 大学データ取得
                $univDataList = $this->service->TicketList(
                    $conditions
                    + [
                        'sch_code' => $sch_code,
                        'psn_code' => $psn_code,
                    ])->toArray();

                if (count($univDataList) > 0) {
                    $univData = $univDataList[0];
                    for ($i = 1; $i <= 6; $i++) {
                        $univData += [
                            'search_univ_name'.$i => null,
                        ];
                    }
                }
            }

            $hsc_list = $this->highSchoolService->selectList([
                'pref_code' => $hsc_pref_code,
                'hsc_name' => $hsc_name,
            ]);

            // 学科選択可否
            $dptEnable = 0;
            if (strpos($conditions['testName'], 'マーク') !== false) {
                if ($conditions['grade'] > 1) {
                    $dptEnable = 1;
                }
            } else {
                if ($conditions['grade'] > 2) {
                    $dptEnable = 1;
                }
            }

            // 判定コード表の表示判別
            if ($dptEnable == 1) {
                $codePdfUrl = config('constants.CODE_PDF_DEPARTMENT_PATH');
                $codePdfName = config('constants.CODE_PDF_DEPARTMENT_NAME');
            } else {
                $codePdfUrl = config('constants.CODE_PDF_FACULTY_PATH');
                $codePdfName = config('constants.CODE_PDF_FACULTY_NAME');
            }

            // 志望大学情報
            $univParams = [];
            for ($i = 1; $i <= 6; $i++) {
                $univParams += [
                    'inst_class'.$i => null,
                    'univ_pref'.$i => null,
                    'search_univ_name'.$i => null,
                    'univ_code'.$i => null,
                    'univ_list'.$i => [],
                    'faculty_code'.$i => null,
                    'faculty_list'.$i => [],
                    'department_code'.$i => null,
                    'department_list'.$i => [],
                ];

                if (! empty($univData)) {
                    // 番号1～47以外は一律99:その他とする
                    if ($univData['c_pref_code'.$i] == null || $univData['c_pref_code'.$i] == '') {
                        $univData['c_pref_code'.$i] = null;
                    } elseif ($univData['c_pref_code'.$i] < 1 || $univData['c_pref_code'.$i] > 47) {
                        $univData['c_pref_code'.$i] = 99;
                    }

                    $univParams['inst_class'.$i] = $univData['c_inst_class'.$i];
                    $univParams['univ_pref'.$i] = $univData['c_pref_code'.$i];
                    $univParams['search_univ_name'.$i] = $univData['search_univ_name'.$i];
                    $univParams['univ_code'.$i] = $univData['c_univ_code'.$i];
                    $univParams['univ_list'.$i] = $this->service->univList([
                        'inst_class' => $univData['c_inst_class'.$i],
                        'pref_code' => $univData['c_pref_code'.$i],
                        'univ_name' => $univData['search_univ_name'.$i],
                    ]);
                    $univParams['faculty_code'.$i] = $univData['c_faculty_code'.$i];
                    $univParams['faculty_list'.$i] = $this->service->facultyList([
                        'univ_code' => $univData['c_univ_code'.$i],
                        'enable_flag' => $dptEnable,
                    ]);
                    $univParams['department_code'.$i] = $univData['c_department_code'.$i];
                    $univParams['department_list'.$i] = $this->service->departmentList([
                        'univ_code' => $univData['c_univ_code'.$i],
                        'fcl_code' => $univData['c_faculty_code'.$i],
                    ]);
                }
            }

            // パラメータ
            $params = [
                'key' => $index,
                'year' => $conditions['year'],
                'testName' => $conditions['testName'],
                'cnttest' => $conditions['cnttest'],
                'grade' => $conditions['grade'],
                'exam_number' => $this->service->getExamNumber($conditions, $this->psnService->selectOneByKey($sch_code, $psn_code)->get(0)['id']),
                'pref_list' => $this->prefService->selectList()
                    ->pluck('name', 'pref_code')
                    ->toArray(),                        // 都道府県リスト
                'hsc_pref_code' => $hsc_pref_code,                              // 高校検索用都道府県コード
                'hsc_name' => $hsc_name,                                        // 検索用高校名
                'hsc_code' => $hsc_code,                                        // 高校コード
                'hsc_list' => $hsc_list,                                        // 高校リスト
                'code_pdf_url' => $codePdfUrl,                                  // コード番号表URL
                'code_pdf_name' => $codePdfName,                                // コード番号表名
                'dptEnable' => $dptEnable,                                      // 学科選択可否
            ];

            return view('ticketRegist')
                ->with($univParams)
                ->with($params)
                ->with('message', $message);

        } else {
            return redirect()
                ->route(RouteUtil::getPrefix().'.ticketList.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))]);
        }
    }

    /**
     * キーがない、値が空文字列の場合はnullを返す
     *
     * @return 文字列 / null
     */
    private function getIptData($key, $arr): 文字列
    {
        if (! array_key_exists($key, $arr)) {
            return null;
        }

        return ($arr[$key] == '') ? null : $arr[$key];
    }

    /**
     * 高校リスト取得処理
     *
     * @return JSON 高校リスト
     */
    public function hsclist(Request $request): JsonResponse
    {
        $conditions = [
            'pref_code' => $request->get('pref_code'),                      // 都道府県コード
            'hsc_name' => $request->get('hsc_name'),                        // 高校名
        ];
        $list = $this->highSchoolService->selectList($conditions);
        $data = [];
        $rows = [];
        // 高校コードが空でない場合はパラメータで高校コードを返す
        $params = [
            'hsc_code' => is_null($request->get('set_hsc')) ? '' : $request->get('set_hsc'),
        ];
        foreach ($list as $key => $val) {
            $rows[] = [
                'hsc_code' => $key,
                'hsc_name' => $val,
            ];
        }
        $data = [
            'list' => $rows,
            'params' => $params,
        ];

        return response()->json($data);
    }

    /**
     * 大学リスト取得処理
     *
     * @return JSON 大学リスト
     */
    public function univlist(Request $request): JsonResponse
    {
        $univName = $request->get('univ_name');
        $conditions = [
            'inst_class' => $request->get('inst_class'),                    // 設置区分
            'pref_code' => $request->get('pref_code'),                      // 都道府県コード
            'univ_name' => $univName,                                       // 検索用大学名
        ];
        $list = $this->service->univList($conditions);
        $data = [];
        $rows = [];
        // 大学コードが空でない場合はパラメータで大学コードを返す
        $params = [
            'univ_code' => is_null($request->get('set_univ')) ? '' : $request->get('set_univ'),
        ];
        foreach ($list as $key => $val) {
            $rows[] = [
                'univ_code' => $key,
                'univ_name' => $val,
            ];
        }
        $data = [
            'list' => $rows,
            'params' => $params,
        ];

        return response()->json($data);
    }

    /**
     * 学部リスト取得処理
     *
     * @return JSON 学部リスト
     */
    public function fcllist(Request $request): JsonResponse
    {
        $conditions = [
            'univ_code' => $request->get('univ_code'),                      // 大学コード
            'enable_flag' => $request->get('enable_flag'),                  // 学科選択可否
        ];
        $list = $this->service->facultyList($conditions);
        $data = [];
        $rows = [];
        // 学部コードが空でない場合はパラメータで学部コードを返す
        $params = [
            'faculty_code' => is_null($request->get('set_fcl')) ? '' : $request->get('set_fcl'),
        ];
        foreach ($list as $key => $val) {
            $rows[] = [
                'fcl_code' => $key,
                'fcl_name' => $val,
            ];
        }
        $data = [
            'list' => $rows,
            'params' => $params,
        ];

        return response()->json($data);
    }

    /**
     * 学科リスト取得処理
     *
     * @return JSON 学科リスト
     */
    public function dptlist(Request $request): JsonResponse
    {
        $conditions = [
            'univ_code' => $request->get('univ_code'),                      // 大学コード
            'fcl_code' => $request->get('fcl_code'),                        // 学部コード
        ];
        $list = $this->service->departmentList($conditions);
        $data = [];
        $rows = [];
        // 学科コードが空でない場合はパラメータで学科コードを返す
        $params = [
            'department_code' => is_null($request->get('set_dpt')) ? '' : $request->get('set_dpt'),
        ];
        foreach ($list as $key => $val) {
            $rows[] = [
                'dpt_code' => $key,
                'dpt_name' => $val,
            ];
        }
        $data = [
            'list' => $rows,
            'params' => $params,
        ];

        return response()->json($data);
    }

    /**
     * 高校および志望校登録処理
     *
     * @param  Request  $request  登録用リクエスト
     */
    public function store(TicketRegistRequest $request, int $sch_code, int $index): RedirectResponse
    {
        // バリデートエラー
        $validator = $request->getValidator();
        if ($validator->fails()) {
            $data = $request->all();

            return redirect()
                ->back()
                ->with('inputData', $data)
                ->withErrors($validator)
                ->with('validErr', true);
        }

        if ($request->session()->has(config('constants.SESSION_KEY_TICKET_LIST'))) {
            $data = $request->session()->get(config('constants.SESSION_KEY_TICKET_LIST'));
            $sch_code = $request->session()->get(config('constants.SESSION_KEY_SCHCODE'));
            $psn_code = $request->session()->get(config('constants.SESSION_KEY_PSNCODE'));

            // テスト情報
            $conditions = $data['directory'][$index];

            // 画面情報を連想配列に設定
            $setData = [
                'id' => $this->psnService->selectOneByKey($sch_code, $psn_code)->get(0)['id'],
                'sch_code' => $sch_code,
                'psn_code' => $psn_code,
                'hsc_code' => $request->get('hsc_code'),
                'year' => $conditions['year'],
                'cnttest' => $conditions['cnttest'],
                'grade' => $conditions['grade'],
                'testname' => $conditions['year'].'年度_'.$conditions['testName'].'_高'.$conditions['grade'],
            ];

            for ($i = 1; $i <= 6; $i++) {
                $setData += [
                    'c_univ_code'.$i => $request->get('univ_code'.$i),
                    'c_faculty_code'.$i => $request->get('faculty_code'.$i),
                    'c_department_code'.$i => $request->get('department_code'.$i),
                ];
            }

            // 登録処理
            $result = $this->service->updateChoiceOfSchool($setData);
            // 結果判定
            if ($result['resultCode'] == -1) {
                // システムエラーの場合、abort
                abort(500);
            }

            // 登録画面へリダイレクト
            return redirect()
                ->back()
                ->with('message', $result['message']);
        } else {
            return redirect()
                ->route(RouteUtil::getPrefix().'.ticketList.index', ['schCode' => session(config('constants.SESSION_KEY_SCHCODE'))]);
        }
    }
}
