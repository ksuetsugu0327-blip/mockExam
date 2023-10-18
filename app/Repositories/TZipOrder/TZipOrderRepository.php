<?php

namespace App\Repositories\TZipOrder;

use App\Repositories\TZipOrder\TZipOrderRepositoryInterface as TZipOrderRepositoryInterface;
use App\Models\TZipOrder as TZipOrder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ディレクトリマスタ用リポジトリ
 *
 */
class TZipOrderRepository implements TZipOrderRepositoryInterface{
    
    /**
     * ディレクトリマスタモデル
     * @var type model
     */
    private $TZipOrder;
    
    /**
     * コンストラクタ
     * @param TZipOrder $mDirectory  ディレクトリマスタモデル
     */
    public function __construct(TZipOrder $tZipOrder){
        $this->TZipOrder = $tZipOrder;
    }
    
    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param int $schCode          塾コード
     * @param string $schName       塾名
     */
    public function selectList(?int $schCode=null, ?int $clsCode=null){
        
        $query = "";
        
        if(!empty($clsCode))
        {
            // 塾ユーザ検索
            $query = $this->TZipOrder::where('sch_code', $schCode)
                                ->where('cls_code', $clsCode)
                                ->select('year','cnttest','grade')
                                ->groupBy('year','cnttest','grade');
        }
        else
        {
            // 教室ユーザ検索
            $query = $this->TZipOrder::where('sch_code', $schCode)
                                ->select('year','cnttest','grade')
                                ->groupBy('year','cnttest','grade')
                                ->get();
        }
        
        // 表示結果を取得(ソート実施）
        return $query->get()->sortBy('sch_code')->values();
        
    }

    /**
     * 引数指定での検索処理( 検索画面用 ）
     * @param int $schCode 塾コード
     * @param int $clsCode 塾名
     * @param int $year 年度
     * @param int $cnttest テスト回数
     * @param int $grade 学年
     * @param string $category_kbn 帳票ID
     * @return type
     */
    public function zipDownloadPath(array $param){
        
        return TZipOrder::where('category_kbn', $param["category_kbn"])
                    ->where('year', $param["year"])
                    ->where('cnttest', $param["cnttest"])
                    ->where('sch_code', $param["sch_code"])
                    ->where('cls_code', $param["cls_code"])
                    ->where('grade', $param["grade"])
                    ->where('error_kbn', "0")->first();
        
    }
    /**
     * 登録処理
     * @param array $order 発行指示情報連想配列
     * @return 連想配列(resultCode:-1(システムエラー) 1(正常終了) message:メッセージ)
     */
    public function register(array $order) {
        DB::beginTransaction();
        try{
            $flg = $this->TZipOrder->where('category_kbn', $order['category_kbn'])
                    ->where('year', $order['year'])
                    ->where('cnttest', $order['cnttest'])
                    ->where('grade', $order['grade'])
                    ->where('sch_code', $order['sch_code'])
                    ->where('cls_code', $order['cls_code'])
                    ->where('error_kbn', '0')
                    ->exists();
            
            if($flg){
                // 排他エラー
                DB::rollback();
                return array('resultCode'=>config('constants.RESULT_CODE_BUSINESS_ERROR'),'message'=>trans('message.TZIPORDER_DONE_EXCEPTION'));
            }
            
            // 発行指示テーブル
            $tZiporder = $this->TZipOrder->newInstance();
            // 発行区分
            $tZiporder->category_kbn = $order['category_kbn'];
            // 処理状況区分
            $tZiporder->process_kbn = $order['process_kbn'];
            // 年度
            $tZiporder->year = $order['year'];
            // 回数
            $tZiporder->cnttest = $order['cnttest'];
            // 学年
            $tZiporder->grade = $order['grade'];
            // 塾コード
            $tZiporder->sch_code = $order['sch_code'];
            // 教室コード
            $tZiporder->cls_code = $order['cls_code'];
            // 圧縮ファイル格納先パス
            $tZiporder->zipfilepath = $order['zipfilepath'];
            // 圧縮ファイルパスワード
            $tZiporder->zipfilepass = $order['zipfilepass'];
            // エラー区分
            $tZiporder->error_kbn = $order['error_kbn'];
            // 発行指示日時
            $tZiporder->order_at = $order['order_at'];
            // 圧縮ファイル作成日時
            $tZiporder->zipfile_at = $order['zipfile_at'];
            // ダウンロード日時
            $tZiporder->download_at = $order['download_at'];
            //登録処理
            $tZiporder->save();
            
            DB::commit();
        } catch (Exception $ex){
            // システムエラー
            DB::rollback();
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }
        
        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'));        
    }
    
    /**
     * 更新処理
     * @param array $school 塾情報(連想配列)
     * @return 連想配列(resultCode:-1(システムエラー) 0(排他エラー) 1(正常終了) message:メッセージ)
     */
    public function update(int $id) {
        try{
            
            $tZiporder = $this->TZipOrder::findOrFail($id);
            
            // ダウンロード日時
            $tZiporder->download_at = Carbon::now(); // 更新処理
            $tZiporder->save();

        } catch (ModelNotFoundException $ex) {
            // 排他エラー
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_BUSINESS_ERROR'));
        } catch (Exception $ex){
            // システムエラー
            report($ex);
            return array('resultCode'=>config('constants.RESULT_CODE_SYSTEM_ERROR'),'message'=>$ex);
        }
        return array('resultCode'=>config('constants.RESULT_CODE_SUCCESS'));
    }
}
