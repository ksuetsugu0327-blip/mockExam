<?php

namespace App\Services;

use App\Repositories\MPrefecture\MPrefectureRepositoryInterface as MPrefectureRepositoryInterface;

/**
 * 都道府県マスタ用サービス
 */
class MPrefectureService {

    // リポジトリ
    // 都道府県マスタ 
    private $repositoryMPrefecture;
    
    /**
     * コンストラクタ
     */
    public function __construct(MPrefectureRepositoryInterface $repositoryMPrefecture)
    {
        $this->repositoryMPrefecture = $repositoryMPrefecture;
    }
    
    /**
     * 一覧取得
     */
    public function selectList()
    {
        return $this->repositoryMPrefecture->selectList();
    }

}
