<?php

namespace App\Repositories\MHighSchoolCode;

/**
 * ディレクトリマスタ用リポジトリ
 */
interface MHighSchoolCodeRepositoryInterface
{
    public function clearTable();

    public function registerArray(array $highCodeArr);

    public function getHighSchoolCode(array $conditions);
}
