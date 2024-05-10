<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MTestName extends Model
{
    // Define the table name (optional if Laravel's default naming conventions match your table name)
    protected $table = 'm_test_name';

    // Define the primary key if it differs from 'id' (optional)
    protected $primaryKey = 'test_name_cd';

    // Define if the primary key is auto-incrementing (optional)
    public $incrementing = false;

    // Define the data type of the primary key (optional)
    protected $keyType = 'integer';

    // Define the columns you want to allow mass assignment (fillable)
    protected $fillable = [
        'test_name',
        'remarks',
        'invalid_flag',
        'created_at',
        'updated_at',
    ];

    // Define relationships to other models (if applicable)
    // For example, you might define relationships with other models if needed.

    // Additional methods, scopes, or accessors/mutators if needed
}
