<?php

namespace App\Repositories\MPerson;

use App\Repositories\MPerson\MPersonRepositoryInterface as MPersonRepositoryInterface;
use App\Models\MPerson as MPerson;
use Illuminate\Support\Facades\Hash as Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

/**
 * 個人マスタ用リポジトリ
 *
 */
class MPersonRepository implements MPersonRepositoryInterface{

    /**
     * 個人モデル
     * @var type model
     */
    private $MPerson;

    /**
     * コンストラクタ
     * @param MPerson $mPerson  個人モデル
     */
    public function __construct(MPerson $mPerson){
        $this->MPerson = $mPerson;
    }

    /**
     * 個人マスタ業務キー検索
     * @param int $schCode  塾コード
     * @param string $psnCode  個人コード
     * @return type　MPerson
     */
    public function selectOneByKey(int $schCode,string $psnCode){
        return $this->MPerson::where('sch_code',$schCode)
                             ->where('psn_code',$psnCode)
                             ->get();
    }

    /**
     * 個人マスタ 業務キーでの検索（塾マスタ、教室マスタの情報も取得）
     * @param int $schCode
     * @param string $psnCode
     */
    public function selectOneByKeyWithSchCls(int $schCode,string $psnCode){
        // 個人マスタを取得するためのクエリ生成
        $query = $this->MPerson::query();

        return $query->join('m_school','m_person.sch_code','=','m_school.sch_code')
                     ->join('m_class', function ($join) {
                        $join->on('m_person.sch_code', '=', 'm_class.sch_code')
                             ->on('m_person.cls_code', '=', 'm_class.cls_code');
                     })
                     ->select('m_person.id','m_person.sch_code','m_person.cls_code','m_person.psn_code','m_person.name as psn_name','m_person.birthlow4','m_person.postallow4',
                              'm_person.temppassword','m_person.tellow4','m_person.mail_address','m_school.name as sch_name','m_school.testname','m_class.name as cls_name',
                              'm_school.psn_hide_flg','m_school.schname_hide_flg',
                              'm_school.psncard_regist_flg','m_school.psn_psw_update_flg')
                     ->where('m_person.sch_code', '=', $schCode)
                     ->where('m_person.psn_code', '=', $psnCode)
                     ->get();
    }

    /**
     * 個人マスタ パスワード更新処理
     * @param int $schCode 塾コード
     * @param string $psnCode 個人コード
     * @param string $password パスワード
     * @return int id 更新データのid
     */
    public function registerPassword(int $schCode, string $psnCode, string $password) {
        return \DB::transaction(
                function() use ($schCode,$psnCode,$password){
                    $updUser = MPerson::where('sch_code',$schCode)->where('psn_code',$psnCode)->first();
                    $updUser->password = Hash::make($password);
                    $updUser->temppassword = null;
                    $updUser->save();
                    return $updUser->id;
                }
        );
    }

    /**
     * 個人マスタ 仮パスワード更新処理
     * @param int $schCode 塾コード
     * @param string $psnCode 個人コード
     * @param string $tempPass 仮パスワード
     * @return int id 更新データのid
     */
    public function registerTempPassword(int $schCode, string $psnCode, string $tempPass) {
        return \DB::transaction(
                function() use ($schCode,$psnCode,$tempPass){
                    $updUser = MPerson::where('sch_code',$schCode)->where('psn_code',$psnCode)->first();
                    $updUser->password = null;
                    $updUser->temppassword = $tempPass;
                    $updUser->save();
                    return $updUser->id;
                }
        );
    }

