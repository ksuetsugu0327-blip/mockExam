<?php

namespace App\Services;

use App\Repositories\MUnivCode\MUniversityCodeRepositoryInterface as MUniversityCodeRepositoryInterface;

/**
 * 大学コードデータ名マスタ用サービス
 */
class MUnivCodeService {

    // リポジトリ
    // 大学コードデータ名マスタ 
    private $repositoryMUniversityCode;
    
    /**
     * コンストラクタ
     */
    public function __construct(MUniversityCodeRepositoryInterface $repositoryMUniversityCode)
    {
        $this->repositoryMUniversityCode = $repositoryMUniversityCode;
    }

    public function getUnivCount($conditions){
        return $this->repositoryMUniversityCode->getUnivCount($conditions);
    }

    public function getDeptCount($conditions){
        return $this->repositoryMUniversityCode->getDeptCount($conditions);
    }

    public function getSubjCount($conditions){
        return $this->repositoryMUniversityCode->getSubjCount($conditions);
    }

    public function getUnivCodeList($conditions){
        return $this->repositoryMUniversityCode->getUnivCodeList($conditions);
    }

    public function getDeptList($univCodeHeadID, $univCode, $offset){
        return $this->repositoryMUniversityCode->getDeptList($univCodeHeadID, $univCode, $offset);
    }

    public function getSubjList($univCodeHeadID, $univCode, $deptCode, $offset){
        return $this->repositoryMUniversityCode->getSubjList($univCodeHeadID, $univCode, $deptCode, $offset);
    }

}
