<?php

namespace App\Services;

use App\Models\MUniversityCodeHead as MUnivCodeHead;

class UnivCodeHeadService{

    /**
     * 保存
     *
     * @param array $values
     * @param MUnivCodeHead|null $mUnivCodeHead
     */
    public function save(array $values, MUnivCodeHead $mUnivCodeHead=null)
    {
        $input = collect($values);
        $model = $mUnivCodeHead ?: new MUnivCodeHead();

        $model->university_code_name_cd = $input->get('university_code_name_cd');
        $model->fiscal_year = $input->get('fiscal_year');
        $model->dept_subject_div = $input->get('dept_subject_div');
        $model->updated_num  = $input->get('updated_num');
        $model->setUpdatedAt($input->get('updated_at'));

        $model->save();
    }
}
