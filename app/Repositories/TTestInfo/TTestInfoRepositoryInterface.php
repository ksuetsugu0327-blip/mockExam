<?php

declare(strict_types=1);

namespace App\Repositories\TTestInfo;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 塾マスタ用リポジトリ
 */
interface TTestInfoRepositoryInterface {

    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param conditions        検索条件(連想配列）
     * @return $data            データコレクション(Eloque)
     */
    public function selectList(array $conditions);

    /**
     * 登録処理
     * @param array $testInfo 塾情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $testInfo);
}
