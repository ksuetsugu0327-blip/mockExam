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
}
