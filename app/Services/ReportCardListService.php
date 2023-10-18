<?php

namespace App\Services;

use App\Repositories\MClass\MClassRepositoryInterface as MClassRepositoryInterface;
use App\Repositories\MDirectory\MDirectoryRepositoryInterface as MDirectoryRepositoryInterface;
use App\Services\MGMKAIService as MGMKAIService;

/**
 * 成績表一覧サービス
 */
class ReportCardListService {
    
    // リポジトリ
    // 教室マスタ 
    private $repositoryMClass;
    // ディレクトリマスタ 
    private $repositoryMDirectory;
    // 回数コンバートマスタサービス
    private $mgmkaiService;
    
    /**
     * コンストラクタ
     */
    public function __construct(MClassRepositoryInterface $repositoryMClass
                                ,MDirectoryRepositoryInterface $repositoryMDirectory
                                ,MGMKAIService $mgmkaiService){
        $this->repositoryMClass = $repositoryMClass;
        $this->repositoryMDirectory = $repositoryMDirectory;
        $this->mgmkaiService = $mgmkaiService;
    }
    
    /**
     * テスト回数別テスト一覧
     */
    public function selecttestCountPearsonList(int $sch_code ,?int $cls_code = null,array $param){
        return $this->repositoryMDirectory->selectTestCountPearsonList($sch_code ,$cls_code,$param);
    }
    
    /**
     * テスト教室リスト取得
     */
    public function selectClassBoxList(int $sch_code){
        return $this->repositoryMClass->selectClassBoxList($sch_code);
    }
    /**
     * 検索処理
     * @param int $schCode  塾コード
     * @param int $clsCode  教室コード
     * @return type collection
     */
    public function search(int $sch_code ,?int $cls_code = null,array $param){
        
        // 検索処理
        $selectList = $this->selecttestCountPearsonList($sch_code ,$cls_code,$param);
        
        if($selectList->count() == 0) return null;
        
        return $selectList;
    }
    
    /**
     * PDFファイルパスを検索
     * @param int $sch_code  塾コード
     * @param int $cls_code  教室コード
     * @param int $grade  学年
     * @param string $category_kbn  識別子
     * @param int $cnttest  テスト回数
     * @return type MDirectory 
     */
    public function selectFilePath(array $param){
        return $this->repositoryMDirectory->selectFilePath($param);
    }
    
    /**
     * テスト回数検索
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
            $screenAry[] = array(
                "year" => $entry->year,
                "grade" => $entry->grade,
                "testName" => $setStr,
                "cnttest" => $entry->cnttest,
            );
        }
        return $screenAry;
    }
}
