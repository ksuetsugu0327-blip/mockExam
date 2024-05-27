<?php

namespace App\Repositories\MUniversityCodeName;

use App\Repositories\MUniversityCodeName\MUniversityCodeNameRepositoryInterface as MUniversityCodeNameRepositoryInterface;
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

    /**
     * 大学コードデータ名マスタプルダウン用リスト
     * @return 大学コードデータ名リスト
     */
    public function selectList()
    {
        return $this->MUniversityCodeName::select(
                'm_university_code_head.id as university_code_head_id', 
                'm_university_code_name.university_code_name'
            )
            ->leftJoin('m_university_code_head', 'm_university_code_name.university_code_name_cd', '=', 'm_university_code_head.university_code_name_cd')
            ->where('m_university_code_name.invalid_flag', 0)
            ->orderby('m_university_code_name.university_code_name_cd', 'asc') 
            ->get();
    }
}
