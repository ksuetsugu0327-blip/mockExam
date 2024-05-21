<?php

namespace App\Observers;


use App\Exceptions\ExclusionException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OptimisticLockObserver
{
    /**
     * 楽観的ロックチェック用の一時プロパティ
     */
    const OPTIMISTIC_LOCK_CHECK_COLUMN = 'updated_at';


    /**
     * INSERT前
     *
     * @param Model $model
     */
    public function creating(Model $model){
        if (!$this->checkPropExists($model)) return;

        $this->unsetOptimisticLockColumn($model);
        $model->updated_at = Carbon::now();
    }

    /**
     * UPDATE前
     *
     * @param Model $model
     */
    public function updating(Model $model){

        if (!$this->checkPropExists($model)) return;

        $this->check($model, 'update');
        $model->updated_at = Carbon::now();
    }

    /**
     * 論理削除前
     *
     * @param Model $model
     */
    public function deleting(Model $model){
        if (!$this->checkPropExists($model)) return;

        $this->check($model, 'delete');
        $model->updated_at = Carbon::now();
    }

    /**
     * 物理削除前
     *
     * @param Model $model
     */
    public function restoring(Model $model){
        if (!$this->checkPropExists($model)) return;

        $this->check($model, 'delete');
        $model->updated_at = Carbon::now();
    }

    /**
     * 楽観的ロックチェック
     *
     * @param Model $model
     */
    private function check(Model $model, $msgType){
        //楽観的ロックチェック用の一時プロパティがあった時だけチェックする
        if (!$this->checkPropExists($model)) return;

        //現時点でのDBのデータを取得
        $currentMe = $model->find($model->id);      
        $currentEditedAt = $currentMe->updated_at;
        //更新されているかチェック
        if($model->{self::OPTIMISTIC_LOCK_CHECK_COLUMN} != $currentEditedAt){
            //楽観ロック
            throw new ExclusionException(trans('message.CSV_MUNIVCODE_UPDATE_EXCLUSION.'.$msgType));
        }

        $this->unsetOptimisticLockColumn($model);
    }

    /**
     * 楽観的ロック用の一時プロパティを削除する
     * これをしないと存在しないカラムに値を登録しようとしてsave()時にエラーになる。
     *
     * @param Model $model
     */
    private function unsetOptimisticLockColumn(Model $model){
        unset($model->{self::OPTIMISTIC_LOCK_CHECK_COLUMN});
    }

    /**
     * 楽観的ロック用の一時プロパティがあるかチェックする
     *
     * @param Model $model
     * @return bool true:ある
     */
    private function checkPropExists(Model $model){
        return array_key_exists(self::OPTIMISTIC_LOCK_CHECK_COLUMN, $model->getAttributes());
    }
}
