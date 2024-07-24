<?php

namespace App\Repositories\MCodeBook;

/**
 * コード表マスタ用リポジトリ
 *
 */
interface MCodeBookRepositoryInterface
{
    /**
     * プルダウン用リスト
     */
    public function selectList();
}
