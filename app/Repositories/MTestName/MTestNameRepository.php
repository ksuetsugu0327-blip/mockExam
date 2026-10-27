<?php

namespace App\Repositories\MTestName;

use App\Models\MTestName;

/**
 * テスト名マスタ用リポジトリ
 */
class MTestNameRepository implements MTestNameRepositoryInterface
{
    /**
     * テスト名マスタモデル
     *
     * @var type model
     */
    private $MTestName;

    /**
     * コンストラクタ
     *
     * @param  MTestName  $mTestName  テスト名マスタモデル
     */
    public function __construct(MTestName $mTestName)
    {
        $this->MTestName = $mTestName;
    }

    /**
     * テスト名マスタプルダウン用リスト
     *
     * @return テスト名リスト
     */
    public function selectList()
    {
        return $this->MTestName::where('invalid_flag', 0)
            ->orderby('display_seq', 'asc')
            ->orderby('test_name_cd', 'asc')
            ->get();
    }
}
