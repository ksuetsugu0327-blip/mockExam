<?php

namespace App\Services;

use App\Repositories\MSchool\MSchoolRepositoryInterface as MSchoolRepositoryInterface;
use App\Repositories\MDirectory\MDirectoryRepositoryInterface as MDirectoryRepositoryInterface;
use App\Repositories\MGMKAI\MGMKAIRepositoryInterface as MGMKAIRepositoryInterface;
use App\Repositories\MTestcount\MTestcountRepositoryInterface as MTestcountRepositoryInterface;
use App\Repositories\TZipOrder\TZipOrderRepositoryInterface as TZipOrderRepositoryInterface;

/**
 * 回数コンバートテーブルサービス
 */
class MGMKAIService {
    
    // リポジトリ
    // 塾マスタ 
    private $repositoryMSchool;
    // 回数コンバートマスタ
    private $repositoryMGMKAI;
    // 回数コンバートマスタ
    private $repositoryMTestcount;
    
    
    /**
     * コンストラクタ
     * 
     */
    public function __construct(MSchoolRepositoryInterface $repositoryMSchool
                                ,MGMKAIRepositoryInterface $repositoryMGMKAI
                                ,MTestcountRepositoryInterface $repositoryMTestcount){
        $this->repositoryMSchool = $repositoryMSchool;
        $this->repositoryMGMKAI = $repositoryMGMKAI;
        $this->repositoryMTestcount = $repositoryMTestcount;
    }
    
    /**
     * テスト回数名取得処理
     * @param int $directoryGrp  ディレクトリ表示情報
     * @param int $schCode  塾コード
     * @param int $clsCode  教室コード
     * @return type MDirectory 
     */
    public function changeTestName($schCode){
        
        // 塾情報
        $querySch = $this->repositoryMSchool->selectOneByKey($schCode)->get(0);
        $cntconvertKbn = $querySch->cntconvert_kbn;
        //テスト情報取得
        $queryEntry = $this->repositoryMGMKAI->selectCntTestKbnAll($cntconvertKbn);
        $cntAry = array("cntconvert_kbn" => $querySch->cntconvert_kbn);
        $cntAry += array("cnttest_kbn" => $querySch->cnttest_kbn);
        foreach ($queryEntry as $entry)
        {
            // 記述、マーク情報取得
            $queryKbn = $this->repositoryMGMKAI->selectOneByKey($entry->KAIA001,config('constants.KAIA010_KBN_MARK_INPUT'),config('constants.KAIA020_KBN_MARK_INPUT'))->first();
            $totalary = array();
            $ary = array();
            $dateAry = array();
            $strAry = array();
            $inputCnt = 0;
            $markCnt = 0;
            $totalCnt = 0;
            for ($i = 1; $i <= 99; $i++) {
                if($entry["KAIA030_" . "$i"] == "1"){
                    // 受験区分／テスト種別／ﾒｯｾｰｼﾞ種別の判定
                    $totalCnt ++;
                    $strKbnName = "";
                    if($queryKbn["KAIA030_" . "$i"] == "0"){
                        // 通算回数設定
                        $totalary += array("$i" => "記述_" . $totalCnt . "回");

                        // 記述毎
                        $inputCnt ++;
                        $ary += array("$i" => "記述_" . $inputCnt . "回");
                        $strKbnName = "記述_";
                    } else {
                        // 通算回数設定
                        $totalary += array("$i" => "マーク_" . $totalCnt . "回");

                        // ﾏｰｸ毎
                        $markCnt ++;
                        $ary += array("$i" => "マーク_" . $markCnt . "回");
                        $strKbnName = "マーク_";
                    }

                    // 実施日
                    $queryMTestcount = $this->repositoryMTestcount->selectOneByKey($entry->KAIA001,$i,$schCode)->first();
                    $testDay = $queryMTestcount ? $queryMTestcount->test_day : "";
                    if(!empty($queryMTestcount)) {
                        $dateAry += substr($queryMTestcount->test_day, -4, 2) ?  array("$i" => $strKbnName . substr($testDay, -4, 2) . "月" ) : "" . "月";
                    } else {
                        $dateAry += array("$i" => "");
                    }
                    
                    // 任意文字列
                    if (!empty($queryMTestcount)) {
                        // 1学年の場合
                        if ($entry->KAIA020 == config('constants.FIRST_GRADE') && !empty($queryMTestcount->printgrade1)) {
                            // 帳票印字用回数文字列(1年生用)
                            $printgrade1 = $queryMTestcount->printgrade1;
                            $strAry  += $queryMTestcount->printgrade1 ?  array("$i" => $strKbnName . $printgrade1) : "" . "$printgrade1";
                        }
                        // 2学年の場合
                        if ($entry->KAIA020 == config('constants.SECOND_GRADE') && !empty($queryMTestcount->printgrade2)) {
                            // 帳票印字用回数文字列(2年生用)
                            $printgrade2 = $queryMTestcount->printgrade2;
                            $strAry  += $queryMTestcount->printgrade2 ?  array("$i" => $strKbnName . $printgrade2) : "" . "$printgrade2";
                        }
                        // 3学年の場合
                        if ($entry->KAIA020 == config('constants.THIRD_GRADE') && !empty($queryMTestcount->printgrade3)) {
                            // 帳票印字用回数文字列(3年生用)
                            $printgrade3 = $queryMTestcount->printgrade3;
                            $strAry  += $queryMTestcount->printgrade3 ?  array("$i" => $strKbnName . $printgrade3) : "" . "$printgrade3";
                        }
                    } else {
                        $strAry += array("$i" => "");
                    }
                } else {
                    $totalary += array("$i" => "");
                    $ary += array("$i" => "");
                }
            };

            // key 年度 + 学年 value 0:マーク、記述毎 ,1:通算回数 ,2:実施日 .3:任意文字列
            $cntAry += array(
                "$entry->KAIA001" . "$entry->KAIA020"  => array(
                        "0" => $ary,
                        "1" => $totalary,
                        "2" => $dateAry,
                        "3" => $strAry,
                )
            );
        }
        
        return $cntAry;
    }
}
