<?php

namespace App\Repositories\MDirectory;

use App\Models\MDirectory;
use App\Models\TTicket;
use Illuminate\Support\Facades\DB;

/**
 * ディレクトリマスタ用リポジトリ
 */
class MDirectoryRepository implements MDirectoryRepositoryInterface
{
    /**
     * ディレクトリマスタモデル
     *
     * @var type model
     */
    private $MDirectory;

    /**
     * コンストラクタ
     *
     * @param  MDirectory  $mDirectory  ディレクトリマスタモデル
     * @param  TTicket  $tTicket  志望校テーブルモデル
     */
    public function __construct(MDirectory $mDirectory, TTicket $tTicket)
    {
        $this->MDirectory = $mDirectory;
        $this->TTicket = $tTicket;
    }

    /**
     * 引数指定で相応ディレクトリのレコード数(資料一覧画面用）
     *
     * @param  string  $year  年度
     * @param  int  $cnttest  回数
     * @param  string  $grade  グレード
     * @param  int  $schCode  塾コード
     * @param  string  $categoryKbn  年度
     */
    public function checkDirectory(array $param)
    {
        $dir_count = DB::table('m_directory')
            ->where('year', $param['year'])
            ->where('cnttest', $param['cnttest'])
            ->where('grade', $param['grade'])
            ->where('sch_code', $param['schCode'])
            ->where('category_kbn', $param['categoryKbn'])
            ->count();

        return $dir_count;
    }

    /**
     * 引数指定での検索処理管理一覧(検索画面用）
     *
     * @param  int  $schCode  塾コード
     * @param  string  $schName  塾名
     */
    public function selectList(array $conditions)
    {

        // 塾ユーザ検索
        $query = $this->MDirectory::where('sch_code', '=', $conditions['sch_code']);

        // 検索条件
        if (isset($conditions['cls_code'])) {
            $query->where('cls_code', '=', $conditions['cls_code']);
        }
        if (isset($conditions['year'])) {
            $query->where('year', '=', $conditions['year']);
        }
        if (isset($conditions['grade'])) {
            $query->where('grade', '=', $conditions['grade']);
        }
        if (isset($conditions['path_kbn'])) {
            $query->where('path_kbn', '=', $conditions['path_kbn']);
        }
        if (isset($conditions['category_kbn'])) {
            $query->where('category_kbn', '=', $conditions['category_kbn']);
        }

        $query->select('year', 'cnttest', 'grade', 'psn_report_publish_flg')
            ->groupBy('year', 'cnttest', 'grade', 'psn_report_publish_flg')
            ->orderBy('year', 'desc')
            ->orderBy('cnttest', 'desc')
            ->orderBy('grade', 'asc');

        // 表示結果を取得(ソート実施）
        return $query->get()->values();

    }

