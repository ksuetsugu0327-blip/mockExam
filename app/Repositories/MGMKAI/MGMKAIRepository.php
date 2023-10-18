<?php

namespace App\Repositories\MGMKAI;

use App\Repositories\MGMKAI\MGMKAIRepositoryInterface as MGMKAIRepositoryInterface;
use App\Models\Mgmkai as MGMKAI;


/**
 * ディレクトリマスタ用リポジトリ
 *
 */
class MGMKAIRepository implements MGMKAIRepositoryInterface{
    
    /**
     * ディレクトリマスタモデル
     * @var type model
     */
    private $MGMKAI;
    
    /**
     * コンストラクタ
     * @param MGMKAI $mDirectory  ディレクトリマスタモデル
     */
    public function __construct(MGMKAI $mDirectory){
        $this->MGMKAI = $mDirectory;
    }
    
    /**
     * 回数コンバートマスタ業務キー検索
     * @param int $year テスト年度
     * @param string $testKbn 回数コンバート区分
     * @param int $grade 学年　
     * @return type
     */
    public function selectOneByKey(int $year,string $testKbn,int $grade){
        return $this->MGMKAI::where('KAIA001',$year) 
                            ->where('KAIA010', $testKbn) 
                            ->where('KAIA020', $grade) 
                            ->get();
    }

    /**
     * 回数コンバートマスタ業務キー検索
     * @param string $testKbn 回数コンバート区分
     * @return type
     */
    public function selectCntTestKbnAll(string $testKbn){
        return $this->MGMKAI::where('KAIA010', $testKbn) 
                            ->get();
    }
}
