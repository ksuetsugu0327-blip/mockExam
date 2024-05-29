<?php

namespace App\Repositories\MUnivCodeHead;

/**
 * ディレクトリマスタ用リポジトリ
 *
 */
interface MUniversityCodeHeadRepositoryInterface
{

    public function createOrUpdateCodeHead($codeHeadArray, $mode);

    public function getCodeHeader($dataCodeHead);

    /**
     * 【テスト情報】大学コードマスタプルダウン用リスト
     * @param string $year
     */
    public function selectList(string $year);
}