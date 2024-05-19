<?php

namespace App\Repositories\MUnivCodeHead;

use App\Repositories\MUnivCodeHead\MUniversityCodeHeadRepositoryInterface as MUniversityCodeHeadRepositoryInterface;
use App\Models\MUniversityCodeHead as MUniversityCodeHead;
use Illuminate\Support\Facades\Log;

class MUniversityCodeHeadRepository implements MUniversityCodeHeadRepositoryInterface{
    
    /**
     * 大学コードマスタ モデル
     * @var type model
     */
    private $MUnivCodeHead;
    
    /**
     * コンストラクタ
     * @param MUniversityCodeHead $mUnivCode  大学コードモデル
     */
    public function __construct(MUniversityCodeHead $mUnivCodeHead){
        $this->MUnivCodeHead = $mUnivCodeHead;
    }

    public function createOrUpdateCodeHead($codeHeadArray, $mode){
        try {
            if($mode === "update"){
                $codeHead = MUniversityCodeHead::find($codeHeadArray['id']);

                $codeHead->university_code_name_cd = $codeHeadArray['university_code_name_cd'];
                $codeHead->fiscal_year = $codeHeadArray['fiscal_year'];
                $codeHead->dept_subject_div = $codeHeadArray['dept_subject_div'];
                $codeHead->updated_num += 1;

                $codeHead->save();

                return $codeHead;
            }
            else{
                $entry = MUniversityCodeHead::Create($codeHeadArray);
                return $entry;
            }

        } catch (QueryException $e) {
            // Handle database related exceptions
            Log::Warning('Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            // Handle general exceptions
            Log::Warning('Error: ' . $e->getMessage());
        }
        return $entry;
    }

    public function getCodeHeader($dataNameCode){
       $mUnivCodeHeader = MUniversityCodeHead::
                                where('university_code_name_cd', $dataNameCode)->first();

       return $mUnivCodeHeader;
    }
}
