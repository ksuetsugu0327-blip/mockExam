<?php

namespace App\Repositories\TTicket;

/**
 * 志望校テーブル用リポジトリ
 */
interface TTicketRepositoryInterface
{
    /**
     * 志望校リスト
     *
     * @param  array  $conditions  検索条件
     * @return 志望校リスト
     */
    public function selectList(array $conditions);

    /**
     * 登録or更新処理
     *
     * @param  array  $choice  志望校情報連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function upsert(array $choice);

    /**
     * 削除処理
     *
     * @param  array  $conditions  志望校情報条件連想配列
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function delete(array $conditions);
}
