<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * 塾マスタのモデルクラス
 *
 *  */
class MSchool extends Authenticatable
{
    use HasFactory;

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
        'psn_psw_update_flg',
        'cls_grade_csv_flg',
        'psn_report_publish_flg',
        'permit_ip',
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
