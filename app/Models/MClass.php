<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * 教室マスタのモデルクラス
 */
class MClass extends Authenticatable
{
    use HasFactory;

    use Notifiable;

    /*
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_class';

    protected $fillable = [
        'sch_code',
        'cls_code',
        'name',
        'password',
        'temppassword',
    ];

    /**
     * 配列からは非表示にする項目
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
}
