<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\MClass\MClassRepositoryInterface as MClassRepositoryInterface;

/**
 * 教室マスタ用サービス
 */
class MClassService {

    // リポジトリ
    private $repository;

    public function __construct(MClassRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * Keyによる検索　
     * @param int $schCode  塾コード
     * @param int $clsCode  教室コード　　
     * @return type MClass 
     */
    public function selectOneByKey(int $schCode, int $clsCode) {
        return $this->repository->selectOneByKey($schCode, $clsCode);
    }

    /**
     * 教室マスタ 業務キーでの検索（塾マスタ、教室マスタの情報も取得）
     * @param int $schCode 　塾コード
     * @param int $clsCode　教室コード
     */
    public function selectOneByKeyWithSch(int $schCode, int $clsCode) {
        return $this->repository->selectOneByKeyWithSch($schCode, $clsCode);
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param array $conditions 検索条件(連想配列）
     * @return type
     * 
     */
    public function selectList(array $conditions) {
        return $this->repository->selectList($conditions);
    }

    //    public function selectList(?int $schCode, ?string $schName,?int $clsCode, ?string $clsName){
    //        return $this->repository->selectList($schCode,$schName,$clsCode, $clsName);
    //    }

    /**
     * 塾教室一覧表示用
     * @param int $schCode
     */
    public function selectClassBoxList(int $schCode) {
        // 教室マスタを取得するためのクエリ生成
        return $this->repository->selectClassBoxList($schCode);
    }

    /**
     * 教室コード指定での削除処理
     * @param int $delClsCode 削除対象塾コード
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException) 
     */
    public function delete(int $delSchCode, int $delClsCode) {
        return $this->repository->delete($delSchCode, $delClsCode);
    }

    /**
     * 登録処理
     * @param array $class 登録用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $class) {
        return $this->repository->register($class);
    }

    /**
     * 更新処理
     * @param array $class 教室情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $class) {
        return $this->repository->update($class);
    }


    /**
     * パスワード更新処理
     * @param int $schCode 塾コード
     * @param string $clsCode 教室コード
     * @param string $password パスワード
     * @return type
     */
    public function registerPassword(int $schCode,string $clsCode,string $password){
        return $this->repository->registerPassword($schCode,$clsCode,$password);
    }

}
