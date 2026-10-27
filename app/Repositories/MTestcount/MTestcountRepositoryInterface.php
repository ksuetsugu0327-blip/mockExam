<?php

namespace App\Repositories\MTestcount;

/**
 * ディレクトリマスタ用リポジトリ
 */
interface MTestcountRepositoryInterface
{
    /**
     * 一覧検索処理
     */
    public function selectOneByKey(int $year, int $testCnt, int $grade);
}
