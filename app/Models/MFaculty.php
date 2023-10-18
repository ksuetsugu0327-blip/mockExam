<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MFaculty extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'm_faculty';
    
    protected $fillable = [
        'univ_code',
        'fcl_code',
        'fcl_name',
        'fcl_order',
        'faculty_enable',
        'department_enable'
    ];
}
