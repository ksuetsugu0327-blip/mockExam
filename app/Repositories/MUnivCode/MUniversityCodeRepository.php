<?php

namespace App\Repositories\MUnivCode;

use Illuminate\Support\Facades\DB;
use App\Models\MUniversityCode as MUniversityCode;
use App\Repositories\MUnivCode\MUniversityCodeRepositoryInterface as MUniversityCodeRepositoryInterface;



/**
 * 大学コードマスタ 用リポジトリ
 *
 */
class MUniversityCodeRepository implements MUniversityCodeRepositoryInterface{
    
    /**
     * 大学コードマスタ モデル
     * @var type model
     */
    private $MUnivCode;
    
    /**
     * コンストラクタ
     * @param MUniversityCode $mUnivCode  大学コードモデル
     */
    public function __construct(MUniversityCode $mUnivCode){
        $this->MUnivCode = $mUnivCode;
    }
    

    /**
     * 登録処理
     * @param array $univCodeAry 連想配列リスト
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray($codeHeadID, array $univCodeArr){

        try{
            DB::beginTransaction();

            foreach ($univCodeArr as $univCode){
                //dd($univCode);
                // キーに合致するデータがあればUPDATE/なければINSERT
                $mUnivCode = $this->MUnivCode->updateOrCreate(
                        [
                            'university_code_head_id' => $codeHeadID,
                            'university_cd'           => $univCode['university_cd'],
                            'prefecture_cd'           => $univCode['prefecture_cd'],
                            'install_div'             => $univCode['install_div'],
                            'dept_line_cd'            => $univCode['dept_line_cd'],
                            'university_name_hkn'     => $univCode['university_name_hkn'],
                            'university_name'         => $univCode['university_name'],
                            'department_cd'           => $univCode['department_cd'],
                            'department_seq'          => $univCode['department_seq'],
                            'department_name'         => $univCode['department_name'],
                            'subject_cd'              => $univCode['subject_cd'],
                            'subject_seq'             => $univCode['subject_seq'],
                            'subject_name'            => $univCode['subject_name'],
                            'search_subject_name'     => $univCode['search_subject_name'],
                            'common_test_use'         => $univCode['common_test_use'],
                            'deviation_value'         => $univCode['deviation_value'],
                        ]
                );

                // 登録
                $mUnivCode->save();
            }
            DB::commit();
        } catch (Exception $ex) {
            // システムエラー
            DB::rollBack();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }

        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'),
                        'message'=>trans('message.SUCCESS_MUNIVCODE_REGISTER_DATA',['code_head_id'=>$codeHeadID]));

    }

    public function deleteCodes($codeHeadID){
        $this->MUnivCode->where('university_code_head_id', $codeHeadID)
            ->chunk(100, function($codes){
                foreach($codes as $code){
                    $code->delete();
                }
        });    
    }

    public function getUnivCount($conditions){

        $subQuery =  $this->MUnivCode::select('university_cd')
                ->distinct()
                ->leftJoin('m_university_code_head as m_univ_head', 'm_univ_head.id', 'university_code_head_id')
                ->when(array_key_exists('univHeaderID', $conditions) && isset($conditions['univHeaderID']), function ($query) use ($conditions) {
                    return $query->where('university_code_head_id', $conditions['univHeaderID']);
                })
                ->when(array_key_exists('deptSubjDiv', $conditions) && isset($conditions['deptSubjDiv']), function ($query) use ($conditions) {
                    return $query->where('dept_subject_div', $conditions['deptSubjDiv']);
                })
                ->when(array_key_exists('uInst1', $conditions) && isset($conditions['uInst1']), function ($query) use ($conditions) {
                    return $query->where('install_div', $conditions['uInst1']);
                })
                ->when(array_key_exists('univName', $conditions) && isset($conditions['univName']), function ($query) use ($conditions) {
                    return $query->where('university_name', 'like', '%'.$conditions['univName'].'%');
                })
                ->when(array_key_exists('deptName', $conditions) && isset($conditions['deptName']), function ($query) use ($conditions) {
                    return $query->where('department_name', '%'.$conditions['deptName'].'%');
                })
                ->when(array_key_exists('subjName', $conditions) && isset($conditions['subjName']), function ($query) use ($conditions) {
                    return $query->where('subject_name', '%'.$conditions['subjName'].'%');
                })
                ->when(array_key_exists('uInst2', $conditions) && isset($conditions['uInst2']), function ($query) use ($conditions) {
                    return $query->where('install_div', $conditions['uInst2']);
                })
                ->when(array_key_exists('prefCode', $conditions) && isset($conditions['prefCode']), function ($query) use ($conditions) {
                    return $query->where('prefecture_cd', $conditions['prefCode']);
                })
                ->when(array_key_exists('deviFrom', $conditions) && isset($conditions['deviFrom']), function ($query) use ($conditions) {
                    return $query->where('deviation_value', '>=', floatval($conditions['deviFrom']));
                })
                ->when(array_key_exists('deviTo', $conditions) && isset($conditions['deviTo']), function ($query) use ($conditions) {
                    return $query->where('deviation_value', '<=', floatval($conditions['deviTo']));
                })
                ->when(array_key_exists('commonTest', $conditions) && isset($conditions['commonTest']), function ($query) use ($conditions) {
                    if($conditions['commonTest'] == 'on'){
                        return $query->where('common_test_use', '=', 1);
                    }
                    return $query->where('common_test_use', '=', 0);
                })
                ->orderBy('install_div')
                ->orderBy('prefecture_cd')
                ->orderBy('university_name_hkn');

        $count = DB::table(DB::raw("({$subQuery->toSql()}) as sub_t1"))
                    ->mergeBindings($subQuery->getQuery()) // you need to pass the bindings to avoid SQL injection
                    ->select(DB::raw('COUNT(*) as count'), 'university_cd')
                    ->groupBy('university_cd')
                    ->first();
// ->getBindings();
        return $count;
    }

    public function getDeptCount($conditions){

        $subQuery =  $this->MUnivCode::select('university_cd', 'department_cd')
                ->distinct()
                ->leftJoin('m_university_code_head as m_univ_head', 'm_univ_head.id', 'university_code_head_id')
                ->when(array_key_exists('univHeaderID', $conditions) && isset($conditions['univHeaderID']), function ($query) use ($conditions) {
                    return $query->where('university_code_head_id', $conditions['univHeaderID']);
                })
                ->when(array_key_exists('deptSubjDiv', $conditions) && isset($conditions['deptSubjDiv']), function ($query) use ($conditions) {
                    return $query->where('dept_subject_div', $conditions['deptSubjDiv']);
                })
                ->when(array_key_exists('uInst1', $conditions) && isset($conditions['uInst1']), function ($query) use ($conditions) {
                    return $query->where('install_div', $conditions['uInst1']);
                })
                ->when(array_key_exists('univName', $conditions) && isset($conditions['univName']), function ($query) use ($conditions) {
                    return $query->where('university_name', 'like', '%'.$conditions['univName'].'%');
                })
                ->when(array_key_exists('deptName', $conditions) && isset($conditions['deptName']), function ($query) use ($conditions) {
                    return $query->where('department_name', '%'.$conditions['deptName'].'%');
                })
                ->when(array_key_exists('subjName', $conditions) && isset($conditions['subjName']), function ($query) use ($conditions) {
                    return $query->where('subject_name', '%'.$conditions['subjName'].'%');
                })
                ->when(array_key_exists('uInst2', $conditions) && isset($conditions['uInst2']), function ($query) use ($conditions) {
                    return $query->where('install_div', $conditions['uInst2']);
                })
                ->when(array_key_exists('prefCode', $conditions) && isset($conditions['prefCode']), function ($query) use ($conditions) {
                    return $query->where('prefecture_cd', $conditions['prefCode']);
                })
                ->when(array_key_exists('deviFrom', $conditions) && isset($conditions['deviFrom']), function ($query) use ($conditions) {
                    return $query->where('deviation_value', '>=', floatval($conditions['deviFrom']));
                })
                ->when(array_key_exists('deviTo', $conditions) && isset($conditions['deviTo']), function ($query) use ($conditions) {
                    return $query->where('deviation_value', '<=', floatval($conditions['deviTo']));
                })
                ->when(array_key_exists('commonTest', $conditions) && isset($conditions['commonTest']), function ($query) use ($conditions) {
                    if($conditions['commonTest'] == 'on'){
                        return $query->where('common_test_use', '=', 1);
                    }
                    return $query->where('common_test_use', '=', 0);
                })
                ->orderBy('install_div')
                ->orderBy('prefecture_cd')
                ->orderBy('university_name_hkn');

        $count = DB::table(DB::raw("({$subQuery->toSql()}) as sub_t1"))
                    ->mergeBindings($subQuery->getQuery()) // you need to pass the bindings to avoid SQL injection
                    ->select(DB::raw('COUNT(*) as count'), 'university_cd', 'department_cd')
                    ->groupBy('university_cd', 'department_cd')
                    ->first();
// ->getBindings();
        return $count;
    }

    public function getSubjCount($conditions){

        $subQuery =  $this->MUnivCode::select('university_cd', 'department_cd', 'subject_cd')
                ->distinct()
                ->leftJoin('m_university_code_head as m_univ_head', 'm_univ_head.id', 'university_code_head_id')
                ->when(array_key_exists('univHeaderID', $conditions) && isset($conditions['univHeaderID']), function ($query) use ($conditions) {
                    return $query->where('university_code_head_id', $conditions['univHeaderID']);
                })
                ->when(array_key_exists('deptSubjDiv', $conditions) && isset($conditions['deptSubjDiv']), function ($query) use ($conditions) {
                    return $query->where('dept_subject_div', $conditions['deptSubjDiv']);
                })
                ->when(array_key_exists('uInst1', $conditions) && isset($conditions['uInst1']), function ($query) use ($conditions) {
                    return $query->where('install_div', $conditions['uInst1']);
                })
                ->when(array_key_exists('univName', $conditions) && isset($conditions['univName']), function ($query) use ($conditions) {
                    return $query->where('university_name', 'like', '%'.$conditions['univName'].'%');
                })
                ->when(array_key_exists('deptName', $conditions) && isset($conditions['deptName']), function ($query) use ($conditions) {
                    return $query->where('department_name', '%'.$conditions['deptName'].'%');
                })
                ->when(array_key_exists('subjName', $conditions) && isset($conditions['subjName']), function ($query) use ($conditions) {
                    return $query->where('subject_name', '%'.$conditions['subjName'].'%');
                })
                ->when(array_key_exists('uInst2', $conditions) && isset($conditions['uInst2']), function ($query) use ($conditions) {
                    return $query->where('install_div', $conditions['uInst2']);
                })
                ->when(array_key_exists('prefCode', $conditions) && isset($conditions['prefCode']), function ($query) use ($conditions) {
                    return $query->where('prefecture_cd', $conditions['prefCode']);
                })
                ->when(array_key_exists('deviFrom', $conditions) && isset($conditions['deviFrom']), function ($query) use ($conditions) {
                    return $query->where('deviation_value', '>=', floatval($conditions['deviFrom']));
                })
                ->when(array_key_exists('deviTo', $conditions) && isset($conditions['deviTo']), function ($query) use ($conditions) {
                    return $query->where('deviation_value', '<=', floatval($conditions['deviTo']));
                })
                ->when(array_key_exists('commonTest', $conditions) && isset($conditions['commonTest']), function ($query) use ($conditions) {
                    if($conditions['commonTest'] == 'on'){
                        return $query->where('common_test_use', '=', 1);
                    }
                    return $query->where('common_test_use', '=', 0);
                })
                ->orderBy('install_div')
                ->orderBy('prefecture_cd')
                ->orderBy('university_name_hkn');

        $count = DB::table(DB::raw("({$subQuery->toSql()}) as sub_t1"))
                    ->mergeBindings($subQuery->getQuery()) // you need to pass the bindings to avoid SQL injection
                    ->select(DB::raw('COUNT(*) as count'), 'university_cd', 'department_cd', 'subject_cd')
                    ->groupBy('university_cd', 'department_cd', 'subject_cd')
                    ->first();
// ->getBindings();
        return $count;
    }

    public function getUnivCodeList($conditions){

        return $this->MUnivCode::select('university_cd', 'university_name')
            ->distinct()
            ->leftJoin('m_university_code_head as m_univ_head', 'm_univ_head.id', 'university_code_head_id')
            ->when(array_key_exists('univHeaderID', $conditions) && isset($conditions['univHeaderID']), function ($query) use ($conditions) {
                return $query->where('university_code_head_id', $conditions['univHeaderID']);
            })
            ->when(array_key_exists('deptSubjDiv', $conditions) && isset($conditions['deptSubjDiv']), function ($query) use ($conditions) {
                return $query->where('dept_subject_div', $conditions['deptSubjDiv']);
            })
            ->when(array_key_exists('uInst1', $conditions) && isset($conditions['uInst1']), function ($query) use ($conditions) {
                return $query->where('install_div', $conditions['uInst1']);
            })
            ->when(array_key_exists('univName', $conditions) && isset($conditions['univName']), function ($query) use ($conditions) {
                return $query->where('university_name', 'like', '%'.$conditions['univName'].'%');
            })
            ->when(array_key_exists('deptName', $conditions) && isset($conditions['deptName']), function ($query) use ($conditions) {
                return $query->where('department_name', '%'.$conditions['deptName'].'%');
            })
            ->when(array_key_exists('subjName', $conditions) && isset($conditions['subjName']), function ($query) use ($conditions) {
                return $query->where('subject_name', '%'.$conditions['subjName'].'%');
            })
            ->when(array_key_exists('uInst2', $conditions) && isset($conditions['uInst2']), function ($query) use ($conditions) {
                return $query->where('install_div', '%'.$conditions['uInst2'].'%');
            })
            ->when(array_key_exists('prefCode', $conditions) && isset($conditions['prefCode']), function ($query) use ($conditions) {
                return $query->where('prefecture_cd', '%'.$conditions['prefCode'].'%');
            })
            ->when(array_key_exists('deviFrom', $conditions) && isset($conditions['deviFrom']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '>=', $conditions['deviFrom']);
            })
            ->when(array_key_exists('deviTo', $conditions) && isset($conditions['deviTo']), function ($query) use ($conditions) {
                return $query->where('deviation_value', '<=', $conditions['deviTo']);
            })
            ->when(array_key_exists('commonTest', $conditions) && isset($conditions['commonTest']), function ($query) use ($conditions) {
                return $query->where('common_test_use', '=', $conditions['commonTest']);
            })
            ->orderBy('install_div')
            ->orderBy('prefecture_cd')
            ->orderBy('university_name_hkn')
            ->limit(config('constants.UNVC_PER_PAGE'))
            ->when(array_key_exists('nextOffset', $conditions) && isset($conditions['nextOffset']), function ($query) use ($conditions) {
                return $query->offset($conditions['nextOffset']);
            })
            ->get();
    }

    public function getDeptList($univCodeHeadID, $univCode, $offset=null){
        return $this->MUnivCode::select(
                'university_cd',
                'department_cd',
                'university_name',
                'department_name',
                'deviation_value',
            )
            ->where('university_code_head_id', $univCodeHeadID)
            ->where('university_cd', '=', $univCode)
            ->orderBy('department_seq')
            ->limit(config('constants.UNVC_PER_PAGE'))
            ->when($offset, function ($query) use ($offset) {
                return $query->offset($offset);
            })
            ->get();
    }

    public function getSubjList($univCodeHeadID, $univCode, $deptCode, $offset=null){
        return $this->MUnivCode::select(
                'university_cd',
                'department_cd',
                'subject_cd',
                'university_name',
                'department_name',
                'subject_name',
                'deviation_value',
            )
            ->where('university_code_head_id', $univCodeHeadID)
            ->where('university_cd', '=', $univCode)
            ->where('department_cd', '=', $deptCode)
            ->orderBy('subject_seq')
            ->limit(config('constants.UNVC_PER_PAGE'))
            ->when($offset, function ($query) use ($offset) {
                return $query->offset($offset);
            })
            ->get();
    }
    
}
