<?php

namespace App\Repositories\MUnivCode;

use App\Repositories\MUnivCode\MUniversityCodeRepositoryInterface as MUniversityCodeRepositoryInterface;
use App\Models\MUniversityCode as MUniversityCode;


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
    public function registerArray($univCodeHeadID, array $univCodeAry){

        try{
            DB::beginTransaction();

            foreach ($univCodeAry as $univCode){
                // キーに合致するデータがあればUPDATE/なければINSERT
                $mUnivCode = $this->MUnivCode->updateOrCreate(
                        [
                            'university_cd'       => $mUnivCode['university_cd'],
                            'prefecture_cd'       => $mUnivCode['prefecture_cd'],
                            'install_div'         => $mUnivCode['install_div'],
                            'dept_line_cd'        => $mUnivCode['dept_line_cd'],
                            'university_name_hkn' => $mUnivCode['university_name_hkn'],
                            'university_name'     => $mUnivCode['university_name'],
                            'department_cd'       => $mUnivCode['department_cd'],
                            'department_seq'      => $mUnivCode['department_seq'],
                            'department_name'     => $mUnivCode['department_name'],
                            'subject_cd'          => $mUnivCode['subject_cd'],
                            'subject_seq'         => $mUnivCode['subject_seq'],
                            'subject_name'        => $mUnivCode['subject_name'],
                            'search_subject_name' => $mUnivCode['search_subject_name'],
                            'common_test_use'     => $mUnivCode['common_test_use'],
                            'deviation_value'     => $mUnivCode['deviation_value'],
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
                        'message'=>trans('message.SUCCESS_MUNIVCODE_REGISTER_DATA',['sch_code'=>$person['sch_code'],'psn_code'=>$person['psn_code']])
                        ,'id'=>$mUnivCode->id);

    }
}
