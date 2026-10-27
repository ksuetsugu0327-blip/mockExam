<?php

namespace App\Services;

use App\Repositories\MUnivCodeHead\MUniversityCodeHeadRepositoryInterface;

class UnivCodeHeadService
{
    // リポジトリ
    // 大学コードヘッド
    private $repositoryMUniversityCodeHead;

    /**
     * コンストラクタ
     */
    public function __construct(
        MUniversityCodeHeadRepositoryInterface $repositoryMUniversityCodeHead
    ) {
        $this->repositoryMUniversityCodeHead = $repositoryMUniversityCodeHead;
    }

    /**
     * 【テスト情報】大学コードマスタプルダウン用リスト
     */
    public function selectList(string $year)
    {
        return $this->repositoryMUniversityCodeHead->selectList($year);
    }

    /**
     * 年度プルダウンリスト用意処理
     *
     * @return array
     */
    public function generateYearsArray(): array
    {
        return $this->repositoryMUniversityCodeHead->generateYearsArray();
    }

    /**
     * 【大学コードデータ削除】一覧取得処理
     *
     * @param  array  $conditions  検索条件(連想配列）
     * @return 大学コードデータリスト
     */
    public function univCodeDataList(array $conditions): 大学コードデータリスト
    {
        return $this->repositoryMUniversityCodeHead->univCodeDataList($conditions);
    }

    /**
     *【大学コードデータ削除】一覧削除処理
     *
     * @param  int  $id  削除対象ID
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function delete(int $id)
    {
        return $this->repositoryMUniversityCodeHead->delete($id);
    }

    /**
     *【大学コードヘッダー存在チェック】大学コードCSV登録処理
     *
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function chkExistUnivCodeHead(int $fy, string $dtcd, int $dptsub)
    {
        return $this->repositoryMUniversityCodeHead->chkExistUnivCodeHead($fy, $dtcd, $dptsub);
    }
}
