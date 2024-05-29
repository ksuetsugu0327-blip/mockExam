<?php

namespace App\Services;

use App\Repositories\MUnivCodeName\MUniversityCodeNameRepositoryInterface as MUniversityCodeNameRepositoryInterface;

/**
 * 大学コードデータ名マスタ用サービス
 */
class MUnivCodeNameService {

    // リポジトリ
    // 大学コードデータ名マスタ 
    private $repositoryMUniversityCodeName;
    
    /**
     * コンストラクタ
     */
    public function __construct(MUniversityCodeNameRepositoryInterface $repositoryMUniversityCodeName)
    {
        $this->repositoryMUniversityCodeName = $repositoryMUniversityCodeName;
    }

}
