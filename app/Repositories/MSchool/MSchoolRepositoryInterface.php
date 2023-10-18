<?php

declare(strict_types=1);

namespace App\Repositories\MSchool;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 塾マスタ用リポジトリ
 */
interface MSchoolRepositoryInterface {
    
    /**
     * 業務キーでの検索処理
     */
    public function selectOneByKey(int $schCode);
    
    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param conditions        検索条件(連想配列）
     * @return $data            データコレクション(Eloque)
     */
    public function selectList(array $conditions);
    
    /**
     * 塾コード指定でのデータ削除処理
     * @param int $delSchCode   削除対象塾コード
     */
    public function delete(int $delSchCode);
    
    /**
     * 登録処理
     * @param array $school 塾情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $school);
    
    /**
     * 更新処理
     * @param array $school 塾情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $school);
    
}
