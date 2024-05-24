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
    public function selectList()
    {
        return $this->repositoryMOemSchool->selectList();
    }

}
