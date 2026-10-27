<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MUniversity extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_university';

    protected $fillable = [
        'univ_code',
        'univ_name',
        'univ_kana',
        'inst_class',
        'pref_code',
    ];
}
