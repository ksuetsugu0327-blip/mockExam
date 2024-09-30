<?php

namespace App\Repositories\MUnivCodeHead;

/**
 * ディレクトリマスタ用リポジトリ
 *
 */
interface MUniversityCodeHeadRepositoryInterface
{

    public function createOrUpdateCodeHead($codeHeadArray, $mode);

    public function getCodeHeader($param);

    /**
     * 【テスト情報】大学コードマスタプルダウン用リスト
     * @param string $year
     */
    public function selectList(string $year);

    /**
     * 年度プルダウンリスト用意処理
     * @return array
     */
    public function generateYearsArray();

    /**
     * 【大学コードデータ削除】一覧取得処理
     * @param array $conditions 検索条件(連想配列）
     * @return 大学コードデータリスト
     */
    public function univCodeDataList(array $conditions);

    /**
     * 【大学コードデータ削除】一覧削除処理
     * @param int $id   削除対象ID
     */
    public function delete(int $id);
}
