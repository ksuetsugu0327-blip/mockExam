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

    public function getUnivCount(array $conditions){
        return $this->repositoryMUniversityCode->getUnivCount($conditions);
    }

    public function getDeptCount(array $conditions){
        return $this->repositoryMUniversityCode->getDeptCount($conditions);
    }

    public function getSubjCount(array $conditions){
        return $this->repositoryMUniversityCode->getSubjCount($conditions);
    }

    public function getUnivCodeList(array $conditions){
        return $this->repositoryMUniversityCode->getUnivCodeList($conditions);
    }

    public function getDeptList(array $conditions, $deptSubjDiv){
        return $this->repositoryMUniversityCode->getDeptList($conditions, $deptSubjDiv);
    }

    public function getSubjList(array $conditions){
        return $this->repositoryMUniversityCode->getSubjList($conditions);
    }

}
