<?php

namespace App\Services;

use App\Repositories\MCodeBook\MCodeBookRepositoryInterface as MCodeBookRepositoryInterface;

/**
 * OEM塾マスタ用サービス
 */
class MCodeBookService {

    // リポジトリ
    // OEM塾マスタ 
    private $repositoryMCodeBook;
    
    /**
     * コンストラクタ
     */
    public function __construct(MCodeBookRepositoryInterface $repositoryMCodeBook)
    {
        $this->repositoryMCodeBook = $repositoryMCodeBook;
    }
    
    /**
     * 一覧取得
     */
    public function selectList()
    {
        return $this->repositoryMCodeBook->selectList();
    }
}
