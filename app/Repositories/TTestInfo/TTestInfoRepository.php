<?php
declare(strict_types=1);

namespace App\Repositories\TTestInfo;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Repositories\TTestInfo\TTestInfoRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\TTestInfo as TTestInfo;

/**
 * 大学模擬試験テーブル用リポジトリ
 *
 */
class TTestInfoRepository implements TTestInfoRepositoryInterface{

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
    public function selectList(array $conditions){
        $testLists = DB::table('t_test_info as t_ti')
                        ->select('t_ti.fiscal_year', 'm_tn.test_name', 't_ti.grade','m_ucn.university_code_name')
                        ->leftJoin('m_test_name as m_tn', 't_ti.test_name_cd','m_tn.test_name_cd')
                        ->leftJoin('m_university_code_head as m_uch', 'm_uch.id', 't_ti.university_code_head_id')
                        ->leftJoin('m_university_code_name as m_ucn', 'm_uch.university_code_name_cd', 'm_ucn.university_code_name_cd')
                        ->when(array_key_exists('year',$conditions) && isset($conditions['year']), function ($query) use ($conditions) {
                            return $query->where('t_ti.fiscal_year', $conditions['year']);
                        })
                        ->when(array_key_exists('test_name',$conditions) && isset($conditions['test_name']), function ($query) use ($conditions) {
                            return $query->where('m_tn.test_name', 'like', '%'.trim($conditions['test_name']).'%');
                        })
                        ->orderBy('t_ti.fiscal_year','DESC')
                        ->orderBy('t_ti.grade','DESC')
                        ->orderBy('m_tn.test_name', 'ASC')
                        //->toSql();
                        ->get();
                        
        return $testLists;
    }

    /**
     * 登録処理
     * @param array $testInfo 塾情報連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $testInfo) {
        $tTestInfo = $this->TTestInfo->newInstance();
        $tTestInfo->oem_sch_code = $testInfo['oem_sch_code'];
        $tTestInfo->fiscal_year = $testInfo['fiscal_year'];
        $tTestInfo->grade = $testInfo['grade'];
        $tTestInfo->test_name_cd = $testInfo['test_name_cd'];
        $tTestInfo->university_code_head_id = $testInfo['university_code_head_id'];
        $tTestInfo->highschool_cls = $testInfo['highschool_cls'];
        try{
            // 登録処理
            $tTestInfo->save();            
        } catch (Exception $ex) {
            // システムエラー
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }
        
        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS')
                    ,'message'=>trans('message.SUCCESS_TESTINFO_REGISTER_DATA')
                    ,'id'=>$tTestInfo->id);       
    }
}