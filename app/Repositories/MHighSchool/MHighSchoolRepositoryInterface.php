<?php

namespace App\Repositories\MHighSchool;

/**
 * 高校マスタ用リポジトリ
 */
interface MHighSchoolRepositoryInterface
{
    /**
     * 高校プルダウン用リスト
     *
     * @return 高校リスト
     */
    public function selectList(array $conditions);

    /**
     * 高校の都道府県コード取得
     *
     * @param string hsc_code
     * @return 都道府県コード
     */
    public function getPrefCode($hsc_code);
}
