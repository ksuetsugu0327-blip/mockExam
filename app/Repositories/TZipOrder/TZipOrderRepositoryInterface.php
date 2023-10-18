<?php

namespace App\Repositories\TZipOrder;

/**
 * 教室マスタ用リポジトリ
 *
 */
interface TZipOrderRepositoryInterface {
    
    /**
     * 発行指示業務キーでの検索
     * @param int $schCode  塾コード
     * @param int $clsCode  教室コード
     */
    public function zipDownloadPath(array $param);
    /**
     * 発行指示登録
     * @param array $order
     */
    public function register(array $order);
    
        /**
     * 発行指示更新
     * @param array $order
     */
    public function update(int $id);
    
    
}
