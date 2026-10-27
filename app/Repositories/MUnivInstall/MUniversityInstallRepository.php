<?php

namespace App\Repositories\MUnivInstall;

use App\Models\MUniversityInstall;

/**
 * 大学設置区分マスタ 用リポジトリ
 */
class MUniversityInstallRepository implements MUniversityInstallRepositoryInterface
{
    /**
     * 大学設置区分マスタ モデル
     *
     * @var type model
     */
    private $MUnivInstall;

    /**
     * コンストラクタ
     *
     * @param  MUniversityInstall  $mUnivInstall  大学設置区分マスタモデル
     */
    public function __construct(MUniversityInstall $mUnivInstall)
    {
        $this->MUnivInstall = $mUnivInstall;
    }

    /**
     * 大学設置者マスタプルダウン用リスト
     */
    public function selectList(): 大学設置者リスト
    {
        return $this->MUnivInstall::where('invalid_flag', 0)
            ->orderby('install_cd', 'asc')
            ->get();
    }
}
