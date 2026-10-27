<?php

namespace App\Services;

use App\Repositories\MDirectory\MDirectoryRepositoryInterface;
use App\Repositories\MGMKAI\MGMKAIRepositoryInterface;
use App\Repositories\MSchool\MSchoolRepositoryInterface;
use App\Repositories\MTestcount\MTestcountRepositoryInterface;
use App\Repositories\TZipOrder\TZipOrderRepositoryInterface;

/**
 * 管理資料一覧用サービス
 */
class ManagementListService
{
    // リポジトリ
    // 塾マスタ
    private $repositoryMSchool;

    // ディレクトリマスタ
    private $repositoryMDirectory;

    // 回数コンバートマスタ
    private $repositoryMGMKAI;

    // 塾マスタ(回数毎)
    private $repositoryMTestcount;

    // 発行指示
    private $repositoryTZipOrder;

    // 回数コンバートサービス
    private $mgmkaiService;

    /**
     * コンストラクタ
     */
    public function __construct(MSchoolRepositoryInterface $repositoryMSchool, MDirectoryRepositoryInterface $repositoryMDirectory, MGMKAIRepositoryInterface $repositoryMGMKAI, MTestcountRepositoryInterface $repositoryMTestcount, TZipOrderRepositoryInterface $repositoryTZipOrder, MGMKAIService $mgmkaiService)
    {
        $this->repositoryMSchool = $repositoryMSchool;
        $this->repositoryMDirectory = $repositoryMDirectory;
        $this->repositoryMGMKAI = $repositoryMGMKAI;
        $this->repositoryMTestcount = $repositoryMTestcount;
        $this->repositoryTZipOrder = $repositoryTZipOrder;
        $this->mgmkaiService = $mgmkaiService;
    }

    /**
     * ディレクトリ構成マスタ一覧情報取得
     *
     * @param  array  $conditions  条件連想配列
     * @return type
     */
    public function selectList(array $conditions)
    {
        return $this->repositoryMDirectory->selectList($conditions);
    }

    /**
     * 検索処理
     *
     * @param  array  $conditions  条件連想配列
     * @return type 表示情報連想配列
     */
    public function search(array $conditions)
    {
        // 検索処理
        $directoryGrp = $this->selectList($conditions);
        // 0件の場合リターン
        if ($directoryGrp->count() == 0) {
            return null;
        }
        // テスト回数テスト区分
        $screenList = $this->changeTestCount($directoryGrp, $conditions);

        return $screenList;
    }

    /**
     * ディレクトリ存在チェック
     *
     * @param  array  $param  条件連想配列
     * @return type MDirectory
     */
    public function checkDirectory(array $param)
    {
        return $this->repositoryMDirectory->checkDirectory($param);
    }

    /**
     * PDFファイルパスを検索
     *
     * @param  array  $param  条件連想配列
     * @return type MDirectory
     */
    public function selectFilePath(array $param)
    {
        return $this->repositoryMDirectory->selectFilePath($param);
    }

    /**
     * ZIPファイルパス取得
     *
     * @return type TZipOrder
     */
    public function zipDownloadPath(array $param)
    {
        return $this->repositoryTZipOrder->zipDownloadPath($param);
    }

