<?php

namespace App\Repositories\MPerson;

/**
 * 個人マスタ用リポジトリ
 *
 */
interface MPersonRepositoryInterface {
    
    /**
     * 個人マスタ 業務キーでの検索
     * @param int $schCode  塾コード
     * @param string $psnCode  個人コード
     */
    public function selectOneByKey(int $schCode,string $psnCode);
    
    /**
     * 個人マスタ 業務キーでの検索（塾マスタ、教室マスタの情報も取得）
     * @param int $schCode
     * @param string $psnCode
     */
    public function selectOneByKeyWithSchCls(int $schCode,string $psnCode);
    
    /**
     * パスワード変更登録
     * @param int $schCode  塾コード
     * @param string $psnCode  個人コード
     * @param string $password  パスワード
     * @return int id   更新対象のid
     */
    public function registerPassword(int $schCode,string $psnCode,string $password);

    /**
     * 仮パスワード更新処理
     * @param int $schCode 塾コード
     * @param string $psnCode 個人コード
     * @param string $tempPass 仮パスワード
     * @return int id 更新データのid
     */
    public function registerTempPassword(int $schCode, string $psnCode, string $tempPass);
    
    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param array $conditions
     * @return $data            データコレクション(Eloque)
     */
    public function selectList(array $conditions);    
    
    /**
     * 塾、個人コード指定での削除処理
     * @param int $delSchCode 削除対象塾コード
     * @param string $delPsnCode 削除対象個人コード
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException) 
     */
    public function delete(int $delSchCode,string $delPsnCode);
    
    /**
     * 登録処理
     * @param array $person 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $person);
    
    /**
     * 更新処理
     * @param array $person 個人情報(連想配列)
     * @param int $adminKbn 管理者区分    
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $person,int $adminKbn);
    
    /**
     * 登録処理
     * @param array $personAry 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray(array $personAry);
    
    /**
     * 高校更新処理
     * @param array $person 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function updateHighSchool(array $person);

}
