<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MHighschoolClass extends Model
{
    // Define the table name
    protected $table = 'm_highschool_class';

    // Define the primary key
    protected $primaryKey = 'class_cd';

    // Define if the primary key is auto-incrementing (optional)
    public $incrementing = false;

    // Define the data type of the primary key (optional)
    protected $keyType = 'string';

    // Define the columns you want to allow mass assignment (fillable)
    protected $fillable = [
        'class_name',
        'invalid_flag',
        'created_at',
        'updated_at',
    ];

    // Define relationships to other models (if applicable)
    // For example, if `MHighschoolClass` references other models, define those relationships using methods like `belongsTo` or `hasMany`.
    // Add any relationships here if needed.

    // Additional methods, scopes, or accessors/mutators if needed
}
