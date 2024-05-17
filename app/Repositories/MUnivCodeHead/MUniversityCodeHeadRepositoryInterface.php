<?php

namespace App\Repositories\MUnivCodeHead;

/**
 * ディレクトリマスタ用リポジトリ
 *
 */
interface MUniversityCodeHeadRepositoryInterface {

    public function createCodeHead($codeHeadArray);

    public function getCodeHeader($dataCodeHead);
   
}