<?php

namespace App\Repositories\MUnivCodeName;

use App\Repositories\MUnivCodeName\MUniversityCodeNameRepositoryInterface as MUniversityCodeNameRepositoryInterface;
use App\Models\MUniversityCodeName as MUniversityCodeName;
use Illuminate\Support\Facades\DB;

/**
 * 大学コードデータ名マスタ用リポジトリ
 *
 */
class MUniversityCodeNameRepository implements MUniversityCodeNameRepositoryInterface
{
    /**
     * 大学コードデータ名マスタモデル
     * @var type model
     */
    private $MUniversityCodeName;

    /**
     * コンストラクタ
     * @param MUniversityCodeName $mUniversityCodeName  大学コードデータ名マスタモデル
     */
    public function __construct(MUniversityCodeName $mUniversityCodeName)
    {
        $this->MUniversityCodeName = $mUniversityCodeName;
    }

}
