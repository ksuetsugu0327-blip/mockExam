<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Academy extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'm_academy';

    protected $fillable = [
        'academy_code',
        'academy_name',
        'number_com_times',
        'number_disp_times',
        'dl_password',
        'academy_pass',
    ];
}
