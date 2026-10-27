<?php

namespace App\Repositories\MPrefecture;

/**
 * 都道府県マスタ用リポジトリ
 */
interface MPrefectureRepositoryInterface
{
    /**
     * プルダウン用リスト
     */
    public function selectList();
}
