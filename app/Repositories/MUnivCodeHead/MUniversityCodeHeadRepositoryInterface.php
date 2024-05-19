<?php

namespace App\Repositories\MUnivCodeHead;

/**
 * ディレクトリマスタ用リポジトリ
 *
 */
interface MUniversityCodeHeadRepositoryInterface {

    public function createOrUpdateCodeHead($codeHeadArray, $mode);

    public function getCodeHeader($dataCodeHead);
   
}