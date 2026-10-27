<?php

namespace App\Repositories\MHighSchoolInstall;

use App\Models\MHighSchoolInstall;

/**
 * OEM塾マスタ用リポジトリ
 */
class MHighSchoolInstallRepository implements MHighSchoolInstallRepositoryInterface
{
    /**
     * 高校設置者マスタモデル
     *
     * @var type model
     */
    private $MHighSchoolInstall;

    /**
     * コンストラクタ
     *
     * @param  MOemSchool  $mOemSchool  OEM塾マスタモデル
     */
    public function __construct(MHighSchoolInstall $mHighSchoolInstall)
    {
        $this->MHighSchoolInstall = $mHighSchoolInstall;
    }

    /**
     * 高校設置者マスタプルダウン用リスト
     *
     * @return 高校設置者リスト
     */
    public function selectList()
    {
        return $this->MHighSchoolInstall::where('invalid_flag', 0)
            ->orderby('install_cd2', 'asc')
            ->get();
    }
}
