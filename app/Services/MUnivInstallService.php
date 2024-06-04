<?php

namespace App\Services;

use App\Repositories\MUnivInstall\MUniversityInstallRepositoryInterface;

/**
 * 大学コードデータ名マスタ用サービス
 */
class MUnivInstallService {

    // リポジトリ
    // 大学コードデータ名マスタ 
    private $repositoryMUniversityInstall;
    
    /**
     * コンストラクタ
     */
    public function __construct(MUniversityInstallRepositoryInterface $repositoryMUniversityInstall)
    {
        $this->repositoryMUniversityInstall = $repositoryMUniversityInstall;
    }

    public function selectList(){
        $this->repositoryMUniversityInstall->selectList();
    }

}
