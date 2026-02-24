<?php

namespace App\Repositories\MDirectory;

/**
 * ディレクトリマスタ用リポジトリ
 *
 */
interface MDirectoryRepositoryInterface {


    /**
     *　一覧検索処理
     * @param int $year
     * @param int $grade
     * @param int $sch_code
     * @param int $cls_code
     */
    public function selectList(array $conditions);
    /**
     * ファイル検索
     * @param int $year
     * @param int $cnttest
     * @param int $grade
     * @param string $category_kbn
     * @param int $sch_code
     * @param int $cls_code
     */
    public function selectFilePath(array $param);
    
    
    /**
     * 個人ファイル検索ファイル検索
     * @param int sch_code
     * @param int cls_code
     * @param array $param
     */
    public function selectTestCountPearsonList(int $sch_code ,?int $cls_code = null ,array $param);
    
    /**
     * 個人単票一覧取得
     * @param int sch_code
     * @param int psn_code
     */
    public function selectTestPearson(int $sch_code, int $cls_code, string $psn_code);

    /**
     * 志望校一覧用リスト（生徒画面用）
     * @param array $conditions 検索条件
     */
    public function selectSOCPersonList(array $conditions);

    /**
     * 成績表返却チェック（生徒画面用）
     * @param array $conditions 検索条件
     * @return 1:登録済、0:未登録
     */
    public function rptReturn(array $conditions);

}
