<?php

namespace App\Models;

use App\Traits\OptimisticLockObserverTrait;
use Illuminate\Database\Eloquent\Model;

class MUniversityCodeHead extends Model
{
    use OptimisticLockObserverTrait;

    // Define the table name if it does not match the model name (optional)
    protected $table = 'm_university_code_head';

    // Define the primary key (if different from 'id')
    protected $primaryKey = 'id';

    // Indicate if the IDs are auto-incrementing (optional)
    public $incrementing = true;

    // Define the data type of the primary key (optional)
    protected $keyType = 'bigint';

    // Disable timestamps if you do not want Laravel to manage `created_at` and `updated_at`
    // You can leave this out because Laravel handles timestamps by default
    // public $timestamps = false;

    // Define the fillable attributes (columns you want to allow mass assignment)
    protected $fillable = [
        'university_code_name_cd',
        'fiscal_year',
        'dept_subject_div',
        'updated_num',
        'created_at',
        'updated_at',
    ];

    // Define relationships, accessors, and mutators as needed
}
