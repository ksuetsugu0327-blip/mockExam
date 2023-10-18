<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MDepartment extends Model
{
        /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_department';

    protected $fillable = [
        'univ_code',
        'fcl_code',
        'dpt_code',
        'dpt_name',
        'dpt_order'
    ];
}
