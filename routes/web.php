<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ClassChangePasswordController;
use App\Http\Controllers\ClassIDLoginCheckController;
use App\Http\Controllers\ClassLoginController;
use App\Http\Controllers\ClassTemporaryLoginController;
use App\Http\Controllers\ClsCsvUploadController;
use App\Http\Controllers\CsvUploadController;
use App\Http\Controllers\ExamNumberCsvUploadController;
use App\Http\Controllers\JmasterCorrectionController;
use App\Http\Controllers\JmasterListController;
use App\Http\Controllers\JmasterRegistController;
use App\Http\Controllers\KmasterCorrectionController;
use App\Http\Controllers\KmasterListController;
use App\Http\Controllers\KmasterRegistController;
use App\Http\Controllers\ManagementDocController;
use App\Http\Controllers\ManagementListController;
use App\Http\Controllers\PersonIDLoginCheckController;
use App\Http\Controllers\PersonLoginController;
use App\Http\Controllers\PersonMailChangeController;
use App\Http\Controllers\PersonPasswordChangeController;
use App\Http\Controllers\PersonPasswordResetController;
use App\Http\Controllers\PersonReportCardListController;
use App\Http\Controllers\PersonTemporaryLoginController;
use App\Http\Controllers\PersonUnknownTempPassLoginController;
use App\Http\Controllers\PortalTopController;
use App\Http\Controllers\ReportCardListController;
use App\Http\Controllers\Rest;
use App\Http\Controllers\SchoolIDLoginCheckController;
use App\Http\Controllers\SchoolLoginController;
use App\Http\Controllers\SmasterCorrectionController;
use App\Http\Controllers\SmasterListController;
use App\Http\Controllers\SmasterRegistController;
use App\Http\Controllers\SurrogateLoginController;
use App\Http\Controllers\TestCountSearchListController;
use App\Http\Controllers\TicketDetailController;
use App\Http\Controllers\TicketRegistController;
use App\Http\Controllers\TicketSearchListController;
use App\Http\Controllers\TopMenuController;
use App\Http\Controllers\UnivCodeDeleteController;
use App\Http\Controllers\UnivCodeSearchIndexController;
use App\Http\Controllers\UnivCsvUploadController;
use App\Http\Controllers\UnivTestEditController;
use App\Http\Controllers\UnivTestListController;
use App\Http\Controllers\UnivTestRegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.very_basic')->group(function () {

    // ■個人情報保護方針■
    Route::get('/{path}praivacy', function () {
        return File::get(public_path('/static/praivacy/praivacy.html'));
    })->where('path', '.*');

    //    Route::view('/{path}praivacy', 'praivacy')->where('path','.*');

    // 塾アカウント用ルート
    Route::prefix('/{schCode}/sch')->group(function () {

        // 塾アカウントログイン IDチェック
        Route::get('idcheck', [SchoolIDLoginCheckController::class, 'index'])->name('schoolIdCheck.index')->middleware('ip_limitation');
        Route::post('idcheck', [SchoolIDLoginCheckController::class, 'check'])->name('schoolIdCheck.check');

        // 塾アカウントログイン
        Route::get('login', [SchoolLoginController::class, 'index'])->name('schoolLogin.index');
        // 塾アカウントログイン
        Route::post('login', [SchoolLoginController::class, 'login'])->name('schoolLogin.login');
        // 塾アカウントログアウト
        Route::match(['get', 'post'], 'logout', [SchoolLoginController::class, 'logout'])->name('schoolLogin.logout');

        // メニュー画面
        Route::get('menu', [TopMenuController::class, 'index'])->name('schoolTopMenu.index');

        // 塾マスタ・一覧
        // 初期表示
        Route::get('schools/list', [JmasterListController::class, 'index'])->name('schools.index');
        // 検索
        Route::post('schools/list', [JmasterListController::class, 'search'])->name('schools.search');
        // 削除
        Route::delete('schools/list/{delSchCode}', [JmasterListController::class, 'delete'])->name('schools.delete');

        // 塾マスタ・登録
        // 初期表示
        Route::get('schools', [JmasterRegistController::class, 'index'])->name('schoolRegist.index');
        // 登録
        Route::post('schools', [JmasterRegistController::class, 'store'])->name('schoolRegist.store');

        // 塾マスタ・修正
        // 初期表示
        Route::get('schools/{editSchCode}', [JmasterCorrectionController::class, 'index'])->name('schoolEdit.index');
        // 修正
        Route::put('schools/{editSchCode}', [JmasterCorrectionController::class, 'update'])->name('schoolEdit.update');
        // 削除
        Route::delete('schools/{delSchCode}', [JmasterCorrectionController::class, 'delete'])->name('schoolEdit.delete');

        // 塾代理ログイン
        Route::post('schools/surrogate/login/{loginSchCode}', [SurrogateLoginController::class, 'schoolLogin'])->name('surrogateLogin.schoolLogin');
        // 塾代理ログアウト
        Route::post('schools/surrogate/logout', [SurrogateLoginController::class, 'schoolLogout'])->name('surrogateLogin.schoolLogout');

        // 教室マスタ・一覧
        // 初期表示
        Route::get('classes/list', [KmasterListController::class, 'index'])->name('classes.index');
        // 検索
        Route::post('classes/list', [KmasterListController::class, 'search'])->name('classes.search');
        // 削除
        Route::delete('classes/list/{delSchCode}/{delClsCode}', [KmasterListController::class, 'delete'])->name('classes.delete');
        // CSV出力
        Route::post('classes/list/csv', [KmasterListController::class, 'downloadCsv'])->name('sch.classes.downloadCsv');

        // 教室マスタ・登録
        // 初期表示
        Route::get('classes', [KmasterRegistController::class, 'index'])->name('classRegist.index');
        // 登録
        Route::post('classes', [KmasterRegistController::class, 'store'])->name('classRegist.store');

        // 教室マスタ・修正
        // 初期表示
        Route::get('classes/{editSchCode}/{editClsCode}', [KmasterCorrectionController::class, 'index'])->name('classEdit.index');
        // 修正
        Route::put('classes/{editSchCode}/{editClsCode}', [KmasterCorrectionController::class, 'update'])->name('classEdit.update');
        // 削除
        Route::delete('classes/{delSchCode}/{delClsCode}', [KmasterCorrectionController::class, 'delete'])->name('classEdit.delete');

        // 教室代理ログイン
        Route::post('classes/surrogate/login/{loginSchCode}/{loginClsCode}', [SurrogateLoginController::class, 'classLogin'])->name('surrogateLogin.classLogin');
        // 教室代理ログアウト
        Route::post('classes/surrogate/logout', [SurrogateLoginController::class, 'classLogout'])->name('surrogateLogin.classLogout');

        // 生徒マスタ・一覧
        // 初期表示
        Route::get('persons/list', [SmasterListController::class, 'index'])->name('sch.persons.index');
        // 検索出力
        Route::post('persons/list', [SmasterListController::class, 'search'])->name('sch.persons.search');
        // CSV出力
        Route::post('persons/list/csv', [SmasterListController::class, 'downloadCsv'])->name('sch.persons.downloadCsv');
        // 削除
        Route::delete('persons/list/{delSchCode}/{delPsnCode}', [SmasterListController::class, 'delete'])->name('persons.delete');

        // 生徒マスタ・登録
        // 初期表示
        Route::get('persons', [SmasterRegistController::class, 'index'])->name('personRegist.index');
        // 登録
        Route::post('persons', [SmasterRegistController::class, 'store'])->name('personRegist.store');

        // 生徒マスタ・修正
        // 初期表示
        Route::get('persons/{editSchCode}/{editPsnCode}', [SmasterCorrectionController::class, 'index'])->name('sch.personEdit.index');
        // 修正
        Route::put('persons/{editSchCode}/{editPsnCode}', [SmasterCorrectionController::class, 'update'])->name('sch.personEdit.update');
        // 削除
        Route::delete('persons/{editSchCode}/{delSchCode}', [SmasterCorrectionController::class, 'delete'])->name('personEdit.delete');

        // 生徒代理ログイン
        Route::post('persons/surrogate/login/{loginSchCode}/{loginPsnCode}', [SurrogateLoginController::class, 'personLogin'])->name('surrogateLogin.personLogin');
        // 生徒代理ログアウト
        Route::post('persons/surrogate/logout', [SurrogateLoginController::class, 'personLogout'])->name('surrogateLogin.personLogout');

        // 管理資料一覧
        Route::get('management/list', [ManagementListController::class, 'index'])->name('sch.management.index');
        // 検索
        Route::post('management/list', [ManagementListController::class, 'search'])->name('sch.management.search');

        // 管理資料画面
        Route::get('managementDoc', [ManagementDocController::class, 'index'])->name('sch.managementdoc.index');
        // pdfダウンロード
        Route::get('managementDoc/pdfDownload/{index}', [ManagementDocController::class, 'pdfDownload'])->name('sch.managementdoc.pdfDownload');
        // ダウンロード指示
        Route::get('managementDoc/order', [ManagementDocController::class, 'order'])->name('sch.managementdoc.order');
        // CSVダウンロード指示
        Route::get('managementDoc/orderCSV', [ManagementDocController::class, 'orderCSV'])->name('sch.managementdoc.orderCSV');
        // zipダウンロード
        Route::get('managementDoc/zipDownload', [ManagementDocController::class, 'zipDownload'])->name('sch.managementdoc.zipDownload');
        // zipダウンロードCSV
        Route::get('managementDoc/zipDownloadCSV', [ManagementDocController::class, 'zipDownloadCSV'])->name('sch.managementdoc.zipDownloadCSV');
        // 画面遷移
        Route::get('managementDoc/{index}', [ManagementDocController::class, 'open'])->name('sch.managementdoc.open');

        // 試験回数検索・一覧
        Route::get('testcountsearch/list', [TestCountSearchListController::class, 'index'])->name('sch.testcountsearch.index');
        // 検索
        Route::post('testcountsearch/list', [TestCountSearchListController::class, 'search'])->name('sch.testcountsearch.search');
        // 試験公開フラグ更新
        Route::post('testcountsearch/list/update', [TestCountSearchListController::class, 'updatePublishFlag'])->name('sch.testcountsearch.update');
        // 成績表一覧
        Route::get('testcountsearch/list/{index}', [ReportCardListController::class, 'index'])->name('sch.reportCard.index');
        // pdfダウンロード
        Route::get('testcountsearch/list/pdfDownload/{year?}/{cnttest?}/{grade?}/{oid?}', [ReportCardListController::class, 'pdfDownload'])->name('sch.reportCard.pdfDownload');

        // 年度回数一覧
        Route::get('ticket/list', [TicketSearchListController::class, 'index'])->name('sch.ticketList.index');
        // 検索
        Route::post('ticket/list', [TicketSearchListController::class, 'search'])->name('sch.ticketList.search');

        // 生徒志望校参照画面
        Route::get('ticket/detail/{index}', [TicketDetailController::class, 'index'])->name('sch.ticketDetail.index');
        // 教室選択
        Route::post('ticket/detail/{index}', [TicketDetailController::class, 'index'])->name('sch.ticketDetail.index');

        // CSVアップロード
        Route::get('csvupload', [CsvUploadController::class, 'index'])->name('csvUpload.index');
        Route::get('csvupload/upload', [CsvUploadController::class, 'index'])->name('csvUpload.err');
        Route::post('csvupload/upload', [CsvUploadController::class, 'upload'])->name('csvUpload.upload');

        // 教室用CSVアップロード
        Route::get('clscsvupload', [ClsCsvUploadController::class, 'index'])->name('ClsCsvUpload.index');
        Route::get('clscsvupload/upload', [ClsCsvUploadController::class, 'index'])->name('ClsCsvUpload.err');
        Route::post('clscsvupload/upload', [ClsCsvUploadController::class, 'upload'])->name('ClsCsvUpload.upload');

        // 受験番号CSVアップロード
        Route::get('exnumcsvupload', [ExamNumberCsvUploadController::class, 'index'])->name('ExNumCsvUpload.index');
        Route::get('exnumcsvupload/upload', [ExamNumberCsvUploadController::class, 'index'])->name('ExNumCsvUpload.err');
        Route::post('exnumcsvupload/upload', [ExamNumberCsvUploadController::class, 'upload'])->name('ExNumCsvUpload.upload');

        // 高校・大学CSV登録
        Route::get('code-csv-register', [UnivCsvUploadController::class, 'index'])->name('univ.register');
        Route::post('code-csv-upload', [UnivCsvUploadController::class, 'upload'])->name('univ.upload');
        Route::get('code-csv-err/{mode}', [UnivCsvUploadController::class, 'downloadErrCsv'])->name('univ.err_csv');

        // 大学コードデータ削除
        // 一覧
        Route::get('univ-code-list', [UnivCodeDeleteController::class, 'index'])->name('univ_code.list');
        Route::post('univ-code-list', [UnivCodeDeleteController::class, 'search'])->name('univ_code.search');
        // 削除
        Route::delete('univ-code-delete/{id}', [UnivCodeDeleteController::class, 'delete'])->name('univ_code.delete');

        // 模試データ管理
        // 一覧
        Route::get('test-list', [UnivTestListController::class, 'index'])->name('test.list');
        Route::post('test-list', [UnivTestListController::class, 'search'])->name('test.search');
        // 登録
        Route::get('test', [UnivTestRegisterController::class, 'register'])->name('test.register');
        Route::post('test-store', [UnivTestRegisterController::class, 'store'])->name('test.store');
        // 修正
        Route::get('test/{id}', [UnivTestEditController::class, 'edit'])->name('test.edit');
        Route::post('test-update/{id}', [UnivTestEditController::class, 'update'])->name('test.update');
        // 削除
        Route::delete('test-delete/{id}', [UnivTestEditController::class, 'delete'])->name('test.delete');

        //  生徒公開機能利用教室の公開フラグ確認用API
        Route::get('check_publish_flg', [Rest\TestCountSearchListAPIController::class, 'checkPublishFlg'])->name('check_publish_flg');

    });

    // 教室アカウント用ルート
    Route::prefix('/{schCode}/cls')->group(function () {

        // 教室アカウントログイン IDチェック
        Route::get('idcheck', [ClassIDLoginCheckController::class, 'index'])->name('classIdCheck.index')->middleware('ip_limitation');
        Route::post('idcheck', [ClassIDLoginCheckController::class, 'check'])->name('classIdCheck.check');

        // 教室アカウントログイン
        Route::get('login', [ClassLoginController::class, 'index'])->name('classLogin.index');
        // 教室アカウントログイン
        Route::post('login', [ClassLoginController::class, 'login'])->name('classLogin.login');
        // 教室アカウントログアウト
        Route::match(['get', 'post'], 'logout', [ClassLoginController::class, 'logout'])->name('classLogin.logout');

        // 教室アカウントログイン(仮パスワード）
        Route::get('tmplogin', [ClassTemporaryLoginController::class, 'index'])->name('classTempLogin.index');
        Route::post('tmplogin', [ClassTemporaryLoginController::class, 'login'])->name('classTempLogin.login');

        // パスワード登録画面
        Route::get('password', [ClassChangePasswordController::class, 'index'])->name('ClassChangePassword.index');
        Route::post('password', [ClassChangePasswordController::class, 'register'])->name('ClassChangePassword.register');

        // メニュー画面
        Route::get('menu', [TopMenuController::class, 'index'])->name('classTopMenu.index');

        // 生徒マスタ・一覧
        // 初期表示
        Route::get('persons/list', [SmasterListController::class, 'index'])->name('cls.persons.index');
        // 検索出力
        Route::post('persons/list', [SmasterListController::class, 'search'])->name('cls.persons.search');
        // CSV出力
        Route::post('persons/list/csv', [SmasterListController::class, 'downloadCsv'])->name('cls.persons.downloadCsv');

        // 生徒マスタ・修正
        // 初期表示
        Route::get('persons/{editSchCode}/{editPsnCode}', [SmasterCorrectionController::class, 'index'])->name('cls.personEdit.index');
        // 修正
        Route::put('persons/{editSchCode}/{editPsnCode}', [SmasterCorrectionController::class, 'update'])->name('cls.personEdit.update');

        // 管理資料一覧
        Route::get('management/list', [ManagementListController::class, 'index'])->name('cls.management.index');
        // 検索
        Route::post('management/list', [ManagementListController::class, 'search'])->name('cls.management.search');

        // 管理資料画面
        Route::get('managementDoc', [ManagementDocController::class, 'index'])->name('cls.managementdoc.index');
        // pdfダウンロード
        Route::get('managementDoc/pdfDownload/{index}', [ManagementDocController::class, 'pdfDownload'])->name('cls.managementdoc.pdfDownload');
        // ダウンロード指示
        Route::get('managementDoc/order', [ManagementDocController::class, 'order'])->name('cls.managementdoc.order');
        // CSVダウンロード指示
        Route::get('managementDoc/orderCSV', [ManagementDocController::class, 'orderCSV'])->name('cls.managementdoc.orderCSV');
        // zipダウンロード
        Route::get('managementDoc/zipDownload', [ManagementDocController::class, 'zipDownload'])->name('cls.managementdoc.zipDownload');
        // zipダウンロードCSV
        Route::get('managementDoc/zipDownloadCSV', [ManagementDocController::class, 'zipDownloadCSV'])->name('cls.managementdoc.zipDownloadCSV');
        // 画面遷移
        Route::get('managementDoc/{index}', [ManagementDocController::class, 'open'])->name('cls.managementdoc.open');

        // 試験回数検索・一覧
        Route::get('testcountsearch/list', [TestCountSearchListController::class, 'index'])->name('cls.testcountsearch.index');
        // 検索
        Route::post('testcountsearch/list', [TestCountSearchListController::class, 'search'])->name('cls.testcountsearch.search');

        // 成績表一覧
        Route::get('testcountsearch/list/{index}', [ReportCardListController::class, 'index'])->name('cls.reportCard.index');
        // pdfダウンロード
        Route::get('testcountsearch/pdfDownload/{year?}/{cnttest?}/{grade?}/{oid?}', [ReportCardListController::class, 'pdfDownload'])->name('cls.reportCard.pdfDownload');

        // 年度回数一覧
        Route::get('ticket/list', [TicketSearchListController::class, 'index'])->name('cls.ticketList.index');
        // 検索
        Route::post('ticket/list', [TicketSearchListController::class, 'search'])->name('cls.ticketList.search');

        // 生徒志望校参照画面
        Route::get('ticket/detail/{index}', [TicketDetailController::class, 'index'])->name('cls.ticketDetail.index');

    });

    // 塾生アカウント用ルート
    Route::prefix('/{schCode}/psn')->group(function () {

        // 生徒アカウントログイン IDチェック
        Route::get('idcheck', [PersonIDLoginCheckController::class, 'index'])->name('personIdCheck.index');
        Route::post('idcheck', [PersonIDLoginCheckController::class, 'check'])->name('personIdCheck.check');

        // 生徒アカウントログイン SSO 認証
        Route::post('ssocheck', [PersonLoginController::class, 'ssologin'])->name('personLogin.ssologin');

        // 生徒アカウントログイン(本パスワード）
        Route::get('login', [PersonLoginController::class, 'index'])->name('personLogin.index');
        Route::post('login', [PersonLoginController::class, 'login'])->name('personLogin.login');
        Route::match(['get', 'post'], 'logout', [PersonLoginController::class, 'logout'])->name('personLogin.logout');
        Route::post('passwordforgot', [PersonLoginController::class, 'passwordResetIndex'])->name('personLogin.passwordreset');

        // 生徒アカウントログイン(仮パスワード）
        Route::get('tmplogin', [PersonTemporaryLoginController::class, 'index'])->name('personTempLogin.index');
        Route::post('tmplogin', [PersonTemporaryLoginController::class, 'login'])->name('personTempLogin.login');
        Route::post('tmplogin/unknown', [PersonTemporaryLoginController::class, 'unknownAuth'])->name('personTempLogin.unknownauth');

        // パスワード登録画面
        Route::get('password', [ChangePasswordController::class, 'index'])->name('changePassword.index');
        Route::post('password', [ChangePasswordController::class, 'register'])->name('changePassword.register');

        // 生徒アカウントログイン(仮パスワード不明）
        Route::get('unknownauth', [PersonUnknownTempPassLoginController::class, 'index'])->name('personUnknownLogin.index');
        Route::post('unknownauth', [PersonUnknownTempPassLoginController::class, 'login'])->name('personUnknownLogin.login');

        // 生徒パスワード再設定
        Route::get('passwordreset', [PersonPasswordResetController::class, 'index'])->name('personPasswordReset.index');
        Route::post('passwordreset', [PersonPasswordResetController::class, 'reset'])->name('personPasswordReset.reset');

        // 生徒パスワード変更
        Route::get('passwordChange', [PersonPasswordChangeController::class, 'index'])->name('personPasswordChange.index');
        Route::post('passwordChange', [PersonPasswordChangeController::class, 'changePassword'])->name('personPasswordChange.update');

        // 生徒メールアドレス変更
        Route::get('mailChange', [PersonMailChangeController::class, 'index'])->name('personMailChange.index');
        Route::post('mailChange', [PersonMailChangeController::class, 'changeMail'])->name('personMailChange.update');

        // メニュー画面
        Route::get('menu', [TopMenuController::class, 'index'])->name('personTopMenu.index');

        // 塾生用 成績表一覧
        Route::get('reportcard/list/', [PersonReportCardListController::class, 'index'])->name('personReportCardList.index');
        // pdfダウンロード
        Route::get('reportcard/pdfDownload/{index}/{category_kbn}', [PersonReportCardListController::class, 'pdfDownload'])->name('personReportCardList.pdfDownload');

        // 年度回数一覧
        Route::get('ticket/list', [TicketSearchListController::class, 'index'])->name('psn.ticketList.index');
        // 検索
        Route::post('ticket/list', [TicketSearchListController::class, 'search'])->name('psn.ticketList.search');

        // 高校および志望校登録画面
        Route::get('ticket/regist/{index}', [TicketRegistController::class, 'index'])->name('ticketRegist.index');
        // 高校リスト
        Route::get('ticket/hsclist', [TicketRegistController::class, 'hsclist'])->name('ticketRegist.hsclist');
        // 大学リスト
        Route::get('ticket/univlist', [TicketRegistController::class, 'univlist'])->name('ticketRegist.univlist');
        // 学部リスト
        Route::get('ticket/fcllist', [TicketRegistController::class, 'fcllist'])->name('ticketRegist.fcllist');
        // 学科リスト
        Route::get('ticket/dptlist', [TicketRegistController::class, 'dptlist'])->name('ticketRegist.dptlist');

        // 高校および志望校登録
        Route::post('ticket/store/{index}', [TicketRegistController::class, 'store'])->name('ticketRegist.store');

    });

});

// ポータル
Route::get('/', [PortalTopController::class, 'index'])->name('PortalTop.index');
Route::prefix('/account')->group(function () {
    Route::get('/login/{account}', [PortalTopController::class, 'account'])->name('PortalAccount.account');
    Route::post('/login/{account}', [PortalTopController::class, 'check'])->name('PortalAccount.check');
});

// sessionは反映しない (個人アクセス)
Route::get('/univ-search', [UnivCodeSearchIndexController::class, 'index'])->name('univ.search');
