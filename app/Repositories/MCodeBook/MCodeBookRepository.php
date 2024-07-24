<?php

namespace App\Repositories\MCodeBook;

use App\Repositories\MCodeBook\MCodeBookRepositoryInterface as MCodeBookRepositoryInterface;
use App\Models\MCodeBook as MCodeBook;

/**
 * コード表マスタ用リポジトリ
 *
 */
class MCodeBookRepository implements MCodeBookRepositoryInterface
{
    /**
     * コード表マスタモデル
     * @var type model
     */
    private $MCodeBook;

    /**
     * コンストラクタ
     * @param MCodeBook $MCodeBook  コード表マスタモデル
     */
    public function __construct(MCodeBook $MCodeBook)
    {
        $this->MCodeBook = $MCodeBook;
    }

    /**
     * コード表マスタプルダウン用リスト
     * @return コード表リスト
     */
    public function selectList()
    {
        return $this->MCodeBook::where('invalid_flag', 0)
            ->orderby('display_seq', 'asc')
            ->orderby('codebook_cd', 'asc') 
            ->get();
    }
}
