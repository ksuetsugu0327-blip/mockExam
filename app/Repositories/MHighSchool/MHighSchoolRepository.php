<?php

namespace App\Repositories\MHighSchool;

use App\Models\MHighSchool;

/**
 * 高校マスタリポジトリ
 */
class MHighSchoolRepository implements MHighSchoolRepositoryInterface
{
    /**
     * 高校マスタモデル
     *
     * @var type model
     */
    private $MHighSchool;

    /**
     * コンストラクタ
     *
     * @param  MHighSchool  $mHighSchool  高校マスタモデル
     */
    public function __construct(MHighSchool $mHighSchool)
    {
        $this->MHighSchool = $mHighSchool;
    }

    /**
     * 高校プルダウン用リスト
     */
    public function selectList(array $conditions): 高校リスト
    {
        $query = $this->MHighSchool::select('hsc_code', 'hsc_name');

        // 都道府県コードが設定されている場合、検索条件に追加
        if (array_key_exists('pref_code', $conditions) && ! empty($conditions['pref_code'])) {
            // 99:その他が指定された場合は1～47以外のコードのものとする
            if ($conditions['pref_code'] == 99) {
                $query->whereNotBetween('pref_code', [1, 47]);
            } else {
                $query->where('pref_code', '=', $conditions['pref_code']);
            }
        }

        // 高校コードが設定されている場合、検索条件に追加（完全一致）
        if (array_key_exists('hsc_code', $conditions) && ! empty($conditions['hsc_code'])) {
            $query->where('hsc_code', '=', $conditions['hsc_code']);
        }

        // 高校名が設定されている場合、検索条件に追加（部分一致）
        if (array_key_exists('hsc_name', $conditions) && ! empty($conditions['hsc_name'])) {
            $query->where('hsc_name', 'like', '%'.$conditions['hsc_name'].'%');
        }

        // 高校コード昇順
        $query->orderby('hsc_code');

        return $query->get();
    }

    /**
     * 高校の都道府県コード取得
     *
     * @param string hsc_code
     */
    public function getPrefCode($hsc_code): 都道府県コード
    {
        return $this->MHighSchool::select('pref_code')
            ->where('hsc_code', '=', $hsc_code)
            ->first();
    }
}
