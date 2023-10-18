<?php

namespace App\Repositories\MDepartment;

use App\Repositories\MDepartment\MDepartmentRepositoryInterface as MDepartmentRepositoryInterface;
use App\Models\MDepartment as MDepartment;


/**
 * 学科マスタリポジトリ
 *
 */
class MDepartmentRepository implements MDepartmentRepositoryInterface
{
    
    /**
     * 学科マスタモデル
     * @var type model
     */
    private $MDepartment;
    
    /**
     * コンストラクタ
     * @param MDepartment $mDepartment  学科マスタモデル
     */
    public function __construct(MDepartment $mDepartment)
    {
        $this->MDepartment = $mDepartment;
    }
    
    /**
     * 学科プルダウン用リスト
     * @param array $conditions
     * @return 学科リスト
     */
    public function selectList(array $conditions)
    {
        $query = $this->MDepartment::select('dpt_code', 'dpt_name');

        // 大学コードが設定されている場合、検索条件に追加（設定されていない場合は件数０を返す）
        if (array_key_exists('univ_code', $conditions) && !empty($conditions['univ_code'])) {
            $query->where('univ_code', '=', $conditions['univ_code']);
        } else {
            return collect([]);
        }

        // 学部コードが設定されている場合、検索条件に追加（設定されていない場合は件数０を返す）
        if (array_key_exists('fcl_code', $conditions) && !empty($conditions['fcl_code'])) {
            $query->where('fcl_code', '=', $conditions['fcl_code']);
        } else {
            return collect([]);
        }

        // 表示順昇順
        $query->orderby('dpt_order');
        return $query->get();
    }
}
