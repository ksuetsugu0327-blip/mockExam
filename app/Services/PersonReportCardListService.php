<?php

namespace App\Services;

use App\Repositories\MDirectory\MDirectoryRepositoryInterface as MDirectoryRepositoryInterface;
use App\Repositories\MGMKAI\MGMKAIRepositoryInterface as MGMKAIRepositoryInterface;
use App\Services\MGMKAIService as MGMKAIService;

/**
 * 個人成績表一覧サービス
 */
class PersonReportCardListService {
    
    // リポジトリ
    // ディレクトリマスタ 
    private $repositoryMDirectory;
    // 回数コンバートマスタ
    private $repositoryMGMKAI;
    // 回数コンバートサービス
    private $mgmkaiService;
    
    /**
     * コンストラクタ
     */
    public function __construct(MDirectoryRepositoryInterface $repositoryMDirectory
                                ,MGMKAIRepositoryInterface $repositoryMGMKAI
                                ,MGMKAIService $mgmkaiService){
        $this->repositoryMDirectory = $repositoryMDirectory;
        $this->repositoryMGMKAI = $repositoryMGMKAI;
        $this->mgmkaiService = $mgmkaiService;
    }
    
    /**
     * テスト回数別テスト一覧
     */
    public function selectTestPearson(int $sch_code ,string $psn_id){
        return $this->repositoryMDirectory->selectTestPearson($sch_code ,$psn_id);
    }
    /**
     * 検索処理
     * @param int $schCode  塾コード
     * @param int $psnId  教室コード
     * @return type collection
     */
    public function search(int $sch_code ,string $psn_id){
        
        // 検索処理
        $selectList = $this->selectTestPearson($sch_code ,$psn_id);
        
        if($selectList->count() == 0) return null;
        
        $testNameList = $this->changeTestCount($selectList,$sch_code);
        
        return $testNameList;
    }
    
    /**
     * テスト回数検索検索
     * @param int $schCode  塾コード
     * @param int $clsCode  教室コード
     * @return type MDirectory 
     */
    public function changeTestCount($entryList , $sch_code) {
        
        //　テスト表示名取得
        $cntAry =  $this->mgmkaiService->changeTestName($sch_code);
        
        // テスト名追加
        $screenAry;
        $cnt = 0;
        foreach ($entryList as $entry)
        {
            $cnt++;
            // 回数名設定
            $setStr = isset($cntAry[$entry->year . $entry->grade][$cntAry["cnttest_kbn"]][$entry->cnttest]) ? $cntAry[$entry->year . $entry->grade][$cntAry["cnttest_kbn"]][$entry->cnttest] : ""; 
            // データ集情報取得
            $datFile = $this->repositoryMDirectory->selectFilePath([
                                                'year' => $entry->year,
                                                'grade' => $entry->grade,
                                                'testName' => $setStr,
                                                'cnttest' => $entry->cnttest,
                                                'category_kbn' => config('constants.FILE_KBN_DAT')
                                            ]);
            // 解答解説情報取得
            $cmtFile = $this->repositoryMDirectory->selectFilePath([
                                                'year' => $entry->year,
                                                'grade' => $entry->grade,
                                                'testName' => $setStr,
                                                'cnttest' => $entry->cnttest,
                                                'category_kbn' => config('constants.FILE_KBN_CMT')
                                            ]);
            $screenAry[] = array(
                'year' => $entry->year,
                'grade' => $entry->grade,
                'testName' => $setStr,
                'cnttest' => $entry->cnttest,
                'psn_report_publish_flg' => $entry->psn_report_publish_flg,
                'cmtfullpath' => $entry->cmtfullpath,
                'datfullpath' => $entry->datfullpath,
                config('constants.FILE_KBN_PSN') => $entry,
                config('constants.FILE_KBN_DAT') => $datFile,
                config('constants.FILE_KBN_CMT') => $cmtFile,
            );
        }
        return $screenAry;
    }
}
