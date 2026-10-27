<?php

namespace App\Repositories\MHighSchoolClass;

use App\Models\MHighSchoolClass;

/**
 * OEM塾マスタ用リポジトリ
 */
class MHighSchoolClassRepository implements MHighSchoolClassRepositoryInterface
{
    /**
     * 高校設置者マスタモデル
     *
     * @var type model
     */
    private $MHighSchoolClass;

    /**
     * コンストラクタ
     *
     * @param  MOemSchool  $mOemSchool  OEM塾マスタモデル
     */
    public function __construct(MHighSchoolClass $mHighSchoolClass)
    {
        $this->MHighSchoolClass = $mHighSchoolClass;
    }

    /**
     * OEM塾マスタプルダウン用リスト
     *
     * @return OEM塾リスト
     */
    public function selectList(): OEM塾リスト
    {
        return $this->MHighSchoolClass::where('invalid_flag', 0)
            ->orderby('class_cd', 'asc')
            ->get();
    }
}
