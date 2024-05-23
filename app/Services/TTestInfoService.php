<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\TTestInfo\TTestInfoRepositoryInterface;

/**
 *  テスト一覧用サービス
 */
class TTestInfoService
{

    // リポジトリ
    private $repository;

    public function __construct(TTestInfoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param array $conditions 検索条件(連想配列）
     * @return type
     */
    public function selectList(array $conditions)
    {
        return $this->repository->selectList($conditions);
    }
}