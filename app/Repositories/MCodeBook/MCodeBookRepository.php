<?php

namespace App\Repositories\MCodeBook;

use App\Models\MCodeBook;

/**
 * コード表マスタ用リポジトリ
 */
class MCodeBookRepository implements MCodeBookRepositoryInterface
{
    /**
     * コード表マスタモデル
     *
     * @var type model
     */
    private $MCodeBook;

    /**
     * コンストラクタ
     *
     * @param  MCodeBook  $MCodeBook  コード表マスタモデル
     */
    public function __construct(MCodeBook $MCodeBook)
    {
        $this->MCodeBook = $MCodeBook;
    }

    /**
     * コード表マスタプルダウン用リスト
     */
    public function selectList(): コード表リスト
    {
        return $this->MCodeBook::where('invalid_flag', 0)
            ->orderby('display_seq', 'asc')
            ->orderby('codebook_cd', 'asc')
            ->get();
    }
}
