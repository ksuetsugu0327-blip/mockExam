<?php

namespace App\Repositories\MUnivCode;

use App\Models\MUniversityCode;
use Illuminate\Support\Facades\DB;

/**
 * 大学コードマスタ 用リポジトリ
 */
class MUniversityCodeRepository implements MUniversityCodeRepositoryInterface
{
    /**
     * 大学コードマスタ モデル
     *
     * @var type model
     */
    private $MUnivCode;

    /**
     * コンストラクタ
     *
     * @param  MUniversityCode  $mUnivCode  大学コードモデル
     */
    public function __construct(MUniversityCode $mUnivCode)
    {
        $this->MUnivCode = $mUnivCode;
    }

    /**
     * 登録処理
     *
     * @param  array  $univCodeAry  連想配列リスト
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray($codeHeadID, array $univCode)
    {

        try {

            // キーに合致するデータがあればUPDATE/なければINSERT
            $mUnivCode = $this->MUnivCode->updateOrCreate(
                [
                    'university_code_head_id' => $codeHeadID,
                    'university_cd' => $univCode['university_cd'],
                    'prefecture_cd' => $univCode['prefecture_cd'],
                    'install_div' => $univCode['install_div'],
                    'dept_line_cd1' => $univCode['dept_line_cd1'],
                    'dept_line_cd2' => $univCode['dept_line_cd2'],
                    'dept_line_cd3' => $univCode['dept_line_cd3'],
                    'dept_line_cd4' => $univCode['dept_line_cd4'],
                    'dept_line_cd5' => $univCode['dept_line_cd5'],
                    'dept_line_cd6' => $univCode['dept_line_cd6'],
                    'dept_line_cd7' => $univCode['dept_line_cd7'],
                    'university_name_hkn' => $univCode['university_name_hkn'],
                    'university_name' => $univCode['university_name'],
                    'university_name_new' => $univCode['university_name_new'],
                    'department_cd' => $univCode['department_cd'],
                    'department_seq' => $univCode['department_seq'],
                    'department_name' => $univCode['department_name'],
                    'subject_cd' => $univCode['subject_cd'],
                    'subject_seq' => $univCode['subject_seq'],
                    'subject_name' => $univCode['subject_name'],
                    'search_subject_name' => $univCode['search_subject_name'],
                    'common_test_use' => $univCode['common_test_use'],
                    'deviation_value' => $univCode['deviation_value'],
                ]
            );

        } catch (Exception $ex) {
            // システムエラー
            report($ex);

            return ['resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex];
        }

        return ['resultCode' => config('constants.RESULT_CODE_SUCCESS'),
            'message' => trans('message.SUCCESS_MUNIVCODE_REGISTER_DATA', ['code_head_id' => $codeHeadID])];

    }

    public function deleteCodes($codeHeadID)
    {
        $this->MUnivCode->where('university_code_head_id', $codeHeadID)->delete();
    }

    public function getUnivCount($conditions)
    {

        $subQuery = $this->MUnivCode::select('university_cd')
            ->distinct()
            ->leftJoin('m_university_code_head as m_univ_head', 'm_univ_head.id', 'university_code_head_id')
            ->when(array_key_exists('univHeaderID', $conditions) && isset($conditions['univHeaderID']), function ($query) use ($conditions) {
                return $query->where('university_code_head_id', $conditions['univHeaderID']);
            })
            ->when(array_key_exists('uInst1', $conditions) && isset($conditions['uInst1']), function ($query) use ($conditions) {
                return $query->where('install_div', $conditions['uInst1']);
            })
            ->when(array_key_exists('univName', $conditions) && isset($conditions['univName']), function ($query) use ($conditions) {
                return $query->where(function ($q) use ($conditions) {
                    $q->where(DB::raw('university_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['univName'].'%')
                        ->orWhere(DB::raw('university_name_new COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['univName'].'%');
                });
            })
            ->when(array_key_exists('deptName', $conditions) && isset($conditions['deptName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('department_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['deptName'].'%');
            })
            ->when(array_key_exists('subjName', $conditions) && isset($conditions['subjName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('subject_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['subjName'].'%');
            })
            ->when(array_key_exists('uInst2', $conditions) && isset($conditions['uInst2']), function ($query) use ($conditions) {
                return $query->where('install_div', $conditions['uInst2']);
            })
            ->when(array_key_exists('prefCode', $conditions) && isset($conditions['prefCode']), function ($query) use ($conditions) {
                return $query->where('prefecture_cd', $conditions['prefCode']);
            })
            ->when(array_key_exists('deptLineCode', $conditions) && isset($conditions['deptLineCode']), function ($query) use ($conditions) {
                return $query->where(function ($query) use ($conditions) {
                    $query->where('dept_line_cd1', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd2', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd3', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd4', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd5', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd6', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd7', $conditions['deptLineCode']);
                });
            })
            ->when(array_key_exists('deviFrom', $conditions) && isset($conditions['deviFrom']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '>=', floatval($conditions['deviFrom']));
            })
            ->when(array_key_exists('deviTo', $conditions) && isset($conditions['deviTo']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '<=', floatval($conditions['deviTo']));
            })
            ->when(array_key_exists('commonTest', $conditions) && isset($conditions['commonTest']), function ($query) use ($conditions) {
                if ($conditions['commonTest'] == 'on') {
                    return $query->where('common_test_use', '=', 1);
                }
            });

        // \Log::info('SQL: ' . $subQuery->toSql());
        // \Log::info('Bindings: ' . json_encode($subQuery->getBindings()));

        $count = DB::table(DB::raw("({$subQuery->toSql()}) as sub_t1"))
            ->mergeBindings($subQuery->getQuery()) // you need to pass the bindings to avoid SQL injection
            ->select(DB::raw('COUNT(*) as count'))
            ->first();

        return $count;
    }

    public function getDeptCount($conditions)
    {

        $subQuery = $this->MUnivCode::select('university_cd', 'department_cd')
            ->distinct()
            ->leftJoin('m_university_code_head as m_univ_head', 'm_univ_head.id', 'university_code_head_id')
            ->when(array_key_exists('univHeaderID', $conditions) && isset($conditions['univHeaderID']), function ($query) use ($conditions) {
                return $query->where('university_code_head_id', $conditions['univHeaderID']);
            })
            ->when(array_key_exists('uInst1', $conditions) && isset($conditions['uInst1']), function ($query) use ($conditions) {
                return $query->where('install_div', $conditions['uInst1']);
            })
            ->when(array_key_exists('univName', $conditions) && isset($conditions['univName']), function ($query) use ($conditions) {
                return $query->where(function ($q) use ($conditions) {
                    $q->where(DB::Raw('university_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['univName'].'%')
                        ->orWhere(DB::Raw('university_name_new COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['univName'].'%');
                });
            })
            ->when(array_key_exists('deptName', $conditions) && isset($conditions['deptName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('department_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['deptName'].'%');
            })
            ->when(array_key_exists('subjName', $conditions) && isset($conditions['subjName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('subject_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['subjName'].'%');
            })
            ->when(array_key_exists('uInst2', $conditions) && isset($conditions['uInst2']), function ($query) use ($conditions) {
                return $query->where('install_div', $conditions['uInst2']);
            })
            ->when(array_key_exists('prefCode', $conditions) && isset($conditions['prefCode']), function ($query) use ($conditions) {
                return $query->where('prefecture_cd', $conditions['prefCode']);
            })
            ->when(array_key_exists('deptLineCode', $conditions) && isset($conditions['deptLineCode']), function ($query) use ($conditions) {
                return $query->where(function ($query) use ($conditions) {
                    $query->where('dept_line_cd1', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd2', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd3', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd4', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd5', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd6', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd7', $conditions['deptLineCode']);
                });
            })
            ->when(array_key_exists('deviFrom', $conditions) && isset($conditions['deviFrom']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '>=', floatval($conditions['deviFrom']));
            })
            ->when(array_key_exists('deviTo', $conditions) && isset($conditions['deviTo']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '<=', floatval($conditions['deviTo']));
            })
            ->when(array_key_exists('commonTest', $conditions) && isset($conditions['commonTest']), function ($query) use ($conditions) {
                if ($conditions['commonTest'] == 'on') {
                    return $query->where('common_test_use', '=', 1);
                }
            })
            ->when(array_key_exists('univCode', $conditions) && isset($conditions['univCode']), function ($query) use ($conditions) {
                return $query->where('university_cd', '=', $conditions['univCode']);
            });

        $count = DB::table(DB::raw("({$subQuery->toSql()}) as sub_t1"))
            ->mergeBindings($subQuery->getQuery()) // you need to pass the bindings to avoid SQL injection
            ->select(DB::raw('COUNT(*) as count'))
            ->first();

        return $count;
    }

    public function getSubjCount($conditions)
    {

        $subQuery = $this->MUnivCode::select('university_cd', 'department_cd', 'subject_cd')
            ->distinct()
            ->leftJoin('m_university_code_head as m_univ_head', 'm_univ_head.id', 'university_code_head_id')
            ->when(array_key_exists('univHeaderID', $conditions) && isset($conditions['univHeaderID']), function ($query) use ($conditions) {
                return $query->where('university_code_head_id', $conditions['univHeaderID']);
            })
            ->when(array_key_exists('uInst1', $conditions) && isset($conditions['uInst1']), function ($query) use ($conditions) {
                return $query->where('install_div', $conditions['uInst1']);
            })
            ->when(array_key_exists('univName', $conditions) && isset($conditions['univName']), function ($query) use ($conditions) {
                return $query->where(function ($q) use ($conditions) {
                    $q->where(DB::Raw('university_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['univName'].'%')
                        ->orWhere(DB::Raw('university_name_new COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['univName'].'%');
                });
            })
            ->when(array_key_exists('deptName', $conditions) && isset($conditions['deptName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('department_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['deptName'].'%');
            })
            ->when(array_key_exists('subjName', $conditions) && isset($conditions['subjName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('subject_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['subjName'].'%');
            })
            ->when(array_key_exists('uInst2', $conditions) && isset($conditions['uInst2']), function ($query) use ($conditions) {
                return $query->where('install_div', $conditions['uInst2']);
            })
            ->when(array_key_exists('prefCode', $conditions) && isset($conditions['prefCode']), function ($query) use ($conditions) {
                return $query->where('prefecture_cd', $conditions['prefCode']);
            })
            ->when(array_key_exists('deptLineCode', $conditions) && isset($conditions['deptLineCode']), function ($query) use ($conditions) {
                return $query->where(function ($query) use ($conditions) {
                    $query->where('dept_line_cd1', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd2', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd3', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd4', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd5', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd6', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd7', $conditions['deptLineCode']);
                });
            })
            ->when(array_key_exists('deviFrom', $conditions) && isset($conditions['deviFrom']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '>=', floatval($conditions['deviFrom']));
            })
            ->when(array_key_exists('deviTo', $conditions) && isset($conditions['deviTo']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '<=', floatval($conditions['deviTo']));
            })
            ->when(array_key_exists('commonTest', $conditions) && isset($conditions['commonTest']), function ($query) use ($conditions) {
                if ($conditions['commonTest'] == 'on') {
                    return $query->where('common_test_use', '=', 1);
                }
            })
            ->when(array_key_exists('univCode', $conditions) && isset($conditions['univCode']), function ($query) use ($conditions) {
                return $query->where('university_cd', '=', $conditions['univCode']);
            })
            ->when(array_key_exists('deptCode', $conditions) && isset($conditions['deptCode']), function ($query) use ($conditions) {
                return $query->where('department_cd', '=', $conditions['deptCode']);
            });

        $count = DB::table(DB::raw("({$subQuery->toSql()}) as sub_t1"))
            ->mergeBindings($subQuery->getQuery()) // you need to pass the bindings to avoid SQL injection
            ->select(DB::raw('COUNT(*) as count'))
            ->first();

        return $count;
    }

    public function getUnivCodeList($conditions)
    {

        $query = $this->MUnivCode::select('university_cd', 'university_name')
            ->leftJoin('m_university_code_head as m_univ_head', 'm_univ_head.id', 'university_code_head_id')
            ->when(array_key_exists('univHeaderID', $conditions) && isset($conditions['univHeaderID']), function ($query) use ($conditions) {
                return $query->where('university_code_head_id', $conditions['univHeaderID']);
            })
            ->when(array_key_exists('uInst1', $conditions) && isset($conditions['uInst1']), function ($query) use ($conditions) {
                return $query->where('install_div', $conditions['uInst1']);
            })
            ->when(array_key_exists('univName', $conditions) && isset($conditions['univName']), function ($query) use ($conditions) {
                return $query->where(function ($q) use ($conditions) {
                    $q->where(DB::raw('university_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['univName'].'%')
                        ->orWhere(DB::raw('university_name_new COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['univName'].'%');
                });
            })
            ->when(array_key_exists('deptName', $conditions) && isset($conditions['deptName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('department_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['deptName'].'%');
            })
            ->when(array_key_exists('subjName', $conditions) && isset($conditions['subjName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('subject_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['subjName'].'%');
            })
            ->when(array_key_exists('uInst2', $conditions) && isset($conditions['uInst2']), function ($query) use ($conditions) {
                return $query->where('install_div', $conditions['uInst2']);
            })
            ->when(array_key_exists('prefCode', $conditions) && isset($conditions['prefCode']), function ($query) use ($conditions) {
                return $query->where('prefecture_cd', $conditions['prefCode']);
            })
            ->when(array_key_exists('deptLineCode', $conditions) && isset($conditions['deptLineCode']), function ($query) use ($conditions) {
                return $query->where(function ($query) use ($conditions) {
                    $query->where('dept_line_cd1', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd2', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd3', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd4', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd5', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd6', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd7', $conditions['deptLineCode']);
                });
            })
            ->when(array_key_exists('deviFrom', $conditions) && isset($conditions['deviFrom']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '>=', $conditions['deviFrom']);
            })
            ->when(array_key_exists('deviTo', $conditions) && isset($conditions['deviTo']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '<=', $conditions['deviTo']);
            })
            ->when(array_key_exists('commonTest', $conditions) && isset($conditions['commonTest']), function ($query) use ($conditions) {
                if ($conditions['commonTest'] == 'on') {
                    return $query->where('common_test_use', '=', 1);
                }
            })
            ->groupBy('university_cd')
            ->groupBy('university_name')
            ->groupBy('install_div')
            ->groupBy('prefecture_cd')
            ->groupBy('university_name_hkn')
            ->orderBy('prefecture_cd')
            ->orderBy('install_div')
            ->orderBy('university_name_hkn')
            ->limit(config('constants.UNVC_PER_PAGE'))
            ->when(array_key_exists('offset', $conditions) && isset($conditions['offset']), function ($query) use ($conditions) {
                return $query->offset($conditions['offset']);
            });

        // \Log::info('SQL: ' . $query->toSql());
        // \Log::info('Bindings: ' . json_encode($query->getBindings()));

        return $query->get();
    }

    public function getDeptList($conditions, $deptSubjDiv)
    {
        return $this->MUnivCode::select(
            'university_cd',
            'department_cd',
            'university_name',
            'department_name',
        )
            ->when($deptSubjDiv == 1, function ($query) {
                return $query->addSelect('deviation_value');
            })
            ->leftJoin('m_university_code_head as m_univ_head', 'm_univ_head.id', 'university_code_head_id')
            ->when(array_key_exists('univHeaderID', $conditions) && isset($conditions['univHeaderID']), function ($query) use ($conditions) {
                return $query->where('university_code_head_id', $conditions['univHeaderID']);
            })
            ->when(array_key_exists('univCode', $conditions) && isset($conditions['univCode']), function ($query) use ($conditions) {
                return $query->where('university_cd', '=', $conditions['univCode']);
            })
            ->when(array_key_exists('deptName', $conditions) && isset($conditions['deptName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('department_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['deptName'].'%');
            })
            ->when(array_key_exists('subjName', $conditions) && isset($conditions['subjName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('subject_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['subjName'].'%');
            })
            ->when(array_key_exists('deptLineCode', $conditions) && isset($conditions['deptLineCode']), function ($query) use ($conditions) {
                return $query->where(function ($query) use ($conditions) {
                    $query->where('dept_line_cd1', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd2', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd3', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd4', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd5', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd6', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd7', $conditions['deptLineCode']);
                });
            })
            ->when(array_key_exists('deviFrom', $conditions) && isset($conditions['deviFrom']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '>=', floatval($conditions['deviFrom']));
            })
            ->when(array_key_exists('deviTo', $conditions) && isset($conditions['deviTo']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '<=', floatval($conditions['deviTo']));
            })
            ->when(array_key_exists('commonTest', $conditions) && isset($conditions['commonTest']), function ($query) use ($conditions) {
                if ($conditions['commonTest'] == 'on') {
                    return $query->where('common_test_use', '=', 1);
                }
            })
            ->groupBy('university_cd')
            ->groupBy('university_name')
            ->groupBy('department_cd')
            ->groupBy('department_name')
            ->when($deptSubjDiv == 1, function ($query) {
                return $query->groupBy('deviation_value');
            })
            ->groupBy('department_seq')
            ->orderBy('university_cd')
            ->orderBy('department_seq')
            ->orderBy('department_cd')
            ->limit(config('constants.UNVC_PER_PAGE'))
            ->when(array_key_exists('offset', $conditions), function ($query) use ($conditions) {
                return $query->offset($conditions['offset']);
            })
            ->get();
    }

    public function getSubjList($conditions)
    {
        return $this->MUnivCode::select(
            'university_cd',
            'department_cd',
            'subject_cd',
            'university_name',
            'department_name',
            'subject_name',
            'deviation_value',
        )
            ->leftJoin('m_university_code_head as m_univ_head', 'm_univ_head.id', 'university_code_head_id')
            ->when(array_key_exists('univHeaderID', $conditions) && isset($conditions['univHeaderID']), function ($query) use ($conditions) {
                return $query->where('university_code_head_id', $conditions['univHeaderID']);
            })
            ->when(array_key_exists('univCode', $conditions) && isset($conditions['univCode']), function ($query) use ($conditions) {
                return $query->where('university_cd', '=', $conditions['univCode']);
            })
            ->when(array_key_exists('deptCode', $conditions) && isset($conditions['deptCode']), function ($query) use ($conditions) {
                return $query->where('department_cd', '=', $conditions['deptCode']);
            })
            ->when(array_key_exists('subjName', $conditions) && isset($conditions['subjName']), function ($query) use ($conditions) {
                return $query->where(DB::Raw('subject_name COLLATE utf8mb4_general_ci'), 'like', '%'.$conditions['subjName'].'%');
            })
            ->when(array_key_exists('deptLineCode', $conditions) && isset($conditions['deptLineCode']), function ($query) use ($conditions) {
                return $query->where(function ($query) use ($conditions) {
                    $query->where('dept_line_cd1', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd2', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd3', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd4', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd5', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd6', $conditions['deptLineCode'])
                        ->orWhere('dept_line_cd7', $conditions['deptLineCode']);
                });
            })
            ->when(array_key_exists('deviFrom', $conditions) && isset($conditions['deviFrom']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '>=', floatval($conditions['deviFrom']));
            })
            ->when(array_key_exists('deviTo', $conditions) && isset($conditions['deviTo']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '<=', floatval($conditions['deviTo']));
            })
            ->when(array_key_exists('commonTest', $conditions) && isset($conditions['commonTest']), function ($query) use ($conditions) {
                if ($conditions['commonTest'] == 'on') {
                    return $query->where('common_test_use', '=', 1);
                }
            })
            ->orderBy('university_cd')
            ->orderBy('department_seq')
            ->orderBy('department_cd')
            ->orderBy('subject_seq')
            ->orderBy('subject_cd')
            ->limit(config('constants.UNVC_PER_PAGE'))
            ->when(array_key_exists('offset', $conditions), function ($query) use ($conditions) {
                return $query->offset($conditions['offset']);
            })
            ->get();
    }
}
