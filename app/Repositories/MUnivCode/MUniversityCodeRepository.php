<?php

namespace App\Repositories\MUnivCode;

use Illuminate\Support\Facades\DB;
use App\Models\MUniversityCode as MUniversityCode;
use App\Repositories\MUnivCode\MUniversityCodeRepositoryInterface as MUniversityCodeRepositoryInterface;



/**
 * 塾マスタ（回数毎）用リポジトリ
 *
 */
class MUniversityCodeRepository implements MUniversityCodeRepositoryInterface{
    
    /**
     * 塾マスタ（回数毎）モデル
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
    
}
