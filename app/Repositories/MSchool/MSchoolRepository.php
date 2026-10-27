<?php

declare(strict_types=1);

namespace App\Repositories\MSchool;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Models\MClass;
use App\Models\MPerson;
use App\Models\MSchool;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * 塾マスタ用リポジトリ
 */
class MSchoolRepository implements MSchoolRepositoryInterface
{
    /**
     * 塾モデル
     *
     * @var type model
     */
    private $MSchool;

    /**
     * 教室モデル
     *
     * @var type
     */
    private $MClass;

    /**
     * 個人モデル
     *
     * @var type
     */
    private $MPerson;

    /**
     * コンストラクタ
     *
     * @param  MSchool  $mSchool  塾モデル
     * @param  MClass  $mClass  教室モデル
     * @param  MPerson  $mPerson  個人モデル
     */
    public function __construct(MSchool $mSchool, MClass $mClass, MPerson $mPerson)
    {
        $this->MSchool = $mSchool;
        $this->MClass = $mClass;
        $this->MPerson = $mPerson;
    }

    /**
     * 塾マスタ業務キー検索
     *
     * @return type　MSchool
     */
    public function selectOneByKey(int $schCode)
    {
        return $this->MSchool::where('sch_code', $schCode)->get();
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     *
     * @param  array  $conditions  検索条件(連想配列）
     */
    public function selectList(array $conditions)
    {
        // 塾マスタを取得するためのクエリ生成
        $query = $this->MSchool::query();

        // 検索条件に塾コードが存在する場合(完全一致)
        if (array_key_exists('sch_code', $conditions) && isset($conditions['sch_code'])) {
            $query->where('sch_code', '=', $conditions['sch_code']);
        }
        // 塾名が存在する場合(部分一致)
        if (array_key_exists('sch_name', $conditions) && isset($conditions['sch_name'])) {
            $query->where('name', 'like', '%'.$conditions['sch_name'].'%');
        }

        // 表示結果を取得(ソート実施）
        return $query->get()->sortBy('sch_code')->values();

    }

    /**
     * 塾コード指定でのデータ削除処理
     *
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function delete(int $delSchCode)
    {
        DB::beginTransaction();
        try {
            $school = $this->MSchool::where('sch_code', $delSchCode)->firstOrFail();
            // 削除処理
            $this->MSchool::destroy($school->id);

            // 教室レコード削除
            $deleteClasses = $this->MClass::where('sch_code', $delSchCode)->delete();

            // 個人レコード削除
            $deletePersons = $this->MPerson::where('sch_code', $delSchCode)->delete();

            DB::commit();
        } catch (ModelNotFoundException $ex) {
            // 排他エラー
            DB::rollback();
            report($ex);

            return ['resultCode' => config('constants.RESULT_CODE_BUSINESS_ERROR'), 'message' => trans('message.MSCHOOL_EXCLUSIVE_EXCEPTION', ['sch_code' => $delSchCode])];
        } catch (Exception $ex) {
            // システムエラー
            DB::rollback();
            report($ex);

            return ['resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex];
        }

        return ['resultCode' => config('constants.RESULT_CODE_SUCCESS'), 'message' => trans('message.SUCCESS_MSCHOOL_DELETE_DATA', ['sch_code' => $delSchCode])];
    }

    /**
     * 登録処理
     *
     * @param  array  $school  塾情報連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $school)
    {
        $mSchool = $this->MSchool->newInstance();
        // 塾コード
        $mSchool->sch_code = $school['sch_code'];
        // 塾名
        $mSchool->name = $school['name'];
        // 塾アカウント名
        $mSchool->portal_account = $school['portal_account'];
        // 回数コンバート区分
        $mSchool->cntconvert_kbn = $school['cntconvert_kbn'];
        // テスト回数表示区分
        $mSchool->cnttest_kbn = $school['cnttest_kbn'];
        // テスト名
        $mSchool->testname = $school['testname'];
        // 管理ユーザー区分
        $mSchool->admin_kbn = $school['admin_kbn'];
        // 生徒機能非表示フラグ
        $mSchool->psn_hide_flg = $school['psn_hide_flg'];
        // 塾名非表示フラグ
        $mSchool->schname_hide_flg = $school['schname_hide_flg'];
        // 個人登録票利用フラグ
        $mSchool->psncard_regist_flg = $school['psncard_regist_flg'];
        // 生徒パスワード変更可能フラグ
        $mSchool->psn_psw_update_flg = $school['psn_psw_update_flg'];
        // 教室成績データCSVデータダウンロード可能フラグ
        $mSchool->cls_grade_csv_flg = $school['cls_grade_csv_flg'];
        // アクセス許可IP
        $mSchool->permit_ip = $school['permit_ip'];
        // パスワード(ハッシュ化)
        $mSchool->password = Hash::make($school['password']);
        try {
            // 登録処理
            $mSchool->save();
        } catch (Exception $ex) {
            // システムエラー
            return ['resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex];
        }

        return ['resultCode' => config('constants.RESULT_CODE_SUCCESS'),
            'message' => trans('message.SUCCESS_MSCHOOL_REGISTER_DATA', ['sch_code' => $school['sch_code']]), 'id' => $mSchool->id];
    }

    /**
     * 更新処理
     *
     * @param  array  $school  塾情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $school)
    {
        try {
            $mSchool = $this->MSchool::findOrFail($school['id']);
            // 更新情報情報セット
            // 塾名
            $mSchool->name = $school['name'];
            // 塾アカウント名
            $mSchool->portal_account = $school['portal_account'];
            // 回数コンバート区分
            $mSchool->cntconvert_kbn = $school['cntconvert_kbn'];
            // テスト回数表示区分
            $mSchool->cnttest_kbn = $school['cnttest_kbn'];
            // テスト名
            $mSchool->testname = $school['testname'];
            // 管理ユーザー区分
            $mSchool->admin_kbn = $school['admin_kbn'];
            // 生徒機能非表示フラグ
            $mSchool->psn_hide_flg = $school['psn_hide_flg'];
            // 塾名非表示フラグ
            $mSchool->schname_hide_flg = $school['schname_hide_flg'];
            // 個人登録票利用フラグ
            $mSchool->psncard_regist_flg = $school['psncard_regist_flg'];
            // 生徒パスワード変更可能フラグ
            $mSchool->psn_psw_update_flg = $school['psn_psw_update_flg'];
            // 教室成績データCSVデータダウンロード可能フラグ
            $mSchool->cls_grade_csv_flg = $school['cls_grade_csv_flg'];
            // 個人成績表公開機能利用フラグ
            $mSchool->psn_report_publish_flg = $school['psn_report_publish_flg'];
            // アクセス許可IP
            $mSchool->permit_ip = $school['permit_ip'];
            // パスワード更新有無判定
            if (array_key_exists('password', $school)) {
                // パスワード(ハッシュ化)
                $mSchool->password = Hash::make($school['password']);
            }
            // 塾マスタの更新
            $mSchool->save();

        } catch (ModelNotFoundException $ex) {
            // 排他エラー
            report($ex);

            return ['resultCode' => config('constants.RESULT_CODE_BUSINESS_ERROR'), 'message' => trans('message.MSCHOOL_EXCLUSIVE_EXCEPTION', ['sch_code' => $school['sch_code']])];
        } catch (Exception $ex) {
            // システムエラー
            report($ex);

            return ['resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex];
        }

        return ['resultCode' => config('constants.RESULT_CODE_SUCCESS'), 'message' => trans('message.SUCCESS_MSCHOOL_UPDATE_DATA', ['sch_code' => $school['sch_code']])];
    }

    public function findBySchAccount($portal_account)
    {
        return $this->MSchool::where('portal_account', $portal_account)->firstOrFail();
    }
}
