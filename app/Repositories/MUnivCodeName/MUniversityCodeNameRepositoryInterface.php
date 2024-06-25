<?php

namespace App\Repositories\MUnivCodeName;

/**
 * 大学コードデータ名マスタ用リポジトリ
 *
 */
interface MUniversityCodeNameRepositoryInterface
{
    /**
     * 【高校・大学CSVデータ登録】大学コードデータ名マスタプルダウン用リスト
     * @param string $year
     */
    public function selectList();
}
