<?php

namespace App\Services;

use App\Repositories\MPerson\MPersonRepositoryInterface;

/**
 * 個人マスタ用サービス
 */
class MPersonService
{
    // リポジトリ
    private $repository;

    public function __construct(MPersonRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 業務Keyによる検索
     *
     * @param  int  $schCode  塾コード
     * @param  string  $psnCode  個人コード
     * @return type MPerson
     */
    public function selectOneByKey(int $schCode, string $psnCode)
    {
        return $this->repository->selectOneByKey($schCode, $psnCode);
    }

    /**
     * 個人マスタ 業務キーでの検索（塾マスタ、教室マスタの情報も取得）
     *
     * @param  int  $schCode  塾コード
     * @param  string  $psnCode  個人コード
     */
    public function selectOneByKeyWithSchCls(int $schCode, string $psnCode)
    {
        return $this->repository->selectOneByKeyWithSchCls($schCode, $psnCode);
    }

    /**
     * パスワード更新処理
     *
     * @param  int  $schCode  塾コード
     * @param  int  $psnCode  個人コード
     * @param  string  $password  パスワード
     * @return type
     */
    public function registerPassword(int $schCode, string $psnCode, string $password)
    {
        return $this->repository->registerPassword($schCode, $psnCode, $password);
    }

    /**
     * 仮パスワード更新処理
     *
     * @param  int  $schCode  塾コード
     * @param  int  $psnCode  個人コード
     * @param  string  $tempPass  パスワード
     * @return type
     */
    public function registerTempPassword(int $schCode, string $psnCode, string $tempPass)
    {
        return $this->repository->registerTempPassword($schCode, $psnCode, $tempPass);
    }

    /**
     * 個人マスタ メール更新処理
     *
     * @param  int  $schCode  塾コード
     * @param  string  $psnCode  個人コード
     * @param  string  $email  メールアドレス
     * @return type
     */
    public function registerEmail(int $schCode, string $psnCode, string $email)
    {
        return $this->repository->registerEmail($schCode, $psnCode, $email);
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     *
     * @param  array  $conditions  検索条件
     * @return type データコレクション(Eloque)
     */
    public function selectList(array $conditions)
    {
        return $this->repository->selectList($conditions);
    }

    /**
     * 塾、個人コード指定での削除処理
     *
     * @param  int  $delSchCode  削除対象塾コード
     * @param  string  $delPsnCode  削除対象個人コード
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function delete(int $delSchCode, string $delPsnCode)
    {
        return $this->repository->delete($delSchCode, $delPsnCode);
    }

    /**
     * 登録処理
     *
     * @param  array  $person  個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $person)
    {
        return $this->repository->register($person);
    }

    /**
     * 更新処理
     *
     * @param  array  $person  個人情報(連想配列)
     * @param  int  $adminKbn  管理者区分
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $person, int $adminKbn)
    {
        return $this->repository->update($person, $adminKbn);
    }
}
