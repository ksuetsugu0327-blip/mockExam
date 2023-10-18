<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MTestcount extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_testcount';
    
    protected $fillable = [
        'year',
        'cnttest',
        'sch_code',
        'test_day'
    ];
        
}
