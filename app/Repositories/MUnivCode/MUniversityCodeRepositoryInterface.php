<?php

namespace App\Repositories\MUnivCode;

/**
 * ディレクトリマスタ用リポジトリ
 *
 */
interface MUniversityCodeRepositoryInterface {

    public function registerArray($univCodeHeadID, array $univCodeArr);

    public function deleteCodes($codeHeadID);
   
}