<?php

declare(strict_types=1);

namespace App\Repositories\MClass;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 教室マスタ用リポジトリ
 */
interface MClassRepositoryInterface
{
    /**
     * Primary Keyでの検索
     *
     * @param  int  $schCode  塾コード
     * @param  int  $clsCode  教室コード
     */
    public function selectOneByKey(int $schCode, int $clsCode);

    /**
     * 教室マスタ 業務キーでの検索（塾マスタの情報も取得）
     *
     * @param  int  $schCode  塾コード
     * @param  int  $clsCode  教室コード
     */
    public function selectOneByKeyWithSch(int $schCode, int $clsCode);

    /**
     * 塾教室一覧表示用
     */
    public function selectClassBoxList(int $schCode);

    /**
     * 引数指定での検索処理( 検索画面用 ）
     *
     * @param conditions 検索条件(連想配列）
     * @return $data データコレクション(Eloque)
     */
    public function selectList(array $conditions);

    /**
     * 教室コード指定でのデータ削除処理
     *
     * @param  int  $delClsCode  削除対象教室コード
     */
    public function delete(int $delSchCode, int $delClsCode);

    /**
     * 登録処理
     *
     * @param  array  $class  教室情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $class);

    /**
     * 更新処理
     *
     * @param  array  $class  塾情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $class);
}
