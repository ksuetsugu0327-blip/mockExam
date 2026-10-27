<?php

namespace App\Repositories\MUniversity;

use App\Models\MUniversity;

/**
 * 大学マスタリポジトリ
 */
class MUniversityRepository implements MUniversityRepositoryInterface
{
    /**
     * 大学マスタモデル
     *
     * @var type model
     */
    private $MUniversity;

    /**
     * コンストラクタ
     *
     * @param  MUniversity  $mUniversity  大学マスタモデル
     */
    public function __construct(MUniversity $mUniversity)
    {
        $this->MUniversity = $mUniversity;
    }

    /**
     * 大学プルダウン用リスト
     */
    public function selectList(array $conditions): 大学リスト
    {
        // 必須条件確認フラグ
        $flag = false;

        $query = $this->MUniversity::select('univ_code', 'univ_name');

        // 設置区分が設定されている場合、検索条件に追加
        if (array_key_exists('inst_class', $conditions) && ! empty($conditions['inst_class'])) {
            $query->where('inst_class', '=', $conditions['inst_class']);
            $flag = true;
        }

        // 都道府県コードが設定されている場合、検索条件に追加
        if (array_key_exists('pref_code', $conditions) && ! empty($conditions['pref_code'])) {
            // 99:その他が指定された場合は1～47以外のコードのものとする
            if ($conditions['pref_code'] == 99) {
                $query->whereNotBetween('pref_code', [1, 47]);
            } else {
                $query->where('pref_code', '=', $conditions['pref_code']);
            }
            $flag = true;
        }

        // 設置区分、都道府県コードのいずれも設定されていない場合は件数０を返す）
        if (! $flag) {
            return collect([]);
        }

        // 大学コードが設定されている場合、検索条件に追加（完全一致）
        if (array_key_exists('univ_code', $conditions) && ! empty($conditions['univ_code'])) {
            $query->where('univ_code', '=', $conditions['univ_code']);
        }

        // 大学名が設定されている場合、検索条件に追加（部分一致）
        if (array_key_exists('univ_name', $conditions) && ! empty($conditions['univ_name'])) {
            $query->where('univ_name', 'like', '%'.$conditions['univ_name'].'%');
        }

        // 設置区分昇順、都道府県コード昇順、大学名かな昇順
        $query->orderby('inst_class')
            ->orderBy('pref_code')
            ->orderBy('univ_kana');

        return $query->get();
    }
}
