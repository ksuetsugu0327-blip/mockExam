<?php

namespace App\Services;

use App\Repositories\MHighSchool\MHighSchoolRepositoryInterface as MHighSchoolRepositoryInterface;

/**
 * 高校マスタ用サービス
 */
class MHighSchoolService {

    // リポジトリ
    // 高校マスタ 
    private $repositoryMHighSchool;
    
    /**
     * コンストラクタ
     */
    public function __construct(MHighSchoolRepositoryInterface $repositoryMHighSchool)
    {
        $this->repositoryMHighSchool = $repositoryMHighSchool;
    }

    /**
     * 一覧取得
     */
    public function selectList(array $conditions)
    {
        $list = $this->repositoryMHighSchool->selectList($conditions)
                                        ->pluck('hsc_name', 'hsc_code')
                                        ->toArray();

        return $list;
    }

    /**
     * 高校の都道府県コード取得
     * @param string hsc_code
     * @return 都道府県コード
     */
    public function getPrefCode($hsc_code)
    {
        return $this->repositoryMHighSchool->getPrefCode($hsc_code)['pref_code'];
    }
}
