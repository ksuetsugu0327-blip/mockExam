<?php

namespace App\Repositories\MUnivCode;

/**
 * ディレクトリマスタ用リポジトリ
 *
 */
interface MUniversityCodeHeadRepositoryInterface {

    public function getUnivCodeHeadID($univCodeNameCode, $year, $dept_subject_div);
   
}