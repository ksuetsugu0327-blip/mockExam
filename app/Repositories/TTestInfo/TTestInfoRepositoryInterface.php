<?php

declare(strict_types=1);

namespace App\Repositories\TTestInfo;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * テスト情報用リポジトリ
 */
interface TTestInfoRepositoryInterface
{

    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param conditions        検索条件(連想配列）
     * @return $data            データコレクション(Eloque)
     */
    public function selectList(array $conditions);

    /**
     * 登録処理
     * @param array $testInfo テスト情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $testInfo);

    /**
     * 更新処理
     * @param array $testInfo テスト情報(連想配列)
     * @param int $id
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $testInfo, int $id);

    /**
     * テストID指定でのデータ削除処理
     * @param int $id   削除対象ID
     */
    public function delete(int $id);

    /**
     * ID指定での検索処理( 修正・詳細画面用 ）
     * @param int $id
     * @return \App\Models\TTestInfo|null
     */
    public function getTestInfoById(int $id);

    /**
     * 年度プルダウンリスト用意処理
     * @return array
     */
    public function generateYearsArray();

    /**
     * テスト名リスト取得
     * @return array
     */
    public function getTestInfo(int $OEMSchCode, int $grade);

    /**
     * 【【大学コードヘッダー情報】大学コード検索画面用
     * @param string $testID
     * @return 大学コードヘッダー
     */
    public function getUnivCodeHead(int $testID);
}
