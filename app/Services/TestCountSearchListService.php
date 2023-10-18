<?php

namespace App\Services;

use App\Repositories\MDirectory\MDirectoryRepositoryInterface as MDirectoryRepositoryInterface;
use App\Repositories\MTestcount\MTestcountRepositoryInterface as MTestcountRepositoryInterface;
use App\Services\MGMKAIService as MGMKAIService;

/**
 * 試験回数一覧用サービス
 */
class TestCountSearchListService {
    
    // リポジトリ
    // ディレクトリマスタ 
    private $repositoryMDirectory;
    // 塾マスタ(回数毎) 
    private $repositoryMTestcount;
    // 回数コンバートサービス
    private $mgmkaiService;
    
    /**
     * コンストラクタ
     */
    public function __construct(MDirectoryRepositoryInterface $repositoryMDirectory
                                ,MTestcountRepositoryInterface $repositoryMTestcount
                                ,MGMKAIService $mgmkaiService){
        $this->repositoryMDirectory = $repositoryMDirectory;
        $this->repositoryMTestcount = $repositoryMTestcount;
        $this->mgmkaiService = $mgmkaiService;
    }
    
    /**
     * 一覧取得
     */
    public function selectList(array $conditions){
        return $this->repositoryMDirectory->selectList($conditions);
    }
    /**
     * 検索処理
     * @param int $schCode  塾コード
     * @param int $clsCode  教室コード
     * @return type collection
     */
    public function search(array $conditions){
        
        // 検索処理
        $directoryGrp = $this->selectList($conditions);
        
        if($directoryGrp->count() == 0) return null;
        
        // テスト回数を変換
        $screenList = $this->changeTestCount($directoryGrp,$conditions);
        
        return $screenList;
    }
    
    /**
     * テスト回数検索検索
     * @param int $schCode  塾コード
     * @param int $clsCode  教室コード
     * @return type MDirectory 
     */
    public function changeTestCount($directoryGrp,$conditions){
        
        //　テスト表示名取得
        $sch_code = $conditions["sch_code"];
        $cntAry =  $this->mgmkaiService->changeTestName($sch_code);
        
        // テスト名追加
        $screenAry;
        $cnt = 0;
        foreach ($directoryGrp as $entry)
        {
            $cnt++;
            // 回数名設定
            $setStr = isset($cntAry[$entry->year . $entry->grade][$cntAry["cnttest_kbn"]][$entry->cnttest]) ? $cntAry[$entry->year . $entry->grade][$cntAry["cnttest_kbn"]][$entry->cnttest] : ""; 
            
            $screenAry[
                $entry->year.
                sprintf('%03d', $entry->cnttest).
                $entry->grade
            ] = array(
                "year" => $entry->year,
                "grade" => $entry->grade,
                "testName" => $setStr,
                "cnttest" => $entry->cnttest,
            );
        }
        
        // インデックスを振りなおし
        $cntIndex = 0;
        $returnArray = array();
        foreach($screenAry as $key => $val){
            $returnArray[$cntIndex] = $val;
            $cntIndex++;
        }
    
        return $returnArray;
    }
}
