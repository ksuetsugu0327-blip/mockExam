<?php

namespace App\Repositories\MUnivDeptLine;

use App\Models\MUniversityDeptLine;

/**
 * 大学設置区分マスタ 用リポジトリ
 */
class MUniversityDeptLineRepository implements MUniversityDeptLineRepositoryInterface
{
    /**
     * 大学設置区分マスタ モデル
     *
     * @var type model
     */
    private $MUnivDeptLine;

    /**
     * コンストラクタ
     *
     * @param  MUniversityDeptLine  $mUnivDeptLine  大学設置区分マスタモデル
     */
    public function __construct(MUniversityDeptLine $mUnivDeptLine)
    {
        $this->MUnivDeptLine = $mUnivDeptLine;
    }

    /**
     * 大学設置者マスタプルダウン用リスト
     *
     * @return 大学設置者リスト
     */
    public function selectList()
    {
        return $this->MUnivDeptLine::where('invalid_flag', 0)
            ->orderby('dept_line_cd', 'asc')
            ->get();
    }
}
