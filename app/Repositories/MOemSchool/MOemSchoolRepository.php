<?php

namespace App\Repositories\MOemSchool;

use App\Repositories\MOemSchool\MOemSchoolRepositoryInterface as MOemSchoolRepositoryInterface;
use App\Models\MOemSchool as MOemSchool;

/**
 * OEM塾マスタ用リポジトリ
 *
 */
class MOemSchoolRepository implements MOemSchoolRepositoryInterface
{
    /**
     * OEM塾マスタモデル
     * @var type model
     */
    private $MOemSchool;

    /**
     * コンストラクタ
     * @param MOemSchool $mOemSchool  OEM塾マスタモデル
     */
    public function __construct(MOemSchool $mOemSchool)
    {
        $this->MOemSchool = $mOemSchool;
    }

    /**
     * OEM塾マスタプルダウン用リスト
     * @return OEM塾リスト
     */
    public function selectList()
    {
        return $this->MOemSchool::where('invalid_flag', 0) 
            ->orderby('oem_sch_code', 'asc') 
            ->get();
    }

    /**
     * 塾名取得
     * @return OEM塾名取得
     */
    public function getOemSchoolName($OEMSchCode)
    {
        return $this->MOemSchool::select('oem_sch_code', 'oem_sch_name')
            ->where('invalid_flag', 0)
            ->where('oem_sch_code', $OEMSchCode)
            ->first();
    }
}
