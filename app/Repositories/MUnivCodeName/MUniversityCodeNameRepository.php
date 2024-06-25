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
    
    /**
     * 【高校・大学CSVデータ登録】大学コードデータ名マスタプルダウン用リスト
     * @return 大学コードリスト
     */
    public function selectList()
    {
        return $this->MUniversityCodeName::select(
            'm_university_code_name.university_code_name_cd',
            'university_code_name',
            'm_university_code_head.updated_at'
            )
            ->leftJoin('m_university_code_head', 'm_university_code_head.university_code_name_cd', 'm_university_code_name.university_code_name_cd')
            ->orderby('m_university_code_name.display_seq', 'asc')
            ->orderBy('m_university_code_name.university_code_name_cd','ASC')
            ->get();
    }

}
