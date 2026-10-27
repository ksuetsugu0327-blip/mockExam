<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MUniversityCode extends Model
{
    // Define the table name (optional if Laravel's default naming conventions match your table name)
    protected $table = 'm_university_code';

    // Define the primary key if it differs from 'id' (optional)
    protected $primaryKey = 'id';

    // Define if the primary key is auto-incrementing (optional)
    public $incrementing = true;

    // Define the data type of the primary key (optional)
    protected $keyType = 'bigint';

    // Define the columns you want to allow mass assignment (fillable)
    protected $fillable = [
        'university_code_head_id',
        'university_cd',
        'prefecture_cd',
        'install_div',
        'dept_line_cd1',
        'dept_line_cd2',
        'dept_line_cd3',
        'dept_line_cd4',
        'dept_line_cd5',
        'dept_line_cd6',
        'dept_line_cd7',
        'university_name_hkn',
        'university_name',
        'university_name_new',
        'department_cd',
        'department_seq',
        'department_name',
        'subject_cd',
        'subject_seq',
        'subject_name',
        'search_subject_name',
        'common_test_use',
        'deviation_value',
        'created_at',
        'updated_at',
    ];

    // Define relationships to other models (if applicable)
    // For example, if `MUniversityCode` is related to `MUniversityCodeHead` through `university_code_head_id`
    public function universityCodeHead(): BelongsTo
    {
        return $this->belongsTo(MUniversityCodeHead::class, 'university_code_head_id');
    }
}
