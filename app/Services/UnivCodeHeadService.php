<?php

namespace App\Services;

use App\Repositories\MUnivCodeHead\MUniversityCodeHeadRepositoryInterface;

class UnivCodeHeadService
{

    // リポジトリ
    // 大学コードヘッド
    private $repositoryMUniversityCodeHead;
    
    /**
     * コンストラクタ
     */
    public function __construct(
        MUniversityCodeHeadRepositoryInterface $repositoryMUniversityCodeHead
    )
    {
        $this->repositoryMUniversityCodeHead = $repositoryMUniversityCodeHead;
    }

    /**
     * 【テスト情報】大学コードマスタプルダウン用リスト
     * @param string $year
     */
    public function selectList(string $year)
    {
        return $this->repositoryMUniversityCodeHead->selectList($year);
    }
}
