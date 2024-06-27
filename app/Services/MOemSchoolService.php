<?php

namespace App\Services;

use App\Repositories\MOemSchool\MOemSchoolRepositoryInterface as MOemSchoolRepositoryInterface;

/**
 * OEM塾マスタ用サービス
 */
class MOemSchoolService {

    // リポジトリ
    // OEM塾マスタ 
    private $repositoryMOemSchool;
    
    /**
     * コンストラクタ
     */
    public function __construct(MOemSchoolRepositoryInterface $repositoryMOemSchool)
    {
        $this->repositoryMOemSchool = $repositoryMOemSchool;
    }
    
    /**
     * 一覧取得
     */
    public function selectList($OEMSchCode="")
    {
        return $this->repositoryMOemSchool->selectList($OEMSchCode);
    }

    /**
     * 塾名取得
     */
    public function getOemSchoolName($OEMSchCode)
    {
        return $this->repositoryMOemSchool->getOemSchoolName($OEMSchCode);
    }
}
