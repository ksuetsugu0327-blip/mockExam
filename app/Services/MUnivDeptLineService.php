<?php

namespace App\Services;

use App\Repositories\MUnivDeptLine\MUniversityDeptLineRepositoryInterface;

/**
 * 大学コードデータ名マスタ用サービス
 */
class MUnivDeptLineService
{
    // リポジトリ
    // 大学コードデータ名マスタ
    private $repositoryMUniversityDeptLine;

    /**
     * コンストラクタ
     */
    public function __construct(MUniversityDeptLineRepositoryInterface $repositoryMUniversityDeptLine)
    {
        $this->repositoryMUniversityDeptLine = $repositoryMUniversityDeptLine;
    }

    public function selectList()
    {
        return $this->repositoryMUniversityDeptLine->selectList();
    }
}