    /**
     * ファイル情報検索処理
     *
     * @param  int  $schCode  塾コード
     * @param  int  $clsCode  塾名
     * @param  int  $year  年度
     * @param  int  $cnttest  テスト回数
     * @param  int  $grade  学年
     * @param  string  $category_kbn  帳票ID
     * @return type
     */
    public function selectFilePath(array $param)
    {
        switch ($param['category_kbn']) {
            case config('constants.FILE_KBN_LS1'):
                $directory = $this->MDirectory::where('year', $param['year'])
                    ->where('cnttest', $param['cnttest'])
                    ->where('grade', $param['grade'])
                    ->where('path_kbn', config('constants.PATH_KBN_FILE'))
                    ->where('category_kbn', $param['category_kbn'])
                    ->where('sch_code', $param['sch_code'])
                    ->first();
                break;
            case config('constants.FILE_KBN_LS2'):
                $directory = $this->MDirectory::where('year', $param['year'])
                    ->where('cnttest', $param['cnttest'])
                    ->where('grade', $param['grade'])
                    ->where('path_kbn', config('constants.PATH_KBN_FILE'))
                    ->where('category_kbn', $param['category_kbn'])
                    ->where('sch_code', $param['sch_code'])
                    ->first();
                break;
            case config('constants.FILE_KBN_LC1'):
                $directory = $this->MDirectory::where('year', $param['year'])
                    ->where('cnttest', $param['cnttest'])
                    ->where('grade', $param['grade'])
                    ->where('path_kbn', config('constants.PATH_KBN_FILE'))
                    ->where('category_kbn', $param['category_kbn'])
                    ->where('sch_code', $param['sch_code'])
                    ->where('cls_code', $param['cls_code'])
                    ->first();
                break;
            case config('constants.FILE_KBN_LC2'):
                $directory = $this->MDirectory::where('year', $param['year'])
                    ->where('cnttest', $param['cnttest'])
                    ->where('grade', $param['grade'])
                    ->where('path_kbn', config('constants.PATH_KBN_FILE'))
                    ->where('category_kbn', $param['category_kbn'])
                    ->where('sch_code', $param['sch_code'])
                    ->where('cls_code', $param['cls_code'])
                    ->first();
                break;
            case config('constants.FILE_KBN_SBJ'):
                $directory = $this->MDirectory::where('year', $param['year'])
                    ->where('cnttest', $param['cnttest'])
                    ->where('grade', $param['grade'])
                    ->where('path_kbn', config('constants.PATH_KBN_FILE'))
                    ->where('category_kbn', $param['category_kbn'])
                    ->where('sch_code', $param['sch_code'])
                    ->first();
                break;
            case config('constants.FILE_KBN_CLS'):
                $directory = $this->MDirectory::where('year', $param['year'])
                    ->where('cnttest', $param['cnttest'])
                    ->where('grade', $param['grade'])
                    ->where('path_kbn', config('constants.PATH_KBN_FILE'))
                    ->where('category_kbn', $param['category_kbn'])
                    ->where('sch_code', $param['sch_code'])
                    ->first();
                break;
            case config('constants.FILE_KBN_DAT'):
                $directory = $this->MDirectory::where('year', $param['year'])
                    ->where('cnttest', $param['cnttest'])
                    ->where('grade', $param['grade'])
                    ->where('path_kbn', config('constants.PATH_KBN_FILE'))
                    ->where('category_kbn', $param['category_kbn'])
                    ->first();
                break;
            case config('constants.FILE_KBN_CMT'):
                $directory = $this->MDirectory::where('year', $param['year'])
                    ->where('cnttest', $param['cnttest'])
                    ->where('grade', $param['grade'])
                    ->where('path_kbn', config('constants.PATH_KBN_FILE'))
                    ->where('category_kbn', $param['category_kbn'])
                    ->first();
                break;
            case config('constants.FILE_KBN_PSN'):
                $directory = $this->MDirectory::where('year', $param['year'])
                    ->where('cnttest', $param['cnttest'])
                    ->where('grade', $param['grade'])
                    ->where('path_kbn', config('constants.PATH_KBN_FILE'))
                    ->where('category_kbn', $param['category_kbn'])
                    ->first();
                break;
            case config('constants.CATEGORY_KBN_SCH'):
            case config('constants.CATEGORY_KBN_CSV'):
                // dd($param);
                $directory = $this->MDirectory::where('year', $param['year'])
                    ->where('cnttest', $param['cnttest'])
                    ->where('grade', $param['grade'])
                    ->where('path_kbn', config('constants.PATH_KBN_DIRECTORY'))
                    ->where('category_kbn', $param['category_kbn'])
                    ->where('sch_code', $param['sch_code'])
                    ->first();
                break;
            default:
                return;
                break;
        }

        // 表示結果を取得(ソート実施）
        return $directory;
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     *
     * @param  int  $schCode  塾コード
     * @param  int  $clsCode  塾名
     * @param  int  $year  年度
     * @param  int  $cnttest  テスト回数
     * @param  int  $grade  学年
     * @param  string  $category_kbn  帳票ID
     * @return type
     */
    public function selectTestCountPearsonList(int $sch_code, ?int $cls_code, array $param)
    {

        $fileList = null;
        if (! isset($cls_code)) {
            $fileList = DB::table('m_directory')
                ->join('m_person', [
                    ['m_directory.sch_code', '=', 'm_person.sch_code'], ['m_directory.psn_code', '=', 'm_person.psn_code'],
                ])
                ->LEFTJOIN('m_class', [
                    ['m_person.sch_code', '=', 'm_class.sch_code'], ['m_person.cls_code', '=', 'm_class.cls_code'],
                ])
                ->select('m_directory.*', 'm_person.name', 'm_class.cls_code', 'm_class.name as cls_name')
                ->where('m_directory.year', $param['year'])
                ->where('m_directory.cnttest', $param['cnttest'])
                ->where('m_directory.grade', $param['grade'])
                ->where('m_directory.sch_code', $sch_code)
                ->where('m_directory.path_kbn', config('constants.PATH_KBN_FILE'))
                ->where('m_directory.category_kbn', config('constants.FILE_KBN_PSN'))
                ->get();
        } else {
            $fileList = DB::table('m_directory')
                ->join('m_person', [
                    ['m_directory.sch_code', '=', 'm_person.sch_code'], ['m_directory.psn_code', '=', 'm_person.psn_code'],
                ])
                ->LEFTJOIN('m_class', [
                    ['m_person.sch_code', '=', 'm_class.sch_code'], ['m_person.cls_code', '=', 'm_class.cls_code'],
                ])
                ->select('m_directory.*', 'm_person.name', 'm_class.cls_code', 'm_class.name as cls_name')
                ->where('m_directory.year', $param['year'])
                ->where('m_directory.cnttest', $param['cnttest'])
                ->where('m_directory.grade', $param['grade'])
                ->where('m_directory.sch_code', $sch_code)
                ->where('m_directory.cls_code', $cls_code)
                ->where('m_directory.path_kbn', config('constants.PATH_KBN_FILE'))
                ->where('m_directory.category_kbn', config('constants.FILE_KBN_PSN'))
                ->get();
        }

        // 表示結果を取得(ソート実施）
        return $fileList;
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     *
     * @param  int  $schCode  塾コード
     * @param  int  $clsCode  塾名
     * @param  int  $year  年度
     * @param  int  $cnttest  テスト回数
     * @param  int  $grade  学年
     * @param  string  $category_kbn  帳票ID
     * @return type
     */
    public function selectTestPearson(int $sch_code, int $cls_code, string $psn_code)
    {

        $fileKbn = (string) config('constants.PATH_KBN_FILE');
        $dat = (string) config('constants.FILE_KBN_DAT');
        $cmt = (string) config('constants.FILE_KBN_CMT');
        $psn = (string) config('constants.FILE_KBN_PSN');
        // 個人成績表すべて取得
        $fileList = DB::table('m_directory as psn_dir')
            ->leftjoin('m_directory as dat_dir', function ($join) use ($fileKbn, $dat) {
                $join->on('psn_dir.year', '=', 'dat_dir.year')
                    ->on('psn_dir.cnttest', '=', 'dat_dir.cnttest')
                    ->on('psn_dir.grade', '=', 'dat_dir.grade')
                    ->where('dat_dir.path_kbn', '=', $fileKbn)
                    ->where('dat_dir.category_kbn', '=', $dat);
            })
            ->leftjoin('m_directory as cmt_dir', function ($join) use ($fileKbn, $cmt) {
                $join->on('psn_dir.year', '=', 'cmt_dir.year')
                    ->on('psn_dir.cnttest', '=', 'cmt_dir.cnttest')
                    ->on('psn_dir.grade', '=', 'cmt_dir.grade')
                    ->where('cmt_dir.path_kbn', '=', $fileKbn)
                    ->where('cmt_dir.category_kbn', '=', $cmt);
            })
            ->select('psn_dir.*', 'dat_dir.fullpath as datfullpath', 'cmt_dir.fullpath as cmtfullpath', 'psn_dir.psn_report_publish_flg')
            ->where('psn_dir.sch_code', $sch_code)
            ->where('psn_dir.cls_code', $cls_code)
            ->where('psn_dir.psn_code', $psn_code)
            ->where('psn_dir.path_kbn', $fileKbn)
            ->where('psn_dir.category_kbn', $psn)
            ->orderBy('year', 'desc')
            ->orderBy('cnttest', 'desc')
            ->orderBy('grade', 'asc')
            ->get();

        // 表示結果を取得(ソート実施）
        return $fileList;
    }

    /**
     * 志望校一覧用リスト（生徒画面用）
     *
     * @param  array  $conditions  検索条件
     */
    public function selectSOCPersonList(array $conditions)
    {
        $fileKbn = (string) config('constants.PATH_KBN_FILE');
        $psn = (string) config('constants.FILE_KBN_PSN');
        $schCode = $conditions['sch_code'];
        $psnCode = $conditions['psn_code'];
        $idPerson = $conditions['id_person'];

        // リストのもとになるリストのサブクエリ
        $subQuery1 = DB::table('m_directory as r')
            ->whereRaw('`m`.`year` = `r`.`year`')
            ->whereRaw('`m`.`cnttest` = `r`.`cnttest`')
            ->whereRaw('`m`.`grade` = `r`.`grade`')
            ->where('r.path_kbn', '=', $fileKbn)
            ->where('r.category_kbn', '=', $psn)
            ->where('r.sch_code', '=', $schCode)
            ->where('r.psn_code', '=', $psnCode)
            ->selectRaw('1');

        $subQuery2 = $this->TTicket::whereRaw('`m`.`year` = `t_ticket`.`year`')
            ->whereRaw('`m`.`cnttest` = `t_ticket`.`cnttest`')
            ->whereRaw('`m`.`grade` = `t_ticket`.`grade`')
            ->where('id_person', '=', $idPerson)
            ->selectRaw('1');

        $subQuery3 = $this->MDirectory::select(
            'year',
            'cnttest',
            'grade'
        )
            ->where('sch_code', '=', $schCode);

        if (isset($conditions['year'])) {
            $subQuery3->where('year', '=', $conditions['year']);
        }
        if (isset($conditions['grade'])) {
            $subQuery3->where('grade', '=', $conditions['grade']);
        }
        $subQuery3->groupBy('year', 'cnttest', 'grade');

        $query = DB::table(DB::raw('('.$subQuery3->toSql().') as m'))
            ->select(
                'year',
                'cnttest',
                'grade'
            )
            ->selectRaw('('.$subQuery1->toSql().') as reportflag')
            ->selectRaw('('.$subQuery2->toSql().') as choiceflag')
            ->mergeBindings($subQuery1)
            ->mergeBindings($subQuery2->getQuery())
            ->mergeBindings($subQuery3->getQuery())
            ->orderBy('year', 'desc')
            ->orderBy('cnttest', 'desc')
            ->orderBy('grade', 'asc');

        // 表示結果を取得(ソート実施）
        return $query->get()->values();
    }

    /**
     * 成績表返却チェック（生徒画面用）
     *
     * @param  array  $conditions  検索条件
     * @return 1:登録済、0:未登録
     */
    public function rptReturn(array $conditions)
    {
        $fileKbn = (string) config('constants.PATH_KBN_FILE');
        $psn = (string) config('constants.FILE_KBN_PSN');

        $query = $this->MDirectory::where('sch_code', '=', $conditions['sch_code'])
            ->where('psn_code', '=', $conditions['psn_code'])
            ->where('year', '=', $conditions['year'])
            ->where('cnttest', '=', $conditions['cnttest'])
            ->where('grade', '=', $conditions['grade'])
            ->where('path_kbn', '=', $fileKbn)
            ->where('category_kbn', '=', $psn);

        return ($query->count() > 0) ? 1 : 0;
    }

    /**
     * 個人成績表公開フラグ更新
     *
     * @param  array  $conditions  検索条件
     */
    public function updatePsnReportPublishFlg(array $conditions)
    {
        // Enable query logging
        // DB::enableQueryLog();
        $psn = config('constants.FILE_KBN_PSN');
        $cmt = config('constants.FILE_KBN_CMT');
        $dat = config('constants.FILE_KBN_DAT');

        // Update query
        $this->MDirectory::where('sch_code', '=', $conditions['sch_code'])
            ->when(isset($conditions['cls_code']), function ($query) use ($conditions) {
                return $query->where('cls_code', '=', $conditions['cls_code']);
            })
            ->where('year', '=', $conditions['year'])
            ->where('cnttest', '=', $conditions['cnttest'])
            ->where('grade', '=', $conditions['grade'])
            ->where('path_kbn', '=', $conditions['path_kbn'])
            ->whereIn('category_kbn', [$psn, $cmt, $dat])
            ->update(['psn_report_publish_flg' => $conditions['psn_report_publish_flg']]);

        // Retrieve and display the query log
        // dd($conditions, DB::getQueryLog());
    }

    /**
     * 試験公開フラグ確認
     *
     * @param  array  $conditions  検索条件
     */
    public function checkPublishFlg(array $conditions)
    {
        // DB::enableQueryLog();
        $psn = config('constants.FILE_KBN_PSN');
        $query = $this->MDirectory::where('sch_code', '=', $conditions['sch_code'])
            ->where('year', '=', $conditions['year'])
            ->where('cnttest', '=', $conditions['cnttest'])
            ->where('grade', '=', $conditions['grade'])
            ->where('path_kbn', '=', $conditions['path_kbn'])
            ->where('category_kbn', [$psn])
            ->where('psn_report_publish_flg', '=', config('constants.PSN_REPORT_PUBLISH_USE_FLG'));
        $count = $query->count();
        // \Log::info($conditions, DB::getQueryLog());

        return $count;
    }
}
