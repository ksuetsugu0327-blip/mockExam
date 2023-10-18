<?php

namespace App\Repositories\MFaculty;

use App\Repositories\MFaculty\MFacultyRepositoryInterface as MFacultyRepositoryInterface;
use App\Models\MFaculty as MFaculty;


/**
 * 学部マスタリポジトリ
 *
 */
class MFacultyRepository implements MFacultyRepositoryInterface
{
    /**
     * 学部マスタモデル
     * @var type model
     */
    private $MFaculty;

    /**
     * コンストラクタ
     * @param MFaculty $mFaculty  学部マスタモデル
     */
    public function __construct(MFaculty $mFaculty)
    {
        $this->MFaculty = $mFaculty;
    }

    /**
     * 学部プルダウン用リスト
     * @param array $conditions
     * @return 学部リスト
     */
    public function selectList(array $conditions)
    {
        $query = $this->MFaculty::select('fcl_code', 'fcl_name');
        
        // 学科フラグが設定されている場合、検索条件に追加（設定されていない場合は件数０を返す）
        if (array_key_exists('enable_flag', $conditions) && ($conditions['enable_flag'] == '1' || $conditions['enable_flag'] == '0')) {
            // 「1」の場合は学科を考慮するリスト
            if ($conditions['enable_flag'] == 1) {
                $query->where('department_enable' , '=', 1);
            } else {
                $query->where('faculty_enable' , '=', 1);
            }
        } else {
            return collect([]);
        }

        // 大学コードが設定されている場合、検索条件に追加（設定されていない場合は件数０を返す）
        if (array_key_exists('univ_code', $conditions) && !empty($conditions['univ_code'])) {
            $query->where('univ_code', '=', $conditions['univ_code']);
        } else {
            return collect([]);
        }

        // 表示順昇順
        $query->orderby('fcl_order');
        return $query->get();
    }
}
