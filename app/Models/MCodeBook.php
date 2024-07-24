<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCodeBook extends Model
{
    // Define the table name (optional if Laravel's default naming conventions match your table name)
    protected $table = 'm_codebook';

    // Define the primary key if it differs from 'id' (optional)
    protected $primaryKey = 'codebook_cd';

    // Define if the primary key is auto-incrementing (optional)
    public $incrementing = false;

    // Define the data type of the primary key (optional)
    protected $keyType = 'varchar';

    // Define the columns you want to allow mass assignment (fillable)
    protected $fillable = [
        'codebook_name',
        'codebook_file_name',
        'remarks',
        'display_seq',
        'invalid_flag',
        'created_at',
        'updated_at',
    ];
}
