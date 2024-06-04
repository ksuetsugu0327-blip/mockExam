<?php

namespace App\Repositories\MUnivCode;

/**
 * 大学コード情報用リポジトリ
 *
 */
interface MUniversityCodeRepositoryInterface {

    public function registerArray($univCodeHeadID, array $univCodeArr);

    public function deleteCodes($codeHeadID);

    public function getUnivCount(array $conditions);
}
