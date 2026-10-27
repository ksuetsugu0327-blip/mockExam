<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\OptimisticLockObserverTrait;
use Illuminate\Database\Eloquent\Model;

class TTestInfo extends Model
{
    use OptimisticLockObserverTrait;

    // Define the table name (optional if Laravel's default naming conventions match your table name)
    protected $table = 't_test_info';

    // Define the primary key if it differs from 'id' (optional)
    protected $primaryKey = 'id';

    // Define if the primary key is auto-incrementing (optional)
    public $incrementing = true;

    // Define the data type of the primary key (optional)
    protected $keyType = 'bigint';

    // Define the columns you want to allow mass assignment (fillable)
    protected $fillable = [
        'oem_sch_code',
        'fiscal_year',
        'grade',
        'test_name_cd',
        'university_code_head_id',
        'remarks',
        'created_at',
        'updated_at',
    ];

    // Define relationships to other models (if applicable)
    // For example, `university_code_head_id` may reference another model such as `MUniversityCodeHead`
    public function universityCodeHead(): BelongsTo
    {
        return $this->belongsTo(MUniversityCodeHead::class, 'university_code_head_id');
    }

    // Additional methods, scopes, or accessors/mutators if needed
}
