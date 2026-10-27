<?php

namespace App\Services;

use App\Repositories\MHighSchoolCode\MHighSchoolCodeRepositoryInterface;

/**
 * 大学コードデータ名マスタ用サービス
 */
class MHighSchoolCodeService
{
    // リポジトリ
    // OEMSchoolマスタ
    private $repository;

    /**
     * コンストラクタ
     */
    public function __construct(MHighSchoolCodeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 一覧取得
     */
    public function getHighSchoolCode(array $conditions)
    {
        return $this->repository->getHighSchoolCode($conditions);
    }
}
