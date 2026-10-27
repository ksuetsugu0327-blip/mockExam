<?php

namespace App\Http\Controllers;

// useしないと 自動的にnamespaceのパスが付与されるのでuse
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\ExamNumberRequest;
use App\Services\ExamNumberCsvService;
use App\Utils\FileUtil;
use Illuminate\View\View;
use SplFileObject;

class ExamNumberCsvUploadController extends Controller implements HasMiddleware
{
    private $ExamNumberCsvService;

    public function __construct(ExamNumberCsvService $ExamNumberCsvService)
    {

        $this->ExamNumberCsvService = $ExamNumberCsvService;
    }

    public static function middleware(): array
    {
        return [
            'auth:'.config('constants.GUARD_ADMIN'),
        ];
    }

    /**
     * CSVインポート画面を表示
     */
    public function index(): View
    {
        return view('ExamNumberCsvUpload');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function upload(ExamNumberRequest $request)
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
        $csv_errors = $this->ExamNumberCsvService->validateCsvData($file);

        // 登録データ格納配列
        $import_data = [];

        // エラー有無をチェック
        if (count($csv_errors) > 0) {
            // HTTP Header 作成
            $responseHeader = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachement;filename='.config('constants.EXAMNUM_IMPORT_ERR_FILENAME'),
            ];

            // エラー内容CSVをダウンロードさせる
            return response()->streamDownload(
                function () use ($csv_errors, $csvHeader) {
                    echo FileUtil::CreateCSV($csv_errors, $csvHeader);
                },
                config('constants.EXAMNUM_IMPORT_ERR_FILENAME'), $responseHeader
            );

        } else {
            // 更新用データの取得読み込み
            $import_data = $this->ExamNumberCsvService->getRegisterData();
        }

        if (count($import_data) > 0) {
            // Insert処理
            $this->ExamNumberCsvService->registerArray($import_data);
        }

        // 画面表示
        return view('ExamNumberCsvUpload')->with('message', trans('message.SUCCESS_MCLASS_CSV_DATA'));
    }
}
