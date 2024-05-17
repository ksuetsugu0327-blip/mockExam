<?php

namespace App\Repositories\MUniversityCodeHeadRepository;

use App\Repositories\MUnivCode\MUniversityCodeHeadRepositoryInterface as MUniversityCodeHeadRepositoryInterface;
use App\Models\MUniversityCodeHead as MUniversityCodeHead;


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

    public function getUnivCodeHeadID($univCodeNameCode, $year, $dept_subject_div){
        $query = $this->MUnivCodeHead::select('id');
        $query->where('university_code_name_cd', '=', $univCodeNameCode);
        $query->where('fiscal_year', '=', $year);
        $query->where('dept_subject_div', '=', $dept_subject_div);

        return $query->first();

    }
}
