<?php
declare(strict_types=1);

namespace App\Repositories\TTestInfo;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Repositories\TTestInfo\TTestInfoRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\ExclusionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TTestInfo as TTestInfo;

/**
 * 大学模擬試験テーブル用リポジトリ
 *
 */
class TTestInfoRepository implements TTestInfoRepositoryInterface
{
    /**
     * テスト情報モデル
     * @var type model
     */
    private $TTestInfo;

    /**
     * コンストラクタ
     * @param TTestInfo $tTestInfo  大学コードデータ名マスタモデル
     */
    public function __construct(TTestInfo $tTestInfo)
    {
        $this->TTestInfo = $tTestInfo;
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param array $conditions     検索条件(連想配列）
     */
    public function selectList(array $conditions)
    {
        $testLists = DB::table('t_test_info as t_ti')
                        ->select('t_ti.id', 'm_oem.oem_sch_name','t_ti.fiscal_year', 'm_tn.test_name', 't_ti.grade', 'm_ucn.university_code_name')
                        ->leftJoin('m_oem_school as m_oem', 't_ti.oem_sch_code', 'm_oem.oem_sch_code')
                        ->leftJoin('m_test_name as m_tn', 't_ti.test_name_cd', 'm_tn.test_name_cd')
                        ->leftJoin('m_university_code_head as m_uch', 'm_uch.id', 't_ti.university_code_head_id')
                        ->leftJoin('m_university_code_name as m_ucn', 'm_uch.university_code_name_cd', 'm_ucn.university_code_name_cd')
                        ->when(array_key_exists('year', $conditions) && isset($conditions['year']), function ($query) use ($conditions) {
                            return $query->where('t_ti.fiscal_year', $conditions['year']);
                        })
                        ->when(array_key_exists('test_name', $conditions) && isset($conditions['test_name']), function ($query) use ($conditions) {
                            return $query->where('m_tn.test_name', 'like', '%'.trim($conditions['test_name']).'%');
                        })
                        ->orderBy('t_ti.oem_sch_code')
                        ->orderBy('t_ti.fiscal_year')
                        ->orderBy('t_ti.grade')
                        ->orderBy('m_tn.test_name')
                        //->toSql();
                        ->get();
                        
        return $testLists;
    }

    /**
     * 登録処理
     * @param array $testInfo テスト情報連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $testInfo)
    {
        DB::beginTransaction();
        try {
            // 登録処理
            $tTestInfo = $this->TTestInfo->newInstance();
            $tTestInfo->oem_sch_code = $testInfo['oem_sch_code'];
            $tTestInfo->fiscal_year = $testInfo['fiscal_year'];
            $tTestInfo->grade = $testInfo['grade'];
            $tTestInfo->test_name_cd = $testInfo['test_name_cd'];
            $tTestInfo->university_code_head_id = $testInfo['university_code_head_id'];
            $tTestInfo->remarks = $testInfo['remarks'];
            $tTestInfo->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            // システムエラー
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }

        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS')
                    ,'message'=>trans('message.SUCCESS_TESTINFO_REGISTER_DATA')
                    ,'id'=>$tTestInfo->id);
    }

    /**
     * 更新処理
     * @param array $testInfo テスト情報(連想配列)
     * @param int $id
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $testInfo, int $id)
    {
        DB::beginTransaction();
        try {
            $tTestInfo = $this->TTestInfo::findOrFail($id);
            // 更新情報情報セット
            $tTestInfo->university_code_head_id = $testInfo['university_code_head_id'];
            $tTestInfo->remarks = $testInfo['remarks'];
            $tTestInfo->setUpdatedAt($testInfo['updated_at']);
            $tTestInfo->save();

            DB::commit();
        } catch (ModelNotFoundException $ex) {
            // 排他エラー
            DB::rollBack();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_BUSINESS_ERROR'),'message'=>trans('message.TESTINFO_EXCLUSIVE_EXCEPTION'));
        } catch (ExclusionException $ex) {
            // 楽観ロックエラー
            DB::rollBack();
            return array('resultCode' => config('constants.RESULT_CODE_CONFLICT_ERROR'), 'message' =>$ex->getMessage());
        } catch (\Exception $ex) {
            // システムエラー
            DB::rollBack();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }
        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'),'message'=>trans('message.SUCCESS_TESTINFO_UPDATE_DATA'));
    }

    /**
     * テストID指定でのデータ削除処理
     * @param int $id
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function delete(int $id)
    {
        DB::beginTransaction();
        try {
            $tTestInfo = $this->TTestInfo::where('id', $id)->firstOrFail();
            // 削除処理
            $this->TTestInfo::destroy($tTestInfo->id);
            
            DB::commit();
        } catch (ModelNotFoundException $ex) {
            // 排他エラー
            DB::rollback();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_BUSINESS_ERROR'),'message'=>trans('message.TESTINFO_EXCLUSIVE_EXCEPTION'));
        } catch (\Exception $ex) {
            // システムエラー
            DB::rollback();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }
        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'),'message'=>trans('message.SUCCESS_TESTINFO_DELETE_DATA'));
    }

    /**
     * ID指定での検索処理( 修正・詳細画面用 ）
     * @param int $id
     * @return \App\Models\TTestInfo|null
     */
    public function getTestInfoById(int $id)
    {
        return $this->TTestInfo
            ->select(
                't_test_info.id',
                'm_oem_school.oem_sch_name as oem_sch_name',
                't_test_info.fiscal_year as fiscal_year',
                't_test_info.grade',
                'm_test_name.test_name as test_name',
                't_test_info.university_code_head_id as university_code_head_id',
                't_test_info.remarks as remarks',
                't_test_info.updated_at',
            )
            ->leftJoin('m_oem_school', 't_test_info.oem_sch_code', '=', 'm_oem_school.oem_sch_code')
            ->leftJoin('m_test_name', 't_test_info.test_name_cd', '=', 'm_test_name.test_name_cd')
            ->where('t_test_info.id', $id)
            ->first();
    }

    /**
     * 年度プルダウンリスト用意処理
     * @return array
     */
    public function generateYearsArray()
    {
        $currentYear = date('Y');
        $noOfFutureYears = config('constants.FUTURE_YEAR');
        $noOfPastYears = config('constants.PAST_YEAR');
        $years = [];

        // 年度未指定のoption
        $years[''] = "";
        
        // Add next year
        for ($i = 1; $i < $noOfFutureYears + 1; $i++) {
            $years[strval($currentYear + $i)] = $currentYear + $i;
        }

        // Add previous 4 years
        for ($i = 0; $i < $noOfPastYears + 1; $i++) {
            $years[strval($currentYear - $i)] = $currentYear - $i;
        }

        return $years;
    }

    /**
     * テスト名リスト取得
     * @return array
     */
    public function getTestInfo($OEMSchCode, $grade)
    {
        return $this->TTestInfo
            ->select(
                't_test_info.id',
                'm_test_name.test_name',
            )
            ->leftJoin('m_test_name', 't_test_info.test_name_cd', '=', 'm_test_name.test_name_cd')
            ->where('t_test_info.oem_sch_code', $OEMSchCode)
            ->where('t_test_info.grade', $grade)
            //->where('t_test_info.fiscal_year', DB::raw('(SELECT MAX(t_test_info.fiscal_year) FROM t_test_info)'))
            ->get();
    }

    /**
     * 【【大学コードヘッダー情報】大学コード検索画面用
     * @param string $testID
     * @return 大学コードヘッダー
     */
    public function getUnivCodeHead($testID)
    {
        return DB::table('t_test_info as t_test')
            ->select('t_test.university_code_head_id', 'm_univ.dept_subject_div')
            ->join('m_university_code_head as m_univ', 't_test.university_code_head_id', '=', 'm_univ.id')
            ->where('t_test.id', '=', $testID)
            ->first();
    }
}
