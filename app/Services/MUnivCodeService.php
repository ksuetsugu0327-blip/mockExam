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
        $this->repositoryMUniversityCode->getUnivCount($conditions);
    }

}
