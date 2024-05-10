<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MUniversityInstall extends Model
{
    // Define the table name
    protected $table = 'm_university_install';

    // Define the primary key
    protected $primaryKey = 'install_cd';

    // Define if the primary key is auto-incrementing (optional)
    public $incrementing = false;

    // Define the data type of the primary key
    protected $keyType = 'string';

    // Define the columns you want to allow mass assignment (fillable)
    protected $fillable = [
        'install_name',
        'invalid_flag',
        'created_at',
        'updated_at',
    ];

    // Define relationships to other models (if applicable)
    // For example, if `MUniversityInstall` references other models, define relationships using methods like `belongsTo` or `hasMany`.
    // Add any relationships here if needed.

    // Additional methods, scopes, or accessors/mutators if needed
}

