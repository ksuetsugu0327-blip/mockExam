<?php

namespace App\Repositories\MUniversity;

/**
 * 大学マスタ用リポジトリ
 */
interface MUniversityRepositoryInterface
{
    /**
     * 大学プルダウン用リスト
     *
     * @return 大学リスト
     */
    public function selectList(array $conditions);
}
