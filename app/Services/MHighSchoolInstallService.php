<?php

namespace App\Services;

use App\Repositories\MHighSchoolInstall\MHighSchoolInstallRepositoryInterface;

/**
 * 大学コードデータ名マスタ用サービス
 */
class MHighSchoolInstallService
{
    // リポジトリ
    // OEMSchoolマスタ
    private $repository;

    /**
     * コンストラクタ
     */
    public function __construct(MHighSchoolInstallRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 一覧取得
     */
    public function selectList()
    {
        return $this->repository->selectList();
    }
}
