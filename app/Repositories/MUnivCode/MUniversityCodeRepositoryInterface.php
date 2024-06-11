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

    public function getDeptCount(array $conditions);
    
    public function getSubjCount(array $conditions);

    public function getUnivCodeList(array $conditions);

    public function getDeptList($univCodeHeadID, $univCode, $offset);

    public function getSubjList($univCodeHeadID, $univCode, $deptCode, $offset);
}
