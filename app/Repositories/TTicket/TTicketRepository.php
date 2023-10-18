<?php

namespace App\Repositories\TTicket;

use App\Repositories\TTicket\TTicketRepositoryInterface as TTicketRepositoryInterface;
use App\Models\TTicket as TTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * 志望校リポジトリ
 *
 */
class TTicketRepository implements TTicketRepositoryInterface {
    /**
     * 志望校テーブルモデル
     * @var type model
     */
    private $TTicket;

    /**
     * コンストラクタ
     * @param TTicket $tTicket 志望校テーブルモデル
     */
    public function __construct(TTicket $tTicket) {
        $this->TTicket = $tTicket;
    }

    /**
     * 志望校リスト
     * @param array $conditions 検索条件
     * @return 志望校リスト
     */
    public function selectList(array $conditions) {
        $query = $this->TTicket::join('m_person', 't_ticket.id_person', '=', 'm_person.id')
            ->join('m_class', function ($join) {
                $join->on('m_person.sch_code', '=', 'm_class.sch_code')
                    ->on('m_person.cls_code', '=', 'm_class.cls_code');
            })
            ->leftJoin('m_university as u1', 't_ticket.c_univ_code1', '=', 'u1.univ_code')
            ->leftJoin('m_faculty as f1', function ($join) {
                $join->on('t_ticket.c_univ_code1', '=', 'f1.univ_code')
                    ->on('t_ticket.c_faculty_code1', '=', 'f1.fcl_code');
            })
            ->leftJoin('m_department as d1', function ($join) {
                $join->on('t_ticket.c_univ_code1', '=', 'd1.univ_code')
                    ->on('t_ticket.c_faculty_code1', '=', 'd1.fcl_code')
                    ->on('t_ticket.c_department_code1', '=', 'd1.dpt_code');
            })
            ->leftJoin('m_university as u2', 't_ticket.c_univ_code2', '=', 'u2.univ_code')
            ->leftJoin('m_faculty as f2', function ($join) {
                $join->on('t_ticket.c_univ_code2', '=', 'f2.univ_code')
                    ->on('t_ticket.c_faculty_code2', '=', 'f2.fcl_code');
            })
            ->leftJoin('m_department as d2', function ($join) {
                $join->on('t_ticket.c_univ_code2', '=', 'd2.univ_code')
                    ->on('t_ticket.c_faculty_code2', '=', 'd2.fcl_code')
                    ->on('t_ticket.c_department_code2', '=', 'd2.dpt_code');
            })
            ->leftJoin('m_university as u3', 't_ticket.c_univ_code3', '=', 'u3.univ_code')
            ->leftJoin('m_faculty as f3', function ($join) {
                $join->on('t_ticket.c_univ_code3', '=', 'f3.univ_code')
                    ->on('t_ticket.c_faculty_code3', '=', 'f3.fcl_code');
            })
            ->leftJoin('m_department as d3', function ($join) {
                $join->on('t_ticket.c_univ_code3', '=', 'd3.univ_code')
                    ->on('t_ticket.c_faculty_code3', '=', 'd3.fcl_code')
                    ->on('t_ticket.c_department_code3', '=', 'd3.dpt_code');
            })
            ->leftJoin('m_university as u4', 't_ticket.c_univ_code4', '=', 'u4.univ_code')
            ->leftJoin('m_faculty as f4', function ($join) {
                $join->on('t_ticket.c_univ_code4', '=', 'f4.univ_code')
                    ->on('t_ticket.c_faculty_code4', '=', 'f4.fcl_code');
            })
            ->leftJoin('m_department as d4', function ($join) {
                $join->on('t_ticket.c_univ_code4', '=', 'd4.univ_code')
                    ->on('t_ticket.c_faculty_code4', '=', 'd4.fcl_code')
                    ->on('t_ticket.c_department_code4', '=', 'd4.dpt_code');
            })
            ->leftJoin('m_university as u5', 't_ticket.c_univ_code5', '=', 'u5.univ_code')
            ->leftJoin('m_faculty as f5', function ($join) {
                $join->on('t_ticket.c_univ_code5', '=', 'f5.univ_code')
                    ->on('t_ticket.c_faculty_code5', '=', 'f5.fcl_code');
            })
            ->leftJoin('m_department as d5', function ($join) {
                $join->on('t_ticket.c_univ_code5', '=', 'd5.univ_code')
                    ->on('t_ticket.c_faculty_code5', '=', 'd5.fcl_code')
                    ->on('t_ticket.c_department_code5', '=', 'd5.dpt_code');
            })
            ->leftJoin('m_university as u6', 't_ticket.c_univ_code6', '=', 'u6.univ_code')
            ->leftJoin('m_faculty as f6', function ($join) {
                $join->on('t_ticket.c_univ_code6', '=', 'f6.univ_code')
                    ->on('t_ticket.c_faculty_code6', '=', 'f6.fcl_code');
            })
            ->leftJoin('m_department as d6', function ($join) {
                $join->on('t_ticket.c_univ_code6', '=', 'd6.univ_code')
                    ->on('t_ticket.c_faculty_code6', '=', 'd6.fcl_code')
                    ->on('t_ticket.c_department_code6', '=', 'd6.dpt_code');
            })
            ->leftJoin('m_highschool', 'm_person.hsc_code', '=', 'm_highschool.hsc_code');

        // 塾コードが設定されている場合、検索条件に追加
        if (array_key_exists('sch_code', $conditions) && !empty($conditions['sch_code'])) {
            $query->where('m_person.sch_code', '=', $conditions['sch_code']);
        }

        // 教室コードが設定されている場合、検索条件に追加
        if (array_key_exists('cls_code', $conditions) && !empty($conditions['cls_code'])) {
            $query->where('m_person.cls_code', '=', $conditions['cls_code']);
        }

        // 生徒コードが設定されている場合、検索条件に追加
        if (array_key_exists('psn_code', $conditions) && !empty($conditions['psn_code'])) {
            $query->where('m_person.psn_code', '=', $conditions['psn_code']);
        }

        // 年度が設定されている場合、検索条件に追加
        if (array_key_exists('year', $conditions) && !empty($conditions['year'])) {
            $query->where('t_ticket.year', '=', $conditions['year']);
        }

        // 回数が設定されている場合、検索条件に追加
        if (array_key_exists('cnttest', $conditions) && !empty($conditions['cnttest'])) {
            $query->where('t_ticket.cnttest', '=', $conditions['cnttest']);
        }

        // 学年が設定されている場合、検索条件に追加
        if (array_key_exists('grade', $conditions) && !empty($conditions['grade'])) {
            $query->where('t_ticket.grade', '=', $conditions['grade']);
        }

        $query->select(
            't_ticket.*',
            'm_person.sch_code',
            'm_person.cls_code',
            'm_class.name as cls_name',
            'm_person.psn_code',
            'm_person.name as psn_name',

            'u1.inst_class as c_inst_class1',
            'u1.pref_code as c_pref_code1',
            'f1.faculty_enable as c_faculty_enable1',
            'f1.department_enable as c_department_enable1',

            'u2.inst_class as c_inst_class2',
            'u2.pref_code as c_pref_code2',
            'f2.faculty_enable as c_faculty_enable2',
            'f2.department_enable as c_department_enable2',


            'u3.inst_class as c_inst_class3',
            'u3.pref_code as c_pref_code3',
            'f3.faculty_enable as c_faculty_enable3',
            'f3.department_enable as c_department_enable3',

            'u4.inst_class as c_inst_class4',
            'u4.pref_code as c_pref_code4',
            'f4.faculty_enable as c_faculty_enable4',
            'f4.department_enable as c_department_enable4',

            'u5.inst_class as c_inst_class5',
            'u5.pref_code as c_pref_code5',
            'f5.faculty_enable as c_faculty_enable5',
            'f5.department_enable as c_department_enable5',

            'u6.inst_class as c_inst_class6',
            'u6.pref_code as c_pref_code6',
            'f6.faculty_enable as c_faculty_enable6',
            'f6.department_enable as c_department_enable6',

            'm_person.hsc_code'
        )
            ->selectRaw(
                'CASE WHEN `m_highschool`.`hsc_name` IS NULL THEN `m_person`.`hsc_code`'
                . ' ELSE `m_highschool`.`hsc_name` END as `hsc_name`'
            );

        for ($i = 1; $i <= 6; $i++) {
            $query->selectRaw(
                'CASE WHEN `u' . $i
                . '`.`univ_name` IS NULL THEN `t_ticket`.`c_univ_code' . $i
                . '` ELSE `u' . $i
                . '`.`univ_name` END as `c_univ_name' . $i . '`'
            )
                ->selectRaw(
                    'CASE WHEN `f' . $i
                    . '`.`fcl_name` IS NULL THEN `t_ticket`.`c_faculty_code' . $i
                    . '` ELSE `f' . $i
                    . '`.`fcl_name` END as `c_faculty_name' . $i . '`'
                )
                ->selectRaw(
                    'CASE WHEN `d' . $i
                    . '`.`dpt_name` IS NULL THEN `t_ticket`.`c_department_code' . $i
                    . '` ELSE `d' . $i
                    . '`.`dpt_name` END as `c_department_name' . $i . '`'
                );
        }

        $query->orderby('sch_code')
            ->orderby('cls_code')
            ->orderby('psn_code')
            ->orderby('year')
            ->orderby('cnttest')
            ->orderby('grade');

        return $query->get();
    }

