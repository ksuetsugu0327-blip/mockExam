<?php

namespace App\Repositories\MUnivCodeHead;

use App\Exceptions\ExclusionException;
use App\Models\MUniversityCode;
use App\Models\MUniversityCodeHead;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MUniversityCodeHeadRepository implements MUniversityCodeHeadRepositoryInterface
{
    /**
     * 大学コードヘッドマスタ モデル
     *
     * @var type model
     */
    private $MUnivCodeHead;

    private $MUnivCode;

    /**
     * コンストラクタ
     *
     * @param  MUniversityCodeHead  $mUnivCode  大学コードモデル
     */
    public function __construct(
        MUniversityCodeHead $mUnivCodeHead,
        MUniversityCode $mUnivCode
    ) {
        $this->MUnivCodeHead = $mUnivCodeHead;
        $this->MUnivCode = $mUnivCode;
    }

    public function createOrUpdateCodeHead($codeHeadArray, $mode)
    {
        try {
            if ($mode === 'update') {
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
            Log::Warning('Database error: '.$e->getMessage());
        } catch (ExclusionException $e) {
            Log::Warning('Database error: '.'大学コードヘッダーID: '.$codeHeadArray['id'].','.trans('message.CONFLICT_EXCEPTION.update'));
            throw new ExclusionException($e->getMessage());
        } catch (Exception $e) {
            // Handle general exceptions
            Log::Warning('Error: '.$e->getMessage());
        }

        return $entry;
    }

    public function getCodeHeader($param)
    {
        $mUnivCodeHeader = $this->MUnivCodeHead::where('fiscal_year', $param['year'])
            ->where('university_code_name_cd', $param['univCodeNameCode'])
            ->where('dept_subject_div', $param['deptOrSubject'])
            ->first();

        return $mUnivCodeHeader;
    }

    /**
     * 保存
     */
    private function save(array $values, ?MUniversityCodeHead $mUnivCodeHead = null)
    {
        $input = collect($values);
        $model = $mUnivCodeHead ?: $this->MUnivCodeHead->newInstance();

        $model->university_code_name_cd = $input->get('university_code_name_cd');
        $model->fiscal_year = $input->get('fiscal_year');
        $model->dept_subject_div = $input->get('dept_subject_div');
        $model->updated_num = $input->get('updated_num');
        $model->setUpdatedAt($input->get('updated_at'));

        $model->save();
    }

    /**
     * 【テスト情報】大学コードマスタプルダウン用リスト
     *
     * @return 大学コードリスト
     */
    public function selectList(string $year): 大学コードリスト
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
            ->orderby('m_university_code_name.display_seq', 'asc')
            ->orderby('m_university_code_head.university_code_name_cd', 'asc')
            ->get();
    }

    /**
     * 年度プルダウンリスト用意処理
     *
     * @return array
     */
    public function generateYearsArray(): array
    {
        $currentYear = date('Y');
        $noOfFutureYears = config('constants.FUTURE_YEAR');
        $noOfPastYears = config('constants.PAST_YEAR');
        $years = [];

        // Add next year
        for ($i = 1; $i < $noOfFutureYears + 1; $i++) {
            $years[strval($currentYear + $i)] = $currentYear + $i;
        }

        // Add previous 4 years
        for ($i = 0; $i < $noOfPastYears + 1; $i++) {
            $years[strval($currentYear - $i)] = $currentYear - $i;
        }

        return $years;
    }

    /**
     * 【大学コードデータ削除】一覧取得処理
     *
     * @param  array  $conditions  検索条件(連想配列）
     * @return 大学コードデータリスト
     */
    public function univCodeDataList(array $conditions): 大学コードデータリスト
    {
        return $this->MUnivCodeHead::select(
            'm_university_code_head.id as id',
            'm_university_code_head.fiscal_year as fiscal_year',
            'm_university_code_head.dept_subject_div as dept_subject_div',
            'm_university_code_name.university_code_name'
        )
            ->leftJoin('m_university_code_name', function ($join) {
                $join->on('m_university_code_head.university_code_name_cd', '=', 'm_university_code_name.university_code_name_cd')
                    ->where('m_university_code_name.invalid_flag', '=', 0);
            })
            ->when(array_key_exists('year', $conditions) && isset($conditions['year']), function ($query) use ($conditions) {
                return $query->where('m_university_code_head.fiscal_year', $conditions['year']);
            })
            ->when(array_key_exists('data_name', $conditions) && isset($conditions['data_name']), function ($query) use ($conditions) {
                return $query->where('m_university_code_name.university_code_name', 'like', '%'.trim($conditions['data_name']).'%');
            })
            ->when(array_key_exists('dept_subject_div', $conditions) && isset($conditions['dept_subject_div']), function ($query) use ($conditions) {
                return $query->where('m_university_code_head.dept_subject_div', $conditions['dept_subject_div']);
            })
            ->orderBy('m_university_code_head.fiscal_year')
            ->orderBy('m_university_code_name.university_code_name')
            ->get();
    }

    /**
     * 【大学コードデータ削除】一覧取得処理
     *
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function delete(int $id)
    {
        DB::beginTransaction();
        try {
            $mUnivCodeHead = $this->MUnivCodeHead::where('id', $id)->firstOrFail();

            // 削除処理
            $this->MUnivCode::where('university_code_head_id', $mUnivCodeHead->id)->delete();
            $mUnivCodeHead->delete();

            DB::commit();
        } catch (ModelNotFoundException $ex) {
            // 排他エラー
            DB::rollback();
            report($ex);

            return ['resultCode' => config('constants.RESULT_CODE_BUSINESS_ERROR'), 'message' => trans('message.UNIVCODEHEAD_EXCLUSIVE_EXCEPTION')];
        } catch (\Exception $ex) {
            // システムエラー
            DB::rollback();
            report($ex);

            return ['resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex];
        }

        return ['resultCode' => config('constants.RESULT_CODE_SUCCESS'), 'message' => trans('message.SUCCESS_UNIVCODEHEAD_DELETE_DATA')];
    }

    /**
     * 【大学コードデータ削除】一覧取得処理
     *
     * @param  int  $id
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function chkExistUnivCodeHead(int $fy, string $dtcd, int $dptsub)
    {
        return $this->MUnivCodeHead::select('id', 'updated_at')
            ->where('fiscal_year', $fy)
            ->where('university_code_name_cd', $dtcd)
            ->where('dept_subject_div', $dptsub)
            ->get();
    }
}
