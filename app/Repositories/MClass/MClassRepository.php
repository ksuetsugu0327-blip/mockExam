<?php

declare(strict_types=1);

namespace App\Repositories\MClass;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Repositories\MClass\MClassRepositoryInterface as MClassRepositoryInterface;
use App\Models\MClass as MClass;
use App\Models\MSchool as MSchool;
use App\Models\MPerson as MPerson;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 教室マスタ用リポジトリ
 *
 */
class MClassRepository implements MClassRepositoryInterface {

    /**
     * 塾モデル
     * @var type model
     */
    private $MSchool;

    /**
     * 教室モデル
     * @var type 
     */
    private $MClass;

    /**
     * 個人モデル
     * @var type 
     */
    private $MPerson;

    /**
     * コンストラクタ
     * @param MSchool $mSchool  塾モデル
     * @param MClass  $mClass   教室モデル
     * @param MPerson $mPerson  個人モデル
     */
    public function __construct(MSchool $mSchool, MClass $mClass, MPerson $mPerson) {
        $this->MSchool = $mSchool;
        $this->MClass = $mClass;
        $this->MPerson = $mPerson;
    }

    /**
     * 教室マスタキー検索
     * @param int $schCode  塾コード
     * @param int $clsCode  教室コード
     * @return type　MClass
     */
    public function selectOneByKey(int $schCode, int $clsCode) {
        return $this->MClass::where('sch_code', $schCode)
                        ->where('cls_code', $clsCode)
                        ->get();
    }

    /**
     * 教室マスタ 業務キーでの検索（塾マスタ、教室マスタの情報も取得）
     * @param int $schCode
     * @param int $clsCode
     */
    public function selectOneByKeyWithSch(int $schCode, int $clsCode) {
        // 教室マスタを取得するためのクエリ生成
        $query = $this->MClass::query();

        return $query->join('m_school', 'm_class.sch_code', '=', 'm_school.sch_code')
                         ->select('m_class.sch_code', 'm_class.cls_code', 'm_class.name as cls_name', 'm_class.password',
                                  'm_class.temppassword',
                                  'm_school.testname', 'm_school.name as sch_name',
                                  'm_school.psn_hide_flg', 'm_school.schname_hide_flg',
                                  'm_school.psncard_regist_flg',
                                  'm_school.cls_grade_csv_flg')
                         ->where('m_class.sch_code', '=', $schCode)
                         ->where('m_class.cls_code', '=', $clsCode)
                         ->get();
    }
    
