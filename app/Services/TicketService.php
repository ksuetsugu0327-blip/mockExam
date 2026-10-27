<?php

namespace App\Services;

use App\Repositories\MDepartment\MDepartmentRepositoryInterface;
use App\Repositories\MFaculty\MFacultyRepositoryInterface;
use App\Repositories\MPerson\MPersonRepositoryInterface;
use App\Repositories\MUniversity\MUniversityRepositoryInterface;
use App\Repositories\TTicket\TTicketRepositoryInterface;

/**
 * 志望校関連用サービス
 */
class TicketService
{
    // リポジトリ
    // 個人マスタ
    private $repositoryMPerson;

    // 志望校テーブル
    private $repositoryTTicket;

    // 大学マスタ
    private $repositoryMUniversity;

    // 学部マスタ
    private $repositoryMFaculty;

    // 学科マスタ
    private $repositoryMDepartment;

    // 回数コンバートサービス
    private $mgmkaiService;

    /**
     * コンストラクタ
     */
    public function __construct(MPersonRepositoryInterface $repositoryMPerson, TTicketRepositoryInterface $repositoryTTicket, MUniversityRepositoryInterface $repositoryMUniversity, MFacultyRepositoryInterface $repositoryMFaculty, MDepartmentRepositoryInterface $repositoryMDepartment, MGMKAIService $mgmkaiService)
    {
        $this->repositoryMPerson = $repositoryMPerson;
        $this->repositoryTTicket = $repositoryTTicket;
        $this->repositoryMUniversity = $repositoryMUniversity;
        $this->repositoryMFaculty = $repositoryMFaculty;
        $this->repositoryMDepartment = $repositoryMDepartment;
        $this->mgmkaiService = $mgmkaiService;
    }

    /**
     * 検索処理
     *
     * @param  int  $viewMode  表タイプ
     * @param  array  $conditions  検索条件
     * @return type collection
     */
    public function search(int $viewMode, array $conditions)
    {
        $exam_data = [];

        // t_ticketからレコードを取得
        if ($viewMode == 4) { // 生徒の場合
            $exam_grp = $this->repositoryTTicket->findStudentExamData($conditions);
        } elseif ($viewMode == 3) { // 教室の場合
            $exam_grp = $this->repositoryTTicket->findClsExamData($conditions);
        } else {
            $exam_grp = $this->repositoryTTicket->findSchExamData($conditions);
        }

        // テスト表示名取得
        $sch_code = $conditions['sch_code'];
        $cntAry = $this->mgmkaiService->changeTestName($sch_code);

        foreach ($exam_grp as $index => $entry) {
            // 回数名設定
            $setStr = $cntAry[$entry->year.$entry->grade][$cntAry['cnttest_kbn']][$entry->cnttest] ?? '';
            $exam_data[$index] = [
                'year' => $entry->year,
                'grade' => $entry->grade,
                'testName' => $setStr,
                'cnttest' => $entry->cnttest,
            ];
            // 生徒の場合は志望校登録と返却状況のフラグを追加
            if ($viewMode == 4) {
                $exam_data[$index]['choiceflag'] = $entry->choiceflag;
                $exam_data[$index]['reportflag'] = $entry->reportflag;
            }
        }

        return $exam_data;
    }

    /**
     * 志望校リスト
     *
     * @param  array  $conditions  検索条件
     * @return type TTicket
     */
    public function TicketList(array $conditions)
    {
        return $this->repositoryTTicket->selectList($conditions);
    }

    /**
     * 大学リスト
     *
     * @param  array  $conditions  検索条件
     * @return type MUniversity
     */
    public function univList(array $conditions)
    {
        $list = $this->repositoryMUniversity->selectList($conditions)
            ->pluck('univ_name', 'univ_code')
            ->toArray();

        return $list;
    }

    /**
     * 学部リスト
     *
     * @param  array  $conditions  検索条件
     * @return type MFaculty
     */
    public function facultyList(array $conditions)
    {
        $list = $this->repositoryMFaculty->selectList($conditions)
            ->pluck('fcl_name', 'fcl_code')
            ->toArray();

        return $list;
    }

    /**
     * 学科リスト
     *
     * @param  array  $conditions  検索条件
     * @return type MFaculty
     */
    public function departmentList(array $conditions)
    {
        $list = $this->repositoryMDepartment->selectList($conditions)
            ->pluck('dpt_name', 'dpt_code')
            ->toArray();

        return $list;
    }

    /**
     * 志望校更新処理
     *
     * @param  array  $data  志望校情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function updateChoiceOfSchool(array $data)
    {
        // 高校更新処理
        $person = [
            'id' => $data['id'],
            'sch_code' => $data['sch_code'],
            'psn_code' => $data['psn_code'],
            'hsc_code' => $data['hsc_code'],
        ];
        $result = $this->repositoryMPerson->updateHighSchool($person);
        // 結果判定
        if ($result['resultCode'] == -1) {
            return $result;
        }

        // 志望校更新処理
        $choice = [
            'id_person' => $data['id'],
            'year' => $data['year'],
            'cnttest' => $data['cnttest'],
            'grade' => $data['grade'],
            'testname' => $data['testname'],
        ];

        // t_ticketに受験番号のデータ登録が前提のため、志望校が空でも削除でなく更新処理を実行する
        for ($i = 1; $i <= 6; $i++) {
            $choice += [
                'c_univ_code'.$i => $data['c_univ_code'.$i],
                'c_faculty_code'.$i => $data['c_faculty_code'.$i],
                'c_department_code'.$i => $data['c_department_code'.$i],
            ];
        }

        return $this->repositoryTTicket->upsert($choice);
    }

    public function getExamNumber($conditions, $personal_id)
    {
        return $this->repositoryTTicket->getExamNumber($conditions, $personal_id);
    }
}
