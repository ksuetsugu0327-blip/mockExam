<?php

namespace App\Services;

use App\Repositories\MTestName\MTestNameRepositoryInterface;

/**
 * テスト名マスタ用サービス
 */
class MTestNameService
{
    // リポジトリ
    // テスト名マスタ
    private $repositoryMTestName;

    /**
     * コンストラクタ
     */
    public function __construct(MTestNameRepositoryInterface $repositoryMTestName)
    {
        $this->repositoryMTestName = $repositoryMTestName;
    }

    /**
     * 一覧取得
     */
    public function selectList()
    {
        return $this->repositoryMTestName->selectList();
    }
}
