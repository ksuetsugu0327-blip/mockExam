<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TZipOrder extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 't_ziporder';

    protected $fillable = [
        'id',
        'category_kbn',
        'process_kbn',
        'year',
        'cnttest',
        'grade',
        'sch_code',
        'cls_code',
        'zipfilepath',
        'zipfilepass',
        'error_kbn',
        'category_at',
        'order_at',
        'download_at',
    ];
}
