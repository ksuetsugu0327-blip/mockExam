<?php

namespace App\Repositories\MOemSchool;

/**
 * OEM塾マスタ用リポジトリ
 *
 */
interface MOemSchoolRepositoryInterface
{
    /**
     * プルダウン用リスト
     */
    public function selectList();

    /**
     * 塾名取得
     * @return OEM塾名取得
     */
    public function getOemSchoolByCode($OEMSchCode);

}
