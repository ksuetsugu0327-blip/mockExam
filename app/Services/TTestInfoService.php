<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\TTestInfo\TTestInfoRepositoryInterface;

/**
 *  テスト一覧用サービス
 */
class TTestInfoService
{
    // リポジトリ
    private $repository;

    public function __construct(TTestInfoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param array $conditions 検索条件(連想配列）
     * @return type
     */
    public function selectList(array $conditions)
    {
        return $this->repository->selectList($conditions);
    }

    /**
     * 登録処理
     * @param array $testInfo 登録用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $testInfo)
    {
        return $this->repository->register($testInfo);
    }

    /**
     * 更新処理
     * @param array $testInfo テスト情報(連想配列)
     * @param int $id
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $testInfo, int $id)
    {
        return $this->repository->update($testInfo, $id);
    }

    /**
     * テストID指定での削除処理
     * @param int $id 削除対象ID
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * ID指定での検索処理( 修正・詳細画面用 ）
     * @param int $id
     * @return type
     */
    public function getTestInfoById(int $id)
    {
        return $this->repository->getTestInfoById($id);
    }

    /**
     * 年度プルダウンリスト用意処理
     * @return array
     */
    public function generateYearsArray()
    {
        return $this->repository->generateYearsArray();
    }

    /**
     * テスト名リスト取得
     */
    public function getTestInfo($OEMSchCode, $grade)
    {
        return $this->repository->getTestInfo($OEMSchCode, $grade);
    }

    /**
     * 【大学コードヘッダー情報】大学コード検索画面用
     * @param int $testID
     */
    public function getUnivCodeHead(int $testID)
    {
        return $this->repository->getUnivCodeHead($testID);
    }
}
