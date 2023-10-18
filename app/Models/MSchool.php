<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * 塾マスタのモデルクラス
 * 
 *  */
class MSchool  extends Authenticatable
{
    
    use Notifiable;
    
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_school';
    
    protected $fillable = [
        'sch_code',
        'name',
        'portal_account',
        'cntconvert_kbn',
        'cnttest_kbn',
        'testname',
        'password',
        'admin_kbn',
        'psn_hide_flg',
        'schname_hide_flg',
        'psncard_regist_flg',
        'permit_ip'
    ];

    /**
     * 配列からは非表示にする項目
     * @var array
     */
    protected $hidden = [
        'password',
    ];
}
