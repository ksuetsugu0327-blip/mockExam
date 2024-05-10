<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MHighschoolInstall extends Model
{
    // Define the table name
    protected $table = 'm_highschool_install';

    // Define the primary keys as an array
    protected $primaryKey = ['instal_cd1', 'instal_cd2'];

    // Disable incrementing because the primary keys are not auto-incrementing
    public $incrementing = false;

    // Define the data types of the primary keys
    protected $keyType = 'string';

    // Define the columns you want to allow mass assignment (fillable)
    protected $fillable = [
        'instal_name1',
        'instal_name2',
        'invalid_flag',
        'created_at',
        'updated_at',
    ];

    // Define relationships to other models (if applicable)
    // For example, relationships with other tables can be defined here.

    // Additional methods, scopes, or accessors/mutators if needed
}
