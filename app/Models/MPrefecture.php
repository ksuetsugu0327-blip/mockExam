<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPrefecture extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_prefecture';
    
    protected $fillable = [
        'pref_code',
        'name'
    ];
}
