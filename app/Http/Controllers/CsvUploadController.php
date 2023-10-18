<?php

namespace App\Http\Controllers;

//useしないと 自動的にnamespaceのパスが付与されるのでuse
use \SplFileObject;
use App\Models\MPerson;
use App\Services\CsvService as CsvService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CsvUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Utils\FileUtil;
use Illuminate\Support\Facades\DB;

class CsvUploadController extends Controller {

    private $csvService;

    public function __construct(CsvService $csvService) {
        $this->middleware('auth:'.config('constants.GUARD_ADMIN'));
        $this->csvService = $csvService;
    }

    /**
     * CSVインポート・ダウンロード画面を表示
     */
    public function index() {
        return view('csvUpload');
    }
    /**
     * CSV アップロード処理
     * @return \Illuminate\Http\CsvUploadRequest
     * @param Request $request
     */
    public function upload(CsvUploadRequest $request) {

        // ヘッダ
        $csvHeader = ['エラー行', 'エラーメッセージ'];

        // ロケールを設定(日本語に設定)
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        // アップロードしたファイルを取得
        // 'csv_file' はビューの inputタグのname属性
        $uploaded_file = $request->file('csv_file');

        //SplFileObjectを生成
        $file = new SplFileObject($uploaded_file);

        //SplFileObject::READ_CSV が最速らしい
        $file->setFlags(SplFileObject::READ_CSV);

        // CSVの中身に対するバリデーションを実施
        $csv_errors = $this->csvService->validateCsvData($file);

        // 登録データ格納配列
        $arrayInsertData = array();

        // エラー有無をチェック
        if (count($csv_errors) > 0) {
            // HTTP Header 作成
            $responseHeader = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachement;filename=' . config('constants.PERSON_IMPORT_CSV_ERRFILENAME'),
            ];

            // エラー内容CSVをダウンロードさせる
            return response()->streamDownload(function() use($csv_errors, $csvHeader) {
                        echo FileUtil::CreateCSV($csv_errors, $csvHeader);
                    }, config('constants.PERSON_IMPORT_CSV_ERRFILENAME'), $responseHeader);
        }
        else{
            $row_count = 0;

            //取得したオブジェクトを読み込み
            foreach ($file as $row) {
                // 最終行の処理(最終行が空っぽの場合の対策
                if ($row === [null])
                    continue;

                // 1行目のヘッダーは取り込まない
                if ($row_count > 0) {
                    // CSVの文字コードがSJIS-winなのでUTF-8に変更
//                    $sch_code   = mb_convert_encoding($row[0], 'UTF-8', 'SJIS-win');
//                    $psn_code   = mb_convert_encoding($row[1], 'UTF-8', 'SJIS-win');
//                    $cls_code   = mb_convert_encoding($row[2], 'UTF-8', 'SJIS-win');
//                    $name       = mb_convert_encoding($row[3], 'UTF-8', 'SJIS-win');
//                    $birthlow4  = mb_convert_encoding($row[4], 'UTF-8', 'SJIS-win');
//                    $postallow4 = mb_convert_encoding($row[5], 'UTF-8', 'SJIS-win');
//                    $tellow4    = mb_convert_encoding($row[6], 'UTF-8', 'SJIS-win');

                    $sch_code   = $row[0];
                    $psn_code   = $row[1];
                    $cls_code   = $row[2];
                    $name       = $row[3];
                    $birthlow4  = $row[4];
                    $postallow4 = $row[5];
                    $tellow4    = $row[6];

                    /************************************************************************
                     * ①CSV に本パスワードが設定されている場合
                     *   仮パスワードを null にして、本パスワードは CSV の値で更新
                     * ②CSV に仮パスワードのみ設定されている場合
                     *   CSV に設定されている仮パスワードで更新
                     * ③仮パスワード及び本パスワードとも未設定の場合
                     *   当該カラムを対象外とし、他のカラムのみ更新する
                     ************************************************************************/

                    if( empty( $row[7] ) && empty( $row[8] ) ){
                      // 登録データを配列に追加 ( パスワードの変更なし )
                      $temp = array(
                        'sch_code'      => $sch_code,
                        'psn_code'      => $psn_code,
                        'cls_code'      => $cls_code,
                        'name'          => $name,
                        'birthlow4'     => $birthlow4,
                        'postallow4'    => $postallow4,
                        'tellow4'       => $tellow4,
                      );

                      // 新規生徒の場合、仮パスワードを付与
                      if( $this->csvService->isNewMPerson( $sch_code, $cls_code, $psn_code )){
                          $temp['password']     = null;
                          $temp['temppassword'] = Crypt::encryptString( str_random(8) );
                      }
                      $arrayInsertData[] = $temp;
                    } else {
                      // temp password はプログラムで生成して登録する
                      $password   = $row[7];
                      $temppass   = empty( $password ) ? ( empty( $row[8] ) ? str_random(8) : $row[8] ) : null;

                      // 登録データを配列に追加 ( パスワードの変更あり )
                      $arrayInsertData[] = array(
                        'sch_code'      => $sch_code,
                        'psn_code'      => $psn_code,
                        'cls_code'      => $cls_code,
                        'name'          => $name,
                        'birthlow4'     => $birthlow4,
                        'postallow4'    => $postallow4,
                        'tellow4'       => $tellow4,
                        'password'      => empty( $password ) ? null : Hash::make( $password ),
                        'temppassword'  => empty( $temppass ) ? null : Crypt::encryptString( $temppass ),
                      );
                    }
                }
                // 行カウンタをインクリメント
                $row_count++;
            }

            if( !empty( $arrayInsertData) ){
              // Insert処理
              $this->csvService->registerArray($arrayInsertData);
            }
        }

        // 画面表示
        return view('csvUpload')->with('message', trans('message.SUCCESS_MPERSON_CSV_DATA'));
    }

}
