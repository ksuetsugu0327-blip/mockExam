<?php

namespace App\Repositories\MFaculty;

/**
 * 学部マスタ用リポジトリ
 *
 */
interface MFacultyRepositoryInterface
{

  /**
   * 学部プルダウン用リスト
   * @param array $conditions
   * @return 学部リスト
   */
  public function selectList(array $conditions);

}
