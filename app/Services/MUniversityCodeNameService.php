<?php

namespace App\Services;

use App\Repositories\MUniversityCodeName\MUniversityCodeNameRepositoryInterface as MUniversityCodeNameRepositoryInterface;

/**
 * 大学コードデータ名マスタ用サービス
 */
class MUniversityCodeNameService {

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
    
    /**
     * 一覧取得
     */
    public function selectList()
    {
        return $this->repositoryMUniversityCodeName->selectList();
    }

}
