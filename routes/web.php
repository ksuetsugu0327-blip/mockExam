<?php

Route::group(['middleware' => 'auth.very_basic'], function() {

    // ■個人情報保護方針■
    Route::get('/{path}praivacy', function(){
        return File::get(public_path('/static/praivacy/praivacy.html'));
    })->where('path','.*');

//    Route::view('/{path}praivacy', 'praivacy')->where('path','.*');

// 塾アカウント用ルート
Route::prefix('/{schCode}/sch')->group(function(){

    // 塾アカウントログイン IDチェック
    Route::get('idcheck','SchoolIDLoginCheckController@index')->name('schoolIdCheck.index')->middleware('ip_limitation');
    Route::post('idcheck','SchoolIDLoginCheckController@check')->name('schoolIdCheck.check');

    // 塾アカウントログイン
    Route::get('login','SchoolLoginController@index')->name('schoolLogin.index');
    // 塾アカウントログイン
    Route::post('login','SchoolLoginController@login')->name('schoolLogin.login');
    // 塾アカウントログアウト
    Route::match(['get', 'post'], 'logout', 'SchoolLoginController@logout')->name('schoolLogin.logout');

    // メニュー画面
    Route::get('menu','TopMenuController@index')->name('schoolTopMenu.index');

    // 塾マスタ・一覧
    // 初期表示
    Route::get('schools/list', 'JmasterListController@index')->name('schools.index');
    // 検索
    Route::post('schools/list', 'JmasterListController@search')->name('schools.search');
    // 削除
    Route::delete('schools/list/{delSchCode}', 'JmasterListController@delete')->name('schools.delete');

    // 塾マスタ・登録
    // 初期表示
    Route::get('schools', 'JmasterRegistController@index')->name('schoolRegist.index');
    // 登録
    Route::post('schools', 'JmasterRegistController@store')->name('schoolRegist.store');

    // 塾マスタ・修正
    // 初期表示
    Route::get('schools/{editSchCode}', 'JmasterCorrectionController@index')->name('schoolEdit.index');
    // 修正
    Route::put('schools/{editSchCode}', 'JmasterCorrectionController@update')->name('schoolEdit.update');
    // 削除
    Route::delete('schools/{delSchCode}', 'JmasterCorrectionController@delete')->name('schoolEdit.delete');

    // 塾代理ログイン
    Route::post('schools/surrogate/login/{loginSchCode}', 'SurrogateLoginController@schoolLogin')->name('surrogateLogin.schoolLogin');
    // 塾代理ログアウト
    Route::post('schools/surrogate/logout', 'SurrogateLoginController@schoolLogout')->name('surrogateLogin.schoolLogout');

    // 教室マスタ・一覧
    // 初期表示
    Route::get('classes/list', 'KmasterListController@index')->name('classes.index');
    // 検索
    Route::post('classes/list', 'KmasterListController@search')->name('classes.search');
    // 削除
    Route::delete('classes/list/{delSchCode}/{delClsCode}', 'KmasterListController@delete')->name('classes.delete');
    // CSV出力
    Route::post('classes/list/csv', 'KmasterListController@downloadCsv')->name('sch.classes.downloadCsv');

    // 教室マスタ・登録
    // 初期表示
    Route::get('classes', 'KmasterRegistController@index')->name('classRegist.index');
    // 登録
    Route::post('classes', 'KmasterRegistController@store')->name('classRegist.store');

    // 教室マスタ・修正
    // 初期表示
    Route::get('classes/{editSchCode}/{editClsCode}','KmasterCorrectionController@index')->name('classEdit.index');
    // 修正
    Route::put('classes/{editSchCode}/{editClsCode}','KmasterCorrectionController@update')->name('classEdit.update');
    // 削除
    Route::delete('classes/{delSchCode}/{delClsCode}','KmasterCorrectionController@delete')->name('classEdit.delete');

    // 教室代理ログイン
    Route::post('classes/surrogate/login/{loginSchCode}/{loginClsCode}', 'SurrogateLoginController@classLogin')->name('surrogateLogin.classLogin');
    // 教室代理ログアウト
    Route::post('classes/surrogate/logout', 'SurrogateLoginController@classLogout')->name('surrogateLogin.classLogout');

    // 生徒マスタ・一覧
    // 初期表示
    Route::get('persons/list', 'SmasterListController@index')->name('sch.persons.index');
    // 検索出力
    Route::post('persons/list', 'SmasterListController@search')->name('sch.persons.search');
    // CSV出力
    Route::post('persons/list/csv', 'SmasterListController@downloadCsv')->name('sch.persons.downloadCsv');
    // 削除
    Route::delete('persons/list/{delSchCode}/{delPsnCode}', 'SmasterListController@delete')->name('persons.delete');

    // 生徒マスタ・登録
    // 初期表示
    Route::get('persons', 'SmasterRegistController@index')->name('personRegist.index');
    // 登録
    Route::post('persons', 'SmasterRegistController@store')->name('personRegist.store');

    // 生徒マスタ・修正
    // 初期表示
    Route::get('persons/{editSchCode}/{editPsnCode}', 'SmasterCorrectionController@index')->name('sch.personEdit.index');
    // 修正
    Route::put('persons/{editSchCode}/{editPsnCode}', 'SmasterCorrectionController@update')->name('sch.personEdit.update');
    // 削除
    Route::delete('persons/{editSchCode}/{delSchCode}', 'SmasterCorrectionController@delete')->name('personEdit.delete');

    // 生徒代理ログイン
    Route::post('persons/surrogate/login/{loginSchCode}/{loginPsnCode}', 'SurrogateLoginController@personLogin')->name('surrogateLogin.personLogin');
    // 生徒代理ログアウト
    Route::post('persons/surrogate/logout', 'SurrogateLoginController@personLogout')->name('surrogateLogin.personLogout');

    // 管理資料一覧
    Route::get('management/list', 'ManagementListController@index')->name('sch.management.index');
    // 検索
    Route::post('management/list','ManagementListController@search')->name('sch.management.search');

    // 管理資料画面
    Route::get('managementDoc', 'ManagementDocController@index')->name('sch.managementdoc.index');
    // pdfダウンロード
    Route::get('managementDoc/pdfDownload/{index}','ManagementDocController@pdfDownload')->name('sch.managementdoc.pdfDownload');
    // ダウンロード指示
    Route::get('managementDoc/order','ManagementDocController@order')->name('sch.managementdoc.order');
    // CSVダウンロード指示
    Route::get('managementDoc/orderCSV','ManagementDocController@orderCSV')->name('sch.managementdoc.orderCSV');
    // zipダウンロード
    Route::get('managementDoc/zipDownload','ManagementDocController@zipDownload')->name('sch.managementdoc.zipDownload');
    // zipダウンロードCSV
    Route::get('managementDoc/zipDownloadCSV','ManagementDocController@zipDownloadCSV')->name('sch.managementdoc.zipDownloadCSV');
    // 画面遷移
    Route::get('managementDoc/{index}', 'ManagementDocController@open')->name('sch.managementdoc.open');

    // 試験回数検索・一覧
    Route::get('testcountsearch/list', 'TestCountSearchListController@index')->name('sch.testcountsearch.index');
    // 検索
    Route::post('testcountsearch/list','TestCountSearchListController@search')->name('sch.testcountsearch.search');

    // 成績表一覧
    Route::get('testcountsearch/list/{index}','ReportCardListController@index')->name('sch.reportCard.index');
    // pdfダウンロード
    Route::get('testcountsearch/list/pdfDownload/{year?}/{cnttest?}/{grade?}/{oid?}','ReportCardListController@pdfDownload')->name('sch.reportCard.pdfDownload');

    // 年度回数一覧
    Route::get('ticket/list','TicketSearchListController@index')->name('sch.ticketList.index');
    // 検索
    Route::post('ticket/list','TicketSearchListController@search')->name('sch.ticketList.search');

    // 生徒志望校参照画面
    Route::get('ticket/detail/{index}','TicketDetailController@index')->name('sch.ticketDetail.index');
    // 教室選択
    Route::post('ticket/detail/{index}','TicketDetailController@index')->name('sch.ticketDetail.index');

    //CSVアップロード
    Route::get('csvupload', 'CsvUploadController@index')->name('csvUpload.index');
    Route::get('csvupload/upload', 'CsvUploadController@index')->name('csvUpload.err');
    Route::post('csvupload/upload', 'CsvUploadController@upload')->name('csvUpload.upload');

    //教室用CSVアップロード
    Route::get('clscsvupload', 'ClsCsvUploadController@index')->name('ClsCsvUpload.index');
    Route::get('clscsvupload/upload', 'ClsCsvUploadController@index')->name('ClsCsvUpload.err');
    Route::post('clscsvupload/upload', 'ClsCsvUploadController@upload')->name('ClsCsvUpload.upload');

    //受験番号CSVアップロード
    Route::get('exnumcsvupload', 'ExamNumberCsvUploadController@index')->name('ExNumCsvUpload.index');
    Route::get('exnumcsvupload/upload', 'ExamNumberCsvUploadController@index')->name('ExNumCsvUpload.err');
    Route::post('exnumcsvupload/upload', 'ExamNumberCsvUploadController@upload')->name('ExNumCsvUpload.upload');

    //高校・大学CSV登録
    Route::get('code-csv-register', 'UnivCsvUploadController@index')->name('univ.register');
    Route::post('code-csv-upload', 'UnivCsvUploadController@upload')->name('univ.upload');
    Route::get('code-csv-err/{mode}', 'UnivCsvUploadController@downloadErrCsv')->name('univ.err_csv');

    Route::get('code-csv-delete', function(){
        return view('univ_delete');
    })->name('univ.delete');
    
    //模試データ管理
        Route::get('test-list', 'UnivTestController@index')->name('test.list');
    
        Route::get('test-details', function(){
            return view('test_details');
        })->name('test.details');

});

// 教室アカウント用ルート
Route::prefix('/{schCode}/cls')->group(function(){

    // 教室アカウントログイン IDチェック
    Route::get('idcheck','ClassIDLoginCheckController@index')->name('classIdCheck.index')->middleware('ip_limitation');
    Route::post('idcheck','ClassIDLoginCheckController@check')->name('classIdCheck.check');

    // 教室アカウントログイン
    Route::get('login','ClassLoginController@index')->name('classLogin.index');
    // 教室アカウントログイン
    Route::post('login','ClassLoginController@login')->name('classLogin.login');
    // 教室アカウントログアウト
    Route::match(['get', 'post'], 'logout', 'ClassLoginController@logout')->name('classLogin.logout');

    // 教室アカウントログイン(仮パスワード）
    Route::get('tmplogin','ClassTemporaryLoginController@index')->name('classTempLogin.index');
    Route::post('tmplogin','ClassTemporaryLoginController@login')->name('classTempLogin.login');

    // パスワード登録画面
    Route::get('password','ClassChangePasswordController@index')->name('ClassChangePassword.index');
    Route::post('password','ClassChangePasswordController@register')->name('ClassChangePassword.register');

    // メニュー画面
    Route::get('menu','TopMenuController@index')->name('classTopMenu.index');

    // 生徒マスタ・一覧
    // 初期表示
    Route::get('persons/list', 'SmasterListController@index')->name('cls.persons.index');
    // 検索出力
    Route::post('persons/list', 'SmasterListController@search')->name('cls.persons.search');
    // CSV出力
    Route::post('persons/list/csv', 'SmasterListController@downloadCsv')->name('cls.persons.downloadCsv');

    // 生徒マスタ・修正
    // 初期表示
    Route::get('persons/{editSchCode}/{editPsnCode}', 'SmasterCorrectionController@index')->name('cls.personEdit.index');
    // 修正
    Route::put('persons/{editSchCode}/{editPsnCode}', 'SmasterCorrectionController@update')->name('cls.personEdit.update');

    // 管理資料一覧
    Route::get('management/list', 'ManagementListController@index')->name('cls.management.index');
    // 検索
    Route::post('management/list','ManagementListController@search')->name('cls.management.search');

    // 管理資料画面
    Route::get('managementDoc', 'ManagementDocController@index')->name('cls.managementdoc.index');
    // pdfダウンロード
    Route::get('managementDoc/pdfDownload/{index}','ManagementDocController@pdfDownload')->name('cls.managementdoc.pdfDownload');
    // ダウンロード指示
    Route::get('managementDoc/order','ManagementDocController@order')->name('cls.managementdoc.order');
    // CSVダウンロード指示
    Route::get('managementDoc/orderCSV','ManagementDocController@orderCSV')->name('cls.managementdoc.orderCSV');
    // zipダウンロード
    Route::get('managementDoc/zipDownload','ManagementDocController@zipDownload')->name('cls.managementdoc.zipDownload');
    // zipダウンロードCSV
    Route::get('managementDoc/zipDownloadCSV','ManagementDocController@zipDownloadCSV')->name('cls.managementdoc.zipDownloadCSV');
    // 画面遷移
    Route::get('managementDoc/{index}', 'ManagementDocController@open')->name('cls.managementdoc.open');

    // 試験回数検索・一覧
    Route::get('testcountsearch/list', 'TestCountSearchListController@index')->name('cls.testcountsearch.index');
    // 検索
    Route::post('testcountsearch/list','TestCountSearchListController@search')->name('cls.testcountsearch.search');

    // 成績表一覧
    Route::get('testcountsearch/list/{index}','ReportCardListController@index')->name('cls.reportCard.index');
    // pdfダウンロード
    Route::get('testcountsearch/pdfDownload/{year?}/{cnttest?}/{grade?}/{oid?}','ReportCardListController@pdfDownload')->name('cls.reportCard.pdfDownload');

    // 年度回数一覧
    Route::get('ticket/list','TicketSearchListController@index')->name('cls.ticketList.index');
    // 検索
    Route::post('ticket/list','TicketSearchListController@search')->name('cls.ticketList.search');

    // 生徒志望校参照画面
    Route::get('ticket/detail/{index}','TicketDetailController@index')->name('cls.ticketDetail.index');

});

// 塾生アカウント用ルート
Route::prefix('/{schCode}/psn')->group(function(){

    // 生徒アカウントログイン IDチェック
    Route::get('idcheck','PersonIDLoginCheckController@index')->name('personIdCheck.index');
    Route::post('idcheck','PersonIDLoginCheckController@check')->name('personIdCheck.check');

    // 生徒アカウントログイン SSO 認証
    Route::post('ssocheck','PersonLoginController@ssologin')->name('personLogin.ssologin');

    // 生徒アカウントログイン(本パスワード）
    Route::get('login','PersonLoginController@index')->name('personLogin.index');
    Route::post('login','PersonLoginController@login')->name('personLogin.login');
    Route::match(['get', 'post'], 'logout', 'PersonLoginController@logout')->name('personLogin.logout');

    // 生徒アカウントログイン(仮パスワード）
    Route::get('tmplogin','PersonTemporaryLoginController@index')->name('personTempLogin.index');
    Route::post('tmplogin','PersonTemporaryLoginController@login')->name('personTempLogin.login');
    Route::post('tmplogin/unknown','PersonTemporaryLoginController@unknownAuth')->name('personTempLogin.unknownauth');

    // パスワード登録画面
    Route::get('password','ChangePasswordController@index')->name('changePassword.index');
    Route::post('password','ChangePasswordController@register')->name('changePassword.register');

    // 生徒アカウントログイン(仮パスワード不明）
    Route::get('unknownauth','PersonUnknownTempPassLoginController@index')->name('personUnknownLogin.index');
    Route::post('unknownauth','PersonUnknownTempPassLoginController@login')->name('personUnknownLogin.login');

    // メニュー画面
    Route::get('menu','TopMenuController@index')->name('personTopMenu.index');

    // 塾生用 成績表一覧
    Route::get('reportcard/list/','PersonReportCardListController@index')->name('personReportCardList.index');
    // pdfダウンロード
    Route::get('reportcard/pdfDownload/{index}/{category_kbn}','PersonReportCardListController@pdfDownload')->name('personReportCardList.pdfDownload');

    // 年度回数一覧
    Route::get('ticket/list','TicketSearchListController@index')->name('psn.ticketList.index');
    // 検索
    Route::post('ticket/list','TicketSearchListController@search')->name('psn.ticketList.search');

    // 高校および志望校登録画面
    Route::get('ticket/regist/{index}','TicketRegistController@index')->name('ticketRegist.index');
    // 高校リスト
    Route::get('ticket/hsclist','TicketRegistController@hsclist')->name('ticketRegist.hsclist');
    // 大学リスト
    Route::get('ticket/univlist','TicketRegistController@univlist')->name('ticketRegist.univlist');
    // 学部リスト
    Route::get('ticket/fcllist','TicketRegistController@fcllist')->name('ticketRegist.fcllist');
    // 学科リスト
    Route::get('ticket/dptlist','TicketRegistController@dptlist')->name('ticketRegist.dptlist');

    // 高校および志望校登録
    Route::post('ticket/store/{index}','TicketRegistController@store')->name('ticketRegist.store');

});

});

//ポータル
Route::get('/', 'PortalTopController@index')->name('PortalTop.index');
Route::prefix('/account')->group(function () {
    Route::get('/login/{account}', 'PortalTopController@account')->name('PortalAccount.account');
    Route::post('/login/{account}', 'PortalTopController@check')->name('PortalAccount.check');
});

 //sessionは反映しない (個人アクセス)
Route::get('univ-search', function(){
    return view('univ_search');
})->name('univ.search');

