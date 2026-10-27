<?php

namespace App\Services;

use App\Repositories\MHighSchoolClass\MHighSchoolClassRepositoryInterface;

/**
 * 大学コードデータ名マスタ用サービス
 */
class MHighSchoolClassService
{
    // リポジトリ
    // OEMSchoolマスタ
    private $repository;

    /**
     * コンストラクタ
     */
    public function __construct(MHighSchoolClassRepositoryInterface $repository)
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
