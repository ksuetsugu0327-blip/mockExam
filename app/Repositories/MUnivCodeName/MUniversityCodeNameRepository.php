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
                'university_code_name_cd',
                'university_code_name'
            )
            ->orderBy('display_seq', 'asc')
            ->orderBy('university_code_name_cd', 'asc')
            ->get();
    }

}