    /**
     * 個人マスタ メール更新処理
     * @param int $schCode 塾コード
     * @param string $psnCode 個人コード
     * @param string $email メールアドレス
     * @return int id 更新データのid
     */
    public function registerEmail(int $schCode, string $psnCode, string $email) {
        return \DB::transaction(
                function() use ($schCode,$psnCode,$email){
                    $updUser = MPerson::where('sch_code',$schCode)->where('psn_code',$psnCode)->first();
                    $updUser->mail_address = $email;
                    $updUser->save();
                    return $updUser->id;
                }
        );
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param array $conditions
     * @return $data            データコレクション(Eloque)
     */
    public function selectList(array $conditions){

        // 個人マスタを取得するためのクエリ生成
        $query = $this->MPerson::query();

        $query->join('m_school','m_person.sch_code','=','m_school.sch_code')
              ->join('m_class', function ($join) {
                $join->on('m_person.sch_code', '=', 'm_class.sch_code')
                     ->on('m_person.cls_code', '=', 'm_class.cls_code');
              })
              ->select('m_person.id','m_person.sch_code','m_person.cls_code','m_person.psn_code','m_person.name as psn_name','m_person.temppassword',
                        'm_school.name as sch_name','m_class.name as cls_name');

        // 検索条件に設定されている場合、検索条件に追加
        if (array_key_exists('sch_code',$conditions) && isset($conditions['sch_code'])) {
            $query->where('m_person.sch_code', '=', $conditions['sch_code']);
        }
        // 塾名が存在する場合(部分一致)
        if (array_key_exists('sch_name',$conditions) && isset($conditions['sch_name'])) {
            $query->where('m_school.name', 'like', '%' . $conditions['sch_name'] . '%');
        }
        // 教室コード
        if (array_key_exists('cls_code',$conditions) && isset($conditions['cls_code'])) {
            $query->where('m_person.cls_code', '=', $conditions['cls_code']);
        }
        // 教室名が存在する場合(部分一致)
        if (array_key_exists('cls_name',$conditions) && isset($conditions['cls_name'])) {
            $query->where('m_class.name', 'like', '%' . $conditions['cls_name'] . '%');
        }
        // 個人コード
        if (array_key_exists('psn_code',$conditions) && isset($conditions['psn_code'])) {
            $query->where('m_person.psn_code', '=', $conditions['psn_code']);
        }
        // 個人名称
        if (array_key_exists('psn_name',$conditions) && isset($conditions['psn_name'])) {
            $query->where('m_person.name', 'like', '%'.$conditions['psn_name'].'%');
        }
        $query->orderBy('sch_code')->orderBy('cls_code')->orderBy('psn_code');

        // 表示結果を取得
        return $query->get();

    }

    /**
     * 塾、個人コード指定での削除処理
     * @param int $delSchCode 削除対象塾コード
     * @param string $delPsnCode 削除対象個人コード
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function delete(int $delSchCode,string $delPsnCode){
        DB::beginTransaction();
        try{
            // 個人レコード削除
            $deletePersons = $this->MPerson::where('sch_code',$delSchCode)
                                  ->where('psn_code',$delPsnCode)
                                  ->delete();

            DB::commit();
        } catch (ModelNotFoundException $ex) {
            // 排他エラー
            DB::rollback();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_BUSINESS_ERROR'),'message'=>trans('message.MPERSON_EXCLUSIVE_EXCEPTION',['sch_code'=>$delSchCode,'psn_code'=>$delPsnCode]));
        } catch (Exception $ex){
            // システムエラー
            DB::rollback();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }
        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'),'message'=>trans('message.SUCCESS_MPERSON_DELETE_DATA',['sch_code'=>$delSchCode,'psn_code'=>$delPsnCode]));

    }

    /**
     * 登録処理
     * @param array $person 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function register(array $person){
        $mPerson = $this->MPerson->newInstance();
        // 塾コード
        $mPerson->sch_code = $person['sch_code'];
        // 個人コード
        $mPerson->psn_code = $person['psn_code'];
        // 教室コード
        $mPerson->cls_code = $person['cls_code'];
        // 氏名（漢字）
        $mPerson->name = $person['name'];
        // 生年月日（下4桁）
        $mPerson->birthlow4 = $person['birthlow4'];
        // 郵便番号（下4桁）
        $mPerson->postallow4 = $person['postallow4'];
        // 電話番号（下4桁）
        $mPerson->tellow4 = $person['tellow4'];
        // 本パスワード
        $mPerson->password = null;
        // 仮パスワード
        $mPerson->temppassword = $person['temppassword'];
        // メールアドレス
        if(!empty($person['mail_address'])){
            $mPerson->mail_address = $person['mail_address'];
        }

        try{
            // 登録処理
            $mPerson->save();
        } catch (Exception $ex) {
            // システムエラー
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }

        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'),
                        'message'=>trans('message.SUCCESS_MPERSON_REGISTER_DATA',['sch_code'=>$person['sch_code'],'psn_code'=>$person['psn_code']])
                        ,'id'=>$mPerson->id);

    }


    /**
     * 更新処理
     * @param array $person 個人情報(連想配列)
     * @param int $adminKbn 管理者区分
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(array $person,int $adminKbn){
        try{
            $mPerson = $this->MPerson::findOrFail($person['id']);
            // 更新情報情報セット

            if($adminKbn == config('constants.ADMIN_KBN_ADMIN')){
                // 教室コード
                $mPerson->cls_code = $person['cls_code'];
                // 氏名（漢字）
                $mPerson->name = $person['name'];
                // 生年月日（下4桁）
                $mPerson->birthlow4 = $person['birthlow4'];
                // 郵便番号（下4桁）
                $mPerson->postallow4 = $person['postallow4'];
                // 電話番号（下4桁）
                $mPerson->tellow4 = $person['tellow4'];
            }

            // メールアドレス更新有無判定
            if(array_key_exists('mail_address',$person)){
                $mPerson->mail_address = $person['mail_address'];
            }

            // パスワード更新有無判定
            if(array_key_exists('temppassword',$person)){
                // 本パスワード
                $mPerson->password = null;
                // 仮パスワード
                $mPerson->temppassword = $person['temppassword'];
            }
            // 個人マスタの更新
            $mPerson->save();

        } catch (ModelNotFoundException $ex) {
            // 排他エラー
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_BUSINESS_ERROR'),'message'=>trans('message.MPERSON_EXCLUSIVE_EXCEPTION',['sch_code'=>$person['sch_code'],'psn_code'=>$person['psn_code']]));
        } catch (Exception $ex){
            // システムエラー
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }
        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'),'message'=>trans('message.SUCCESS_MPERSON_UPDATE_DATA',['sch_code'=>$person['sch_code'],'psn_code'=>$person['psn_code']]));
    }

    /**
     * 登録処理
     * @param array $personAry 個人用連想配列リスト
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray(array $personAry){

        try{
            DB::beginTransaction();

            foreach ($personAry as $person){
//                $mPerson = $this->MPerson->newInstance();
//                // 塾コード
//                $mPerson->sch_code = $person['sch_code'];
//                // 個人コード
//                $mPerson->psn_code = $person['psn_code'];
//                // 教室コード
//                $mPerson->cls_code = $person['cls_code'];
//                // 氏名（漢字）
//                $mPerson->name = $person['name'];
//                // 生年月日（下4桁）
//                $mPerson->birthlow4 = $person['birthlow4'];
//                // 郵便番号（下4桁）
//                $mPerson->postallow4 = $person['postallow4'];
//                // 電話番号（下4桁）
//                $mPerson->tellow4 = $person['tellow4'];
//                // 本パスワード
//                $mPerson->password = null;
//                // 仮パスワード
//                $mPerson->temppassword = $person['temppassword'];
//                // 登録処理
//                $mPerson->save();

                // キーに合致するデータがあればUPDATE/なければINSERT
                $mPerson = $this->MPerson->updateOrCreate(
                        [
                            // キー
                            'sch_code' => $person['sch_code'],
                            'psn_code' => $person['psn_code']
                        ],

                        array_key_exists('password', $person ) && array_key_exists('temppassword', $person )
                        ? [
                            // 値
                            'cls_code'     => $person['cls_code'],
                            'name'         => $person['name'],
                            'birthlow4'    => $person['birthlow4'],
                            'postallow4'   => $person['postallow4'],
                            'tellow4'      => $person['tellow4'],
                            'password'     => $person['password'],
                            'temppassword' => $person['temppassword']
                          ]
                        : [
                            // 値
                            'cls_code'     => $person['cls_code'],
                            'name'         => $person['name'],
                            'birthlow4'    => $person['birthlow4'],
                            'postallow4'   => $person['postallow4'],
                            'tellow4'      => $person['tellow4'],
                        ]
                );

                // 登録
                $mPerson->save();
            }
            DB::commit();
        } catch (Exception $ex) {
            // システムエラー
            DB::rollBack();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }

        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'),
                        'message'=>trans('message.SUCCESS_MPERSON_REGISTER_DATA',['sch_code'=>$person['sch_code'],'psn_code'=>$person['psn_code']])
                        ,'id'=>$mPerson->id);

    }

    /**
     * 高校更新処理
     * @param array $person 個人用連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function updateHighSchool(array $person)
    {
        try{
            $mPerson = $this->MPerson::findOrFail($person['id']);

            // 高校コード
            $mPerson->hsc_code = $person['hsc_code'];

            // 個人マスタの更新
            $mPerson->save();
        } catch (ModelNotFoundException $ex) {
            // 排他エラー
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_BUSINESS_ERROR'),
                'message'=>trans('message.MPERSON_EXCLUSIVE_EXCEPTION', ['sch_code'=>$person['sch_code'],'psn_code'=>$person['psn_code']]));
        } catch (Exception $ex){
            // システムエラー
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message'=>$ex);
        }
        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'),
            'message'=>trans('message.SUCCESS_MPERSON_UPDATE_DATA', ['sch_code'=>$person['sch_code'],'psn_code'=>$person['psn_code']]));
    }

    /**
     * 生徒のカウント
     * @param $sch_code
     * @param $cls_code
     * @param $psn_code
     * @return mixed
     */
    public function countMPerson($sch_code, $cls_code, $psn_code){
        return $this->MPerson::where('sch_code',$sch_code)->where('cls_code',$cls_code)->where('psn_code',$psn_code)->count();
    }
}
