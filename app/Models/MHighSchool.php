<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MHighSchool extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_highschool';

    protected $fillable = [
        'pref_code',
        'hsc_code',
        'hsc_name',
    ];
}
