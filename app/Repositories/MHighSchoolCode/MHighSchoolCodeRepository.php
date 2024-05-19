<?php

namespace App\Repositories\MHighSchoolCode;

use Illuminate\Support\Facades\DB;
use App\Models\MHighSchoolCode;
use App\Repositories\MHighSchoolCode\MHighSchoolCodeRepositoryInterface as HighCodeRepositoryInterface;

class MHighSchoolCodeRepository implements MHighSchoolCodeRepositoryInterface{
    
    /**
     * 塾マスタ（回数毎）モデル
     * @var type model
     */
    private $MHighCode;
    
    /**
     * コンストラクタ
     * @param MUniversityCode $mUnivCode  大学コードモデル
     */
    public function __construct(MHighSchoolCode $mHighSchoolCode){
        $this->MHighCode = $mHighSchoolCode;
    }
    

    /**
     * 登録処理
     * @param array $univCodeAry 連想配列リスト
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray(array $highCodeArr){

        try{
            DB::beginTransaction();

            foreach ($highCodeArr as $highCode){
                //dd($univCode);
                // キーに合致するデータがあればUPDATE/なければINSERT
                $mHighSchoolCode = $this->MHighCode->updateOrCreate(
                        [
                            'highschool_code'     => $highCode['highschool_code'], 
                            'highschool_name_hkn' => $highCode['highschool_name_hkn'],
                            'zip_num'             => $highCode['zip_num'],
                            'tel_num'             => $highCode['tel_num'],
                            'install_cd'          => $highCode['install_cd'],
                            'class_cd'            => $highCode['class_cd'],
                            'highschool_name'     => $highCode['highschool_name'],
                            'address'             => $highCode['address'],
                            'prefecture_cd'       => $highCode['prefecture_cd'],
                        ]
                );

                // 登録
                $mHighSchoolCode->save();
            }
            DB::commit();
        } catch (Exception $ex) {
            // システムエラー
            DB::rollBack();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }

        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'),
                        'message'=>trans('message.SUCCESS_MHIGHSCHOOL_REGISTER_DATA'));

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
