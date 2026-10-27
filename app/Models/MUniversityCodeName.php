<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MUniversityCodeName extends Model
{
    // The table associated with the model
    protected $table = 'm_university_code_name';

    // The primary key associated with the table
    protected $primaryKey = 'university_code_name_cd';

    // Indicates if the IDs are auto-incrementing
    public $incrementing = false;

    // The data type of the primary key
    protected $keyType = 'integer';

    // Indicates if the model should be timestamped
    public $timestamps = true;

    // The attributes that are mass assignable
    protected $fillable = [
        'university_code_name_cd',
        'university_code_name',
        'remarks',
        'invalid_flag',
        'created_at',
        'updated_at',
    ];

    //  // The attributes that should be cast to native types
    //  protected $casts = [
    //      'university_code_name_cd' => 'integer',
    //      'invalid_flag' => 'integer',
    //  ];
}
