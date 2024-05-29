<?php

namespace App\Repositories\MUnivCodeHead;

use App\Repositories\MUnivCodeHead\MUniversityCodeHeadRepositoryInterface as MUniversityCodeHeadRepositoryInterface;
use App\Models\MUniversityCodeHead as MUniversityCodeHead;
use App\Exceptions\ExclusionException;
use Illuminate\Support\Facades\Log;

class MUniversityCodeHeadRepository implements MUniversityCodeHeadRepositoryInterface
{
    /**
     * 大学コードヘッドマスタ モデル
     * @var type model
     */
    private $MUnivCodeHead;
    
    /**
     * コンストラクタ
     * @param MUniversityCodeHead $mUnivCode  大学コードモデル
     */
    public function __construct(MUniversityCodeHead $mUnivCodeHead)
    {
        $this->MUnivCodeHead = $mUnivCodeHead;
    }

    public function createOrUpdateCodeHead($codeHeadArray, $mode)
    {
        try {
            if ($mode === "update") {
                $codeHead = $this->MUnivCodeHead::find($codeHeadArray['id']);
                // $codeHead->university_code_name_cd = $codeHeadArray['university_code_name_cd'];
                // $codeHead->fiscal_year = $codeHeadArray['fiscal_year'];
                // $codeHead->dept_subject_div = $codeHeadArray['dept_subject_div'];
                // $codeHead->updated_num += 1;

                // $codeHead->save();

                $codeHeadArray['updated_num'] = $codeHead->updated_num += 1;
                $this->save($codeHeadArray, $codeHead);

                return $codeHead;
            } else {
                $entry = $this->MUnivCodeHead::Create($codeHeadArray);
                return $entry;
            }

        } catch (QueryException $e) {
            // Handle database related exceptions
            Log::Warning('Database error: ' . $e->getMessage());
        } catch (ExclusionException $e){
            Log::Warning('Database error: ' . '大学コードヘッダーID: '.$codeHeadArray['id'].','.trans('message.CONFLICT_EXCEPTION.update'));
            throw new ExclusionException($e->getMessage());
        } catch (Exception $e) {
            // Handle general exceptions
            Log::Warning('Error: ' . $e->getMessage());
        }
        return $entry;
    }

    public function getCodeHeader($dataNameCode)
    {
       $mUnivCodeHeader = $this->MUnivCodeHead::
                                where('university_code_name_cd', $dataNameCode)->first();

       return $mUnivCodeHeader;
    }

    /**
     * 保存
     *
     * @param array $values
     * @param MUniversityCodeHead|null $mUnivCodeHead
     */
    private function save(array $values, MUniversityCodeHead $mUnivCodeHead=null)
    {
        $input = collect($values);
        $model = $mUnivCodeHead ?: $this->MUnivCodeHead->newInstance();

        $model->university_code_name_cd = $input->get('university_code_name_cd');
        $model->fiscal_year = $input->get('fiscal_year');
        $model->dept_subject_div = $input->get('dept_subject_div');
        $model->updated_num  = $input->get('updated_num');
        $model->setUpdatedAt($input->get('updated_at'));

        $model->save();
    }

    /**
     * 【テスト情報】大学コードマスタプルダウン用リスト
     * @param string $year
     * @return 大学コードリスト
     */
    public function selectList(string $year)
    {
        return $this->MUnivCodeHead::select(
            'm_university_code_head.id as id',
            'm_university_code_head.fiscal_year as fiscal_year',
            'm_university_code_name.university_code_name'
        )
        ->leftJoin('m_university_code_name', function ($join) {
            $join->on('m_university_code_head.university_code_name_cd', '=', 'm_university_code_name.university_code_name_cd')
                ->where('m_university_code_name.invalid_flag', '=', 0);
        })
        ->where('m_university_code_head.fiscal_year', '=', $year)
        ->orderby('m_university_code_head.id', 'asc') 
        ->get();
    }
}
