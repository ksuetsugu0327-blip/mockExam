<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\MSchool\MSchoolRepositoryInterface;

/**
 * 塾マスタ用サービス
 */
class MSchoolService
{
    // リポジトリ
    private $repository;

    public function __construct(MSchoolRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 業務キーによる検索
     *
     * @param  int  $schCode  塾コード
     * @return type MSchool
     */
    public function selectOneByKey(int $schCode)
    {
        return $this->repository->selectOneByKey($schCode);
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     *
     * @param  array  $conditions  検索条件(連想配列）
     * @return type
     */
    public function selectList(array $conditions)
    {
        return $this->repository->selectList($conditions);
    }

    /**
     * 塾コード指定での削除処理
     *
     * @param  int  $delSchCode  削除対象塾コード
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function delete(int $delSchCode)
    {
        return $this->repository->delete($delSchCode);
    }

    /**
     * 登録処理
     *
     * @param  array  $school  登録用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $school)
    {
        return $this->repository->register($school);
    }

    /**
     * 更新処理
     *
     * @param  array  $school  塾情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $school)
    {
        return $this->repository->update($school);
    }

    /**
     * 塾アカウントから塾コードを取得
     *
     * @return mixed
     */
    public function getCodeByAccount($portal_account)
    {
        return $this->repository->findBySchAccount($portal_account)->sch_code;
    }
}
