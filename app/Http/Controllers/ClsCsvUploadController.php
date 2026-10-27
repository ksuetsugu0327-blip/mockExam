<?php

namespace App\Http\Controllers;

// useしないと 自動的にnamespaceのパスが付与されるのでuse
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\ClsCsvUploadRequest;
use App\Services\ClsCsvService;
use App\Utils\FileUtil;
use Illuminate\Http\CsvUploadRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SplFileObject;

class ClsCsvUploadController extends Controller implements HasMiddleware
{
    private $ClsCsvService;

    public function __construct(ClsCsvService $ClsCsvService)
    {

        $this->ClsCsvService = $ClsCsvService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_ADMIN'),
        ];
    }

    /**
     * CSVインポート・ダウンロード画面を表示
     */
    public function index(): View
    {
        return view('clsCsvUpload');
    }

    /**
     * CSV アップロード処理
     */
    public function upload(ClsCsvUploadRequest $request): CsvUploadRequest
    {

        // ヘッダ
        $csvHeader = ['エラー行', 'エラーメッセージ'];

        // ロケールを設定(日本語に設定)
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        // アップロードしたファイルを取得
        // 'csv_file' はビューの inputタグのname属性
        $uploaded_file = $request->file('csv_file');

        // SplFileObjectを生成
        $file = new SplFileObject($uploaded_file);

        // SplFileObject::READ_CSV が最速らしい
        $file->setFlags(SplFileObject::READ_CSV);

        // CSVの中身に対するバリデーションを実施
        $csv_errors = $this->ClsCsvService->validateCsvData($file);

        // 登録データ格納配列
        $arrayInsertData = [];

        // エラー有無をチェック
        if (count($csv_errors) > 0) {
            // HTTP Header 作成
            $responseHeader = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachement;filename='.config('constants.CLASS_IMPORT_CSV_ERRFILENAME'),
            ];

            // エラー内容CSVをダウンロードさせる
            return response()->streamDownload(function () use ($csv_errors, $csvHeader) {
                echo FileUtil::CreateCSV($csv_errors, $csvHeader);
            }, config('constants.CLASS_IMPORT_CSV_ERRFILENAME'), $responseHeader);
        } else {
            $row_count = 0;

            // 取得したオブジェクトを読み込み
            foreach ($file as $i => $row) {

                // 最終行の処理(最終行が空っぽの場合の対策
                if ($row === [null]) {
                    continue;
                }

                // 1行目のヘッダーは取り込まない
                if ($row_count > 0) {
                    // CSVの文字コードがSJIS-winなのでUTF-8に変更
                    //                    $sch_code   = mb_convert_encoding($row[0], 'UTF-8', 'SJIS-win');
                    //                    $cls_code   = mb_convert_encoding($row[1], 'UTF-8', 'SJIS-win');
                    //                    $cls_name       = mb_convert_encoding($row[2], 'UTF-8', 'SJIS-win');

                    $sch_code = $row[0];
                    $cls_code = $row[1];
                    $cls_name = $row[2];

                    /************************************************************************
                     * 更新時
                     * ①CSV に本パスワードが設定されている場合
                     *   仮パスワードを null にして、本パスワードは CSV の値で更新
                     * ②CSV に仮パスワードのみ設定されている場合
                     *   CSV に設定されている仮パスワードで更新
                     * ③仮パスワード及び本パスワードとも未設定の場合
                     *   何もしない
                     *
                     * 新規登録時
                     * ①CSV に本パスワードが設定されている場合
                     *   仮パスワードを null にして、本パスワードは CSV の値で更新
                     * ②CSV に仮パスワードのみ設定されている場合
                     *   CSV に設定されている仮パスワードで更新
                     * ③仮パスワード及び本パスワードとも未設定の場合
                     *   仮パスワードを発行して登録する
                     ************************************************************************/

                    // 本パスワードと仮パスワードが無い場合
                    if (empty($row[3]) && empty($row[4])) {
                        // 新規登録（m_classにレコードが存在しない）時は仮パスワードを生成する。更新時は何もしない
                        if ($this->ClsCsvService->isNewMClass($sch_code, $cls_code)) {
                            $arrayInsertData[] = [
                                'sch_code' => $sch_code,
                                'cls_code' => $cls_code,
                                'cls_name' => $cls_name,
                                'password' => null,
                                'temppassword' => Crypt::encryptString(Str::random(8)),
                            ];
                        }
                        //                        else{ //（仮パスワードおよび本パスワードとも未設定時は何もしない）
                        //                            $arrayInsertData[] = array(
                        //                                'sch_code'      => $sch_code,
                        //                                'cls_code'      => $cls_code,
                        //                                'name'          => $name,
                        //                            );
                        //                        }

                    } else {
                        // temp password はプログラムで生成して登録する
                        $password = $row[3];
                        $temppass = empty($password) ? (empty($row[4]) ? Str::random(8) : $row[4]) : null;

                        // 登録データを配列に追加 ( パスワードの変更あり )
                        $arrayInsertData[] = [
                            'sch_code' => $sch_code,
                            'cls_code' => $cls_code,
                            'cls_name' => $cls_name,
                            'password' => empty($password) ? null : Hash::make($password),
                            'temppassword' => empty($temppass) ? null : Crypt::encryptString($temppass),
                        ];
                    }
                }
                // 行カウンタをインクリメント
                $row_count++;
            }

            if (! empty($arrayInsertData)) {
                // Insert処理
                $this->ClsCsvService->registerArray($arrayInsertData);
            }
        }

        // 画面表示
        return view('clsCsvUpload')->with('message', trans('message.SUCCESS_MCLASS_CSV_DATA'));
    }
}
