<?php

namespace App\Repositories\MUniversityCodeName;

use App\Repositories\MUniversityCodeName\MUniversityCodeNameRepositoryInterface as MUniversityCodeNameRepositoryInterface;
use App\Models\MUniversityCodeName as MUniversityCodeName;

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

    /**
     * 大学コードデータ名マスタプルダウン用リスト
     * @return 大学コードデータ名リスト
     */
    public function selectList()
    {
        return $this->MUniversityCodeName::where('invalid_flag', 0) 
            ->orderby('university_code_name_cd', 'asc') 
            ->get();
    }
}
