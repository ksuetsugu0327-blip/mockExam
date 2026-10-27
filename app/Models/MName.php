<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 名称マスタのモデルクラス
 *
 *  */
class MName extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_name';

    public $primaryKey = 'name_code';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'name_kbn',
        'name_code',
        'name',
    ];
}
