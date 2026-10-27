<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MHighSchoolCode extends Model
{
    // Define the table name (optional if Laravel's default naming conventions match your table name)
    protected $table = 'm_highschool_code';

    // Define the primary key if it differs from 'id' (optional)
    protected $primaryKey = 'id';

    // Define if the primary key is auto-incrementing (optional)
    public $incrementing = true;

    // Define the data type of the primary key (optional)
    protected $keyType = 'bigint';

    // Define the columns you want to allow mass assignment (fillable)
    protected $fillable = [
        'highschool_code',
        'highschool_name_hkn',
        'zip_num',
        'tel_num',
        'install_cd',
        'class_cd',
        'highschool_name',
        'address',
        'prefecture_cd',
        'created_at',
        'updated_at',
    ];

    // Define relationships to other models (if applicable)
    // Define relationships with other tables as necessary, e.g., `Prefecture` model
    // For example, if `MHighschoolCode` references a `Prefecture` model:
    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class, 'prefecture_cd');
    }

    // Additional methods, scopes, or accessors/mutators if needed
}