    /**
     * 塾教室一覧表示用
     * @param int $schCode
     */
    public function selectClassBoxList(int $schCode) {
        // 教室マスタを取得するためのクエリ生成
        return $this->MClass::where('m_class.sch_code', '=', $schCode)
                         ->select('name','cls_code')
                         ->groupBy('name','cls_code')
                         ->get();
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param int $schCode      塾コード
     * @param string $schName   塾名
     * @param int $clsCode      教室コード
     * @param string $clsName   教室名
     */
    public function selectList(array $conditions) {
        // 塾マスタを取得するためのクエリ生成
        $query = $this->MClass::query();
        $query->join('m_school', 'm_class.sch_code', '=', 'm_school.sch_code')
              ->select('m_class.sch_code', 'm_school.name as sch_name', 'm_class.cls_code', 'm_class.name','m_class.temppassword');

        // 検索条件に設定されている場合、検索条件に追加
        if (array_key_exists('sch_code', $conditions) && isset($conditions['sch_code'])) {
            $query->where('m_class.sch_code', '=', $conditions['sch_code']);
        }
        // 塾名が存在する場合(部分一致)
        if (array_key_exists('sch_name', $conditions) && isset($conditions['sch_name'])) {
            $query->where('m_school.name', 'like', '%' . $conditions['sch_name'] . '%');
        }
        // 教室コード
        if (array_key_exists('cls_code', $conditions) && isset($conditions['cls_code'])) {
            $query->where('m_class.cls_code', '=', $conditions['cls_code']);
        }
        // 教室名が存在する場合(部分一致)
        if (array_key_exists('cls_name', $conditions) && isset($conditions['cls_name'])) {
            $query->where('m_class.name', 'like', '%' . $conditions['cls_name'] . '%');
        }
        $query->orderBy('m_class.sch_code')->orderBy('m_class.cls_code');
        // 表示結果を取得(ソート実施）
        return $query->get();
    }

    /**
     * 塾コード指定でのデータ削除処理
     * @param int $delClsCode
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException) 
     */
    public function delete(int $delSchCode, int $delClsCode) {
        
        DB::beginTransaction();
        try {
           $class = $this->MClass::where('cls_code', $delClsCode)->where('sch_code', $delSchCode)->firstOrFail();
           // 削除処理
           $this->MClass::destroy($class->id);

           // 個人レコード削除
            $deletePersons = $this->MPerson::where('sch_code',$delSchCode)->where('cls_code',$delClsCode)->delete();
            
           DB::commit();
        } catch (ModelNotFoundException $ex) {
        // 排他エラー
           DB::rollback();
           report($ex);
           return array('resultCode' => config('constants.RESULT_CODE_BUSINESS_ERROR'), 'message' => trans('message.MCLASS_EXCLUSIVE_EXCEPTION', ['sch_code'=>$delSchCode,'cls_code' => $delClsCode]));
        } catch (Exception $ex) {
        // システムエラー
           DB::rollback();
           report($ex);
           return array('resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex);
        }
        return array('resultCode' => config('constants.RESULT_CODE_SUCCESS'), 'message' => trans('message.SUCCESS_MCLASS_DELETE_DATA', ['sch_code'=>$delSchCode,'cls_code' => $delClsCode]));
    }

    /**
     * 登録処理
     * @param array $class 教室情報連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $class) {
        $mClass = $this->MClass->newInstance();
        // 塾コード

        $mClass->sch_code = $class['sch_code'];

        //教室コード
        $mClass->cls_code = $class['cls_code'];
        //教室名
        $mClass->name = $class['name'];
        // パスワード更新有無判定
        if (array_key_exists('password', $class)) {
            // パスワード(ハッシュ化)
            $mClass->password = Hash::make($class['password']);
        }
        // 仮パスワード発行判定
        if(array_key_exists('temppassword',$class)){
            // 仮パスワード
            $mClass->temppassword = $class['temppassword'];
        }
        try {
            // 登録処理
            $mClass->save();
        } catch (Exception $ex) {
            // システムエラー
            report($ex);
            return array('resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex);
        }

        return array('resultCode' => config('constants.RESULT_CODE_SUCCESS'),
            'message' => trans('message.SUCCESS_MCLASS_REGISTER_DATA', ['sch_code' => $class['sch_code'], 'cls_code' => $class['cls_code']])
            , 'id' => $mClass->id);
    }

    /**
     * 更新処理
     * @param array $class 塾情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $class) {
        //dd($class);
        try {
            $mClass = $this->MClass::findOrFail($class['id']);

            // 更新情報情報セット
            // 教室名
            if (session()->get('ADMIN_KBN') == 1) {
                $mClass->name = $class['cls_name'];
            }
            // パスワード更新有無判定
            if (array_key_exists('password', $class)) {
                // パスワード(ハッシュ化)
                $mClass->password = Hash::make($class['password']);
                $mClass->temppassword = null;
            }
            // 仮パスワード再発行判定
            if(array_key_exists('temppassword',$class)){
                // 本パスワード
                $mClass->password = null;
                // 仮パスワード
                $mClass->temppassword = $class['temppassword'];
            }
            // 塾マスタの更新
            $mClass->save();
        } catch (ModelNotFoundException $ex) {
            // 排他エラー
            report($ex);
            return array('resultCode' => config('constants.RESULT_CODE_BUSINESS_ERROR'), 'message' => trans('message.MCLASS_EXCLUSIVE_EXCEPTION', ['cls_code' => $class['cls_code']]));
        } catch (Exception $ex) {
            // システムエラー
            report($ex);
            return array('resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex);
        }
        return array('resultCode' => config('constants.RESULT_CODE_SUCCESS'), 'message' => trans('message.SUCCESS_MCLASS_UPDATE_DATA', ['cls_code' => $class['cls_code']]));
    }

    /**
     * 登録処理
     * @param array $classAry 個人用連想配列リスト
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray(array $classAry){

        try{
            DB::beginTransaction();

            foreach ($classAry as $class){

                // キーに合致するデータがあればUPDATE/なければINSERT
                $mClass = $this->MClass->updateOrCreate(
                    [
                        // キー
                        'sch_code' => $class['sch_code'],
                        'cls_code' =>  $class['cls_code']
                    ],

                    array_key_exists('password', $class ) && array_key_exists('temppassword', $class )
                        ? [
                        // 値
                        'name'         => $class['cls_name'],
                        'password'     => $class['password'],
                        'temppassword' => $class['temppassword']
                    ]
                        : [
                        // 値
                        'name'         => $class['cls_name'],
                    ]
                );

                // 登録
                $mClass->save();
            }
            DB::commit();
        } catch (Exception $ex) {
            // システムエラー
            DB::rollBack();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }

        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'),
            'message'=>trans('message.SUCCESS_MCLASS_REGISTER_DATA',['sch_code'=>$class['sch_code'],'cls_code'=>$class['cls_code']])
        ,'id'=>$mClass->id);

    }

    /**
     * 教室のカウント
     * @param $sch_code
     * @param $cls_code
     * @return mixed
     */
    public function countMClass($sch_code, $cls_code){
        return $this->MClass::where('sch_code',$sch_code)->where('cls_code',$cls_code)->count();
    }

    /**
     * 教室マスタ パスワード更新処理
     * @param int $schCode 塾コード
     * @param string $psnCode 個人コード
     * @param string $password パスワード
     * @return int id 更新データのid
     */
    public function registerPassword(int $schCode, string $clsCode, string $password) {
        return \DB::transaction(
            function() use ($schCode,$clsCode,$password){
                $updClass = MClass::where('sch_code',$schCode)->where('cls_code',$clsCode)->first();
                $updClass->password = Hash::make($password);
                $updClass->temppassword = null;
                $updClass->save();
                return $updClass->id;
            }
        );
    }
}
