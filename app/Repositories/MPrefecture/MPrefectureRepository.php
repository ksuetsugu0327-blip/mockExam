<?php

namespace App\Repositories\MPrefecture;

use App\Models\MPrefecture;

/**
 * 都道府県マスタ用リポジトリ
 */
class MPrefectureRepository implements MPrefectureRepositoryInterface
{
    /**
     * 都道府県マスタモデル
     *
     * @var type model
     */
    private $MPrefecture;

    /**
     * コンストラクタ
     *
     * @param  MPrefecture  $mPrefecture  都道府県マスタモデル
     */
    public function __construct(MPrefecture $mPrefecture)
    {
        $this->MPrefecture = $mPrefecture;
    }

    /**
     * 都道府県マスタプルダウン用リスト
     *
     * @return 都道府県リスト
     */
    public function selectList()
    {
        return $this->MPrefecture::select('pref_code', 'name')
            ->whereBetween('pref_code', [1, 47])
            ->orderby('pref_code')
            ->get();
    }
}