    /**
     * 登録or更新処理
     * @param array $choice 志望校情報連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function upsert(array $choice) {
        try {
            $this->TTicket::updateOrCreate(
                [
                    'id_person' => $choice['id_person'],
                    'year' => $choice['year'],
                    'cnttest' => $choice['cnttest'],
                    'grade' => $choice['grade'],
                ],
                [
                    'c_univ_code1' => $choice['c_univ_code1'],
                    'c_faculty_code1' => $choice['c_faculty_code1'],
                    'c_department_code1' => $choice['c_department_code1'],

                    'c_univ_code2' => $choice['c_univ_code2'],
                    'c_faculty_code2' => $choice['c_faculty_code2'],
                    'c_department_code2' => $choice['c_department_code2'],

                    'c_univ_code3' => $choice['c_univ_code3'],
                    'c_faculty_code3' => $choice['c_faculty_code3'],
                    'c_department_code3' => $choice['c_department_code3'],

                    'c_univ_code4' => $choice['c_univ_code4'],
                    'c_faculty_code4' => $choice['c_faculty_code4'],
                    'c_department_code4' => $choice['c_department_code4'],

                    'c_univ_code5' => $choice['c_univ_code5'],
                    'c_faculty_code5' => $choice['c_faculty_code5'],
                    'c_department_code5' => $choice['c_department_code5'],

                    'c_univ_code6' => $choice['c_univ_code6'],
                    'c_faculty_code6' => $choice['c_faculty_code6'],
                    'c_department_code6' => $choice['c_department_code6'],
                ]
            );
        } catch (Exception $ex) {
            // システムエラー
            report($ex);
            return array('resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex);
        }
        return array(
            'resultCode' => config('constants.RESULT_CODE_SUCCESS'),
            'message' => trans('message.SUCCESS_TICKET_REGISTER_DATA', ['testname' => $choice['testname']]));
    }

    /**
     * 削除処理
     * @param array $conditions 志望校情報条件連想配列
     * @return 連想配列(resultCode(-1(システムエラー) 0:(業務エラー) 1:正常終了)  message : メッセージ(システムエラーの場合はException)
     */
    public function delete(array $conditions) {
        try {
            $choice = $this->TTicket::where('id_person', $conditions['id_person'])
                ->where('year', $conditions['year'])
                ->where('cnttest', $conditions['cnttest'])
                ->where('grade', $conditions['grade'])
                ->first();
            // 削除処理
            if (!is_null($choice)) {
                $this->TTicket::destroy($choice->id);

            }
        } catch (Exception $ex) {
            // システムエラー
            report($ex);
            return array('resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex);
        }
        return array(
            'resultCode' => config('constants.RESULT_CODE_SUCCESS'),
            'message' => trans('message.SUCCESS_TICKET_REGISTER_DATA', ['testname' => $conditions['testname']]));
    }

    /**
     * 塾の年度回数一覧に表示する試験リストを取得
     * @param array $conditions
     * @return mixed
     */
    public function findSchExamData(array $conditions) {
        //ベースのクエリを作成
        $query = $this->setSchClsBaseQuery($conditions);
        return $this->getExamData($query);
    }

    /**
     * 教室の年度回数一覧に表示する試験リストを取得
     * @param array $conditions
     * @return mixed
     */
    public function findClsExamData(array $conditions) {
        //ベースのクエリを作成
        $query = $this->setSchClsBaseQuery($conditions);
        //クラスコードを条件に追加
        $query->where('m_person.cls_code', '=', $conditions['cls_code']);
        return $this->getExamData($query);
    }

    /**
     * 生徒の年度回数一覧に表示する試験リストを取得
     * @param array $conditions
     * @return mixed
     */
    public function findStudentExamData(array $conditions) {
        //ベースのクエリを作成
        $query = $this->setExamDataBaseQuery($conditions);

        //m_directoryを結合する
        $this->createExamDirectoryJoinClause($query);

        //取得カラム指定
        $query->select('t_ticket.year', 't_ticket.cnttest', 't_ticket.grade', 'm_directory.fullpath');

        //登録状況のフラグ：志望校大学名の入力有無で登録状況を判定する
        $query->selectRaw($this->createUnivRegisterCaseStmt());

        //返却状況のフラグ；ファイルフルパスの有無で返却状況を判定する
        $query->selectRaw($this->createReportFlagCaseStmt());

        //生徒用のWHERE句追加
        $query->where('m_person.psn_code', '=', $conditions['psn_code']);

        return $this->getExamData($query);
    }

    /**
     * 年度回数一覧に表示する試験リストのベースのクエリ
     * @param $conditions
     * @return mixed
     */
    private function setExamDataBaseQuery(array $conditions) {
        $query = $this->TTicket::join('m_person', 't_ticket.id_person', '=', 'm_person.id');
        //年度
        if (!is_null($conditions['year'])) {
            $query->where('t_ticket.year', '=', $conditions['year']);
        }
        //学年
        if (!is_null($conditions['grade'])) {
            $query->where('t_ticket.grade', '=', $conditions['grade']);
        }
        return $query->where('m_person.sch_code', '=', $conditions['sch_code']);
    }

    /**
     * 塾と教室の年度回数一覧に表示する試験リストのベースクエリ
     * @param $conditions
     * @return mixed
     */
    private function setSchClsBaseQuery($conditions) {
        $query = $this->setExamDataBaseQuery($conditions);
        //取得カラム指定
        $query->select('t_ticket.year', 't_ticket.cnttest', 't_ticket.grade');
        //グルーピング
        return $query->groupBy('t_ticket.year', 't_ticket.cnttest', 't_ticket.grade');
    }

    /**
     * 年度回数一覧に表示する試験リストを返却する
     * @param $query
     * @return mixed
     */
    private function getExamData($query) {
        //並べ替え：年度（降順）、回数（降順）、学年（昇順）
        return $query->orderby('t_ticket.year', 'desc')
            ->orderby('t_ticket.cnttest', 'desc')
            ->orderby('t_ticket.grade')
            ->get();
    }

    /**
     * m_directory結合のクエリ
     * @param $query
     */
    private function createExamDirectoryJoinClause($query) {
        $query->leftJoin('m_directory', function ($join) {
            $join->on('t_ticket.year', '=', 'm_directory.year')
                ->on('t_ticket.cnttest', '=', 'm_directory.cnttest')
                ->on('t_ticket.grade', '=', 'm_directory.grade')
                ->on('m_person.sch_code', '=', 'm_directory.sch_code')
                ->on('m_person.psn_code', '=', 'm_directory.psn_code')
                ->where('m_directory.path_kbn', '=', (string)config('constants.PATH_KBN_FILE'))
                ->where('m_directory.category_kbn', '=', (string)config('constants.FILE_KBN_PSN'));
        });
    }

    /**
     * 登録状況のフラグを設定するCASE式を生成する
     * @return string
     */
    private function createUnivRegisterCaseStmt() {
        $s_i = 1;
        for ($s_i; $s_i <= 6; $s_i++) {
            $stmt[] = 'WHEN t_ticket.c_univ_code' . $s_i . '  IS NOT NULL THEN 1';
        }
        return '(CASE ' . implode(' ', $stmt) . ' ELSE 0 END) AS choiceflag';
    }

    /**
     * 返却状況のフラグを設定するCASE式を生成する
     * @return string
     */
    private function createReportFlagCaseStmt() {
        return '(CASE WHEN m_directory.fullpath IS NOT NULL THEN 1 ELSE 0 END) AS reportflag';
    }

    /**
     * 登録処理
     * @param array $import_data 個人用配列リスト
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ id:登録時に採番したid)
     */
    public function registerArray(array $import_data) {
        try {

            DB::beginTransaction();

            $chunk_data = Collection::make($import_data);
            $chunk_data->chunk(1000)->each(function ($ch) {
                $insert_string = '';
                foreach ($ch as $ranking) {
                    $insert_string .= '(' . $ranking . '), ';
                }
                $insert_string = rtrim($insert_string, ', ');
                DB::insert("INSERT INTO t_ticket (`id_person`, `year`, `cnttest`, `grade`, `exam_number`, `created_at`, `updated_at`) 
                                    VALUES 
                                    $insert_string 
                                    ON DUPLICATE KEY UPDATE `exam_number`=VALUES(`exam_number`)");
                //auto incrementを調整
                $this->setAutoIncrementId();
            });

            DB::commit();

        } catch (Exception $ex) {
            // システムエラー
            DB::rollBack();
            report($ex);
            return array('resultCode' => config('constants.RESULT_CODE_SYSTEM_ERROR'), 'message' => $ex);
        }

    }

    /**
     * INSERT～ON DUPLICATE KEY UPDATE 後のauto increment調整
     */
    private function setAutoIncrementId() {
        $max_id = $this->TTicket->max('id') + 1;
        DB::statement('ALTER TABLE `t_ticket` AUTO_INCREMENT = ' . $max_id . ';');
    }

    public function getExamNumber($conditions, $personal_id) {
        return $this->TTicket::where('id_person', $personal_id)
            ->where('year', $conditions['year'])
            ->where('cnttest', $conditions['cnttest'])
            ->where('grade', $conditions['grade'])
            ->first()['exam_number'];
    }

}
