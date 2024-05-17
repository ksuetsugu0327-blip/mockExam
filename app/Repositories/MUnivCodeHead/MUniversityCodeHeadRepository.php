<?php

namespace App\Repositories\MUnivCodeHead;

use App\Repositories\MUnivCodeHead\MUniversityCodeHeadRepositoryInterface as MUniversityCodeHeadRepositoryInterface;
use App\Models\MUniversityCodeHead as MUniversityCodeHead;
use Illuminate\Support\Facades\Log;


/**
 * 塾マスタ（回数毎）用リポジトリ
 *
 */
class MUniversityCodeHeadRepository implements MUniversityCodeHeadRepositoryInterface{
    
    /**
     * 塾マスタ（回数毎）モデル
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

    public function createCodeHead($codeHeadArray){
        try {
            $entry = MUniversityCodeHead::create($codeHeadArray);
            // If the create() method is successful, you can use the $entry object
            return $entry;
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
