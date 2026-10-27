<?php

namespace App\Repositories\MTestcount;

use App\Models\MTestcount;

/**
 * 塾マスタ（回数毎）用リポジトリ
 */
class MTestcountRepository implements MTestcountRepositoryInterface
{
    /**
     * 塾マスタ（回数毎）モデル
     *
     * @var type model
     */
    private $MTestcount;

    /**
     * コンストラクタ
     *
     * @param  MTestcount  $mDirectory  ディレクトリマスタモデル
     */
    public function __construct(MTestcount $mTestcount)
    {
        $this->MTestcount = $mTestcount;
    }

    /**
     * 塾マスタ（回数毎）業務キー検索
     *
     * @param  int  $year  テスト年度
     * @param  string  $cnttest  テスト回数
     * @param  int  $schCode  塾コード
     * @return type
     */
    public function selectOneByKey(int $year, int $cnttest, int $schCode)
    {
        return $this->MTestcount::where('year', $year)
            ->where('cnttest', $cnttest)
            ->where('sch_code', $schCode)
            ->get();
    }
}
