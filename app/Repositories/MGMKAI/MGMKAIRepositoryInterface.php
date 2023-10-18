<?php

namespace App\Repositories\MGMKAI;

/**
 * ディレクトリマスタ用リポジトリ
 *
 */
interface MGMKAIRepositoryInterface {


    /**
     * 一覧検索処理
     */
    public function selectOneByKey(int $year,string $testKbn,int $grade);
    
    /**
     * 区分から対象データ取得 
     */
    public function selectCntTestKbnAll(string $testKbn);
   
}
