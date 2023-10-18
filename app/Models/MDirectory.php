<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MDirectory extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_directory';
    
    protected $fillable = [
        'id',
        'year',
        'cnttest',
        'grade',
        'path_kbn',
        'category_kbn',
        'sch_code',
        'cls_code',
        'psn_code',
        'fullpath'
    ];
        
}
