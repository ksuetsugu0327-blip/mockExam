<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TTicket extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 't_ticket';

    protected $fillable = [
        'id_person',
        'year',
        'cnttest',
        'grade',
        'exam_number',

        'c_univ_code1',
        'c_faculty_code1',
        'c_department_code1',

        'c_univ_code2',
        'c_faculty_code2',
        'c_department_code2',

        'c_univ_code3',
        'c_faculty_code3',
        'c_department_code3',

        'c_univ_code4',
        'c_faculty_code4',
        'c_department_code4',

        'c_univ_code5',
        'c_faculty_code5',
        'c_department_code5',

        'c_univ_code6',
        'c_faculty_code6',
        'c_department_code6'
    ];

    //受験番号のアクセサ：ゼロ埋めで返却
    public function getExamNumberAttribute($value) {
        if($value > 0 ){
            return str_pad($value, config('constants.EXAM_NUMBER_ZERO_PAD_DIGITS'), '0', STR_PAD_LEFT);
        }
        return '';
    }
}
