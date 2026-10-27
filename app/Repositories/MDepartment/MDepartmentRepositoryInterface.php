<?php

namespace App\Repositories\MDepartment;

/**
 * 学科マスタ用リポジトリ
 */
interface MDepartmentRepositoryInterface
{
    /**
     * 学科プルダウン用リスト
     *
     * @return 学科リスト
     */
    public function selectList(array $conditions);
}