    /**
     * テスト回数検索検索
     *
     * @param  array  $directoryGrp  ディレクトリ表示情報
     * @param  array  $conditions  条件連想配列
     * @return type MDirectory
     */
    public function changeTestCount($directoryGrp, array $conditions)
    {
        // 塾情報
        $schCode = $conditions['sch_code'];
        $clsCode = $conditions['cls_code'];

        $cntAry = $this->mgmkaiService->changeTestName($schCode);
        // テスト名追加

        $cnt = 0;
        foreach ($directoryGrp as $entry) {
            $cnt++;
            // 回数名設定
            $setStr = isset($cntAry[$entry->year.$entry->grade][$cntAry['cnttest_kbn']][$entry->cnttest]) ? $cntAry[$entry->year.$entry->grade][$cntAry['cnttest_kbn']][$entry->cnttest] : '';

            // テスト情報格納
            $inputAry = [
                'year' => $entry->year,
                'grade' => $entry->grade,
                'testName' => $setStr,
                'cnttest' => $entry->cnttest,
            ];

            // 画面情報を連想配列に設定
            // 会場内成績一覧表
            // zipパスワード取得
            $param = [
                'year' => $entry->year,
                'cnttest' => $entry->cnttest,
                'grade' => $entry->grade,
                'sch_code' => $schCode,
                'cls_code' => $clsCode,
            ];

            // ファイル情報取得
            if (! isset($clsCode)) {
                // 塾設定
                // 会場内成績一覧表（科目編）
                $param['category_kbn'] = config('constants.FILE_KBN_LS1');
                $inputAry += [config('constants.FILE_KBN_LS1') => $this->selectFilePath($param)];
                // 会場内成績一覧表（総合編）
                $param['category_kbn'] = config('constants.FILE_KBN_LS2');
                $inputAry += [config('constants.FILE_KBN_LS2') => $this->selectFilePath($param)];
                // 科目別学力分析表
                $param['category_kbn'] = config('constants.FILE_KBN_SBJ');
                $inputAry += [config('constants.FILE_KBN_SBJ') => $this->selectFilePath($param)];
                // 教室別成績比較表
                $param['category_kbn'] = config('constants.FILE_KBN_CLS');
                $inputAry += [config('constants.FILE_KBN_CLS') => $this->selectFilePath($param)];
                // データ集
                $param['category_kbn'] = config('constants.FILE_KBN_DAT');
                $inputAry += [config('constants.FILE_KBN_DAT') => $this->selectFilePath($param)];
                // 解答解説
                $param['category_kbn'] = config('constants.FILE_KBN_CMT');
                $inputAry += [config('constants.FILE_KBN_CMT') => $this->selectFilePath($param)];

            } else {
                // 教室内成績一覧表（科目編）
                $param['category_kbn'] = config('constants.FILE_KBN_LC1');
                $inputAry += [config('constants.FILE_KBN_LC1') => $this->selectFilePath($param)];
                // 教室内成績一覧表（総合編）
                $param['category_kbn'] = config('constants.FILE_KBN_LC2');
                $inputAry += [config('constants.FILE_KBN_LC2') => $this->selectFilePath($param)];
                // 科目別学力分析表
                $param['category_kbn'] = config('constants.FILE_KBN_SBJ');
                $inputAry += [config('constants.FILE_KBN_SBJ') => $this->selectFilePath($param)];
                // データ集
                $param['category_kbn'] = config('constants.FILE_KBN_DAT');
                $inputAry += [config('constants.FILE_KBN_DAT') => $this->selectFilePath($param)];
                // 解答解説
                $param['category_kbn'] = config('constants.FILE_KBN_CMT');
                $inputAry += [config('constants.FILE_KBN_CMT') => $this->selectFilePath($param)];
                // zipファイルパス
                $param['category_kbn'] = config('constants.CATEGORY_KBN_CLS');
                $inputAry += ['zipfilepass' => $this->zipDownloadPath($param)];
            }

            $screenAry[
                $entry->year.
                sprintf('%03d', $entry->cnttest).
                $entry->grade
            ] = $inputAry;
        }

        // インデックスを振りなおし
        $cntIndex = 0;
        $returnArray = [];
        foreach ($screenAry as $key => $val) {
            $returnArray[$cntIndex] = $val;
            $cntIndex++;
        }

        return $returnArray;
    }

    /**
     * 発行指示登録処理
     *
     * @param  array  $order  条件連想配列
     * @return type TZipOrder
     */
    public function registerOrder(array $order)
    {
        return $this->repositoryTZipOrder->register($order);
    }

    /**
     * ダウンロード発行指更新処理
     *
     * @return type
     */
    public function updateOrder(int $id)
    {
        return $this->repositoryTZipOrder->update($id);
    }

    /**
     * パスワードの複合化
     *
     * @param  type  $data  複合化文字列
     * @return type
     */
    public function decrypt($data)
    {

        $key = config('constants.APP_KEY');
        // 暗号化方式
        $method = 'AES-256-CBC';
        // IV(初期化ベクトル)に必要な長さを取得
        // $iv_length = openssl_cipher_iv_length($method);
        // IV(初期化ベクトル)をランダム生成
        // $iv = openssl_random_pseudo_bytes($iv_length);
        $iv = '1234567890123456';
        // OPENSSL_RAW_DATA と OPENSSL_ZERO_PADDING を指定可
        $options = 0;
        // openssl
        $decrypted = openssl_decrypt($data, $method, $key, $options, $iv);

        return $decrypted;
    }
}
