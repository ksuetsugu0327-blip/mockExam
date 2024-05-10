<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MOemSchool extends Model
{
    // Define the table name (optional if Laravel's default naming conventions match your table name)
    protected $table = 'm_oem_school';

    // Define the primary key if it differs from 'id' (optional)
    protected $primaryKey = 'oem_sch_code';

    // Define if the primary key is auto-incrementing (optional)
    public $incrementing = false;

    // Define the data type of the primary key (optional)
    protected $keyType = 'bigint';

    // Define the columns you want to allow mass assignment (fillable)
    protected $fillable = [
        'oem_sch_name',
        'remarks',
        'invalid_flag',
        'created_at',
        'updated_at',
    ];

    // Define relationships to other models (if applicable)
    // For example, if `MOemSchool` references another model, define relationships using methods like `belongsTo` or `hasMany`.
    // Add any relationships here if needed.

    // Additional methods, scopes, or accessors/mutators if needed
}
