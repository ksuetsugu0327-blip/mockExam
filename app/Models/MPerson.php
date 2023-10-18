<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class MPerson extends Authenticatable
{
    
    use Notifiable;
    
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_person';
    
    protected $fillable = [
        'sch_code',
        'psn_code',
        'cls_code',
        'name',
        'birthlow4',
        'postallow4',
        'tellow4',
        'password',
        'temppassword',
        'hsc_code'
    ];
    
    /**
     * 配列からは非表示にする項目
     * @var array
     */
    protected $hidden = [
        'password',
    ];
}
