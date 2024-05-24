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
}