<?php

/*
 * 定数クラスです。
 *
 */
return[
    //APP_KEY
    'APP_KEY' => env('APP_KEY'),
    // レコード区分_塾名
    'NAME_KBN_SCH_NAME' => 12,
    // 発行区分
    'CATEGORY_KBN_SCH' => 'sch',
    'CATEGORY_KBN_CLS' => 'cls',
    'CATEGORY_KBN_CSV' => 'csv',
    // パス区分
    'PATH_KBN_DIRECTORY' => '1',
    'PATH_KBN_FILE' => '2',

    // ファイル区分
    'FILE_KBN_LS1' => 'ls1',    // 会場内成績一覧（科目編）
    'FILE_KBN_LS2' => 'ls2',    // 会場内成績一覧（総合）
    'FILE_KBN_LC1' => 'lc1',    // 教室内成績一覧（科目編）
    'FILE_KBN_LC2' => 'lc2',    // 教室内成績一覧（総合）
    'FILE_KBN_SBJ' => 'sbj',    // 科目別学力分析表
    'FILE_KBN_CLS' => 'cls',    // 教室別成績比較表
    'FILE_KBN_DAT' => 'dat',    // データ集
    'FILE_KBN_PSN' => 'psn',    // 個人成績表
    'FILE_KBN_CMT' => 'cmt',    // 解答解説
    'FILE_KBN_CSV' => 'csv',    // CSV一括ダウンロード

    // 回数コンバートマスタ
    'KAIA010_KBN_MARK_INPUT' => '9',
    'KAIA020_KBN_MARK_INPUT' => 7,
    'CNTTEST_KBN_MARK' => 1,
    'CNTTEST_KBN_INPUT' => 2,
    'CNTTEST_KBN_DATE' => 3,
    // ファイル転送プロトコル
    'FILESYSTEM_PROTOCOL' => 'ftp',

    // ガード名   ADMIN:管理者アカウント SCHOOL:塾アカウント CLASS:教室アカウント PERSON:生徒アカウント
    'GUARD_ADMIN' => 'ADMIN',
    'GUARD_SCHOOL' => 'SCHOOL',
    'GUARD_CLASS' => 'CLASS',
    'GUARD_PERSON' => 'PERSON',
    // 代理ログイン用ガード（ガードとして実際の定義は存在しません。処理上のガードです）
    // ADMIN_SCHOOL:代理塾  ADMIN_CLASS:代理教室 ADMIN_PERSON:代理生徒
    'GUARD_SURROGATE_SCHOOL' => 'ADMIN_SCHOOL',
    'GUARD_SURROGATE_CLASS' => 'ADMIN_CLASS',
    'GUARD_SURROGATE_PERSON' => 'ADMIN_PERSON',

    // セッションキー
    // ログイン用
    // 塾コード
    'SESSION_KEY_SCHCODE' => 'SCH_CODE',
    // 塾名
    'SESSION_KEY_SCHNAME' => 'SCH_NAME',
    // 教室コード
    'SESSION_KEY_CLSCODE' => 'CLS_CODE',
    // 教室名
    'SESSION_KEY_CLSNAME' => 'CLS_NAME',
    // 試験名
    'SESSION_KEY_SCHTESTNAME' => 'TESTNAME',
    // 管理ユーザ区分
    'SESSION_KEY_SCHADMINKBN' => 'ADMIN_KBN',
    // 生徒機能非表示フラグ
    'SESSION_KEY_PSN_HIDE_FLG' => 'PSN_HIDE_FLG',
    // 塾名非表示フラグ
    'SESSION_KEY_SCH_NAME_HIDE_FLG' => 'SCH_NAME_HIDE_FLG',
    // 個人登録票利用フラグ
    'SESSION_KEY_PSN_CARD_REGIST_FLG' => 'PSN_CARD_REGIST_FLG',
    // 生徒パスワード変更可能フラグ
    'SESSION_KEY_PSN_PSW_CHANGE_FLG' => 'PSN_PSW_CHANGE_FLG',
    // 教室成績データCSVダウンロード可能フラグ
    'SESSION_KEY_CLS_GRADE_CSV_FLG' => 'CLS_GRADE_CSV_FLG',
    // 塾生コード
    'SESSION_KEY_PSNCODE' => 'PSN_CODE',
    // 塾生氏名
    'SESSION_KEY_PSNNAME' => 'PSN_NAME',
    // 塾生INFO
    'SESSION_KEY_PSNINFO' => 'PSN_INFO',

    // 塾マスタ一覧用
    'SESSION_KEY_MSCHOOL_SEARCH_CONDITION' => 'MSCHOOL_SEARCH_CONDITION',

    // 生徒マスタ一覧用
    'SESSION_KEY_MPERSON_SEARCH_CONDITION' => 'MPERSON_SEARCH_CONDITION',
    // 検索結果一覧リスト
    'SESSION_KEY_MPERSON_LIST' => 'MPERSON_LIST',

    //教室マスタ一覧
    'SESSION_KEY_MCLASS_SEARCH_CONDITION' => 'MCLASS_SEARCH_CONDITION',

    // 代理ログイン用
    // 代理ログイン済み区分
    'SESSION_KEY_IS_SURROGATE_LOGINED' => 'IS_SURROGATE_LOGINED',
    // 塾用
    'SESSION_KEY_SURROGATE_LOGIN_SCHOOL' => 'SURROGATE_LOGIN_SCHOOL',
    // 教室用
    'SESSION_KEY_SURROGATE_LOGIN_CLASS' => 'SURROGATE_LOGIN_CLASS',
    // 個人用
    'SESSION_KEY_SURROGATE_LOGIN_PERSON' => 'SURROGATE_LOGIN_PERSON',
    // 代理ログインログアウト区分
    'SESSION_KEY_IS_SURROGATE_LOGOUT' => 'IS_SURROGATE_LOGOUT',

    // 管理資料一覧用
    // 検索フラグ (true:検索実施済み )
    'SESSION_KEY_MANAGEMENT_CONDITION' => 'MANAGEMENT_SEARCH_CONDITION',
    // 検索結果一覧
    'SESSION_KEY_MANAGEMENT_LIST' => 'MANAGEMENT_LIST',

    // 管理資料ダウンロード画面
    'SESSION_KEY_MANAGEMENTDOC_LIST' => 'MANAGEMENTDOC_LIST',

    // 試験回数一覧用
    // 検索フラグ (true:検索実施済み )
    'SESSION_KEY_TESTCOUNT_SEARCH_CONDITION' => 'MANAGEMENT_SEARCH_CONDITION',
    // 検索結果一覧リスト
    'SESSION_KEY_TESTCOUNT_LIST' => 'TESTCOUNT_LIST',

    // 成績表一覧
    // 成績表一覧リスト
    'SESSION_KEY_REPORTCARD_LIST' => 'REPORTCARD_LIST',

    // 志望校登録
    // 検索フラグ (true:検索実施済み )
    'SESSION_KEY_TICKET_SEARCH_CONDITION' => 'TICKET_SEARCH_CONDITION',
    // 検索結果一覧リスト
    'SESSION_KEY_TICKET_LIST' => 'TICKET_LIST',

    // 模試データ管理
    // 模擬試験一覧用
    'SESSION_KEY_TTESTINFO_SEARCH_CONDITION' => 'TTESTINFO_SEARCH_CONDITION',

    // 大学コードデータ削除
    // 大学コードデータ削除一覧用
    'SESSION_KEY_UNIVCODE_SEARCH_CONDITION' => 'UNIVCODE_SEARCH_CONDITION',

    // 処理結果用
    'RESULT_CODE_SYSTEM_ERROR' => -1,
    'RESULT_CODE_BUSINESS_ERROR' => 0,
    'RESULT_CODE_SUCCESS' => 1,
    'RESULT_CODE_CONFLICT_ERROR' => -2,

    // 区分値
    // 管理ユーザ区分 0:一般 1:管理者
    'ADMIN_KBN_GENERAL' => 0,
    'ADMIN_KBN_ADMIN' => 1,
    // 生徒機能表示フラグ 0:表示 1:非表示
    'PSN_NO_HIDE_FLG'       => '0',
    'PSN_HIDE_FLG'          => '1',
    // 塾名表示フラグ 0:表示 1:非表示
    'SCH_NAME_NO_HIDE_FLG'  => '0',
    'SCH_NAME_HIDE_FLG'     => '1',
    // 個人登録票利用フラグ　0:非表示 1:表示
    'PSN_CARD_HIDE_FLG' => '0',
    'PSN_CARD_NO_HIDE_FLG' => '1',
    // 生徒パスワード変更可能フラグ　0:利用しない 1:利用する
    'PSN_CHANGE_PW_HIDE_FLG' => '0',
    'PSN_CHANGE_PW_NO_HIDE_FLG' => '1',
    // 教室成績データCSVダウンロード可能フラグ　0:利用しない 1:利用する
    'CLS_GRADE_CSV_NOT_USE_FLG' => '0',
    'CLS_GRADE_CSV_USE_FLG' => '1',
    // テスト回数表示区分  0:マーク・記述毎 1:マーク・記述通算 2:塾マスタ(回数毎)をもとに回数表示
    'CNTTEST_KBN_BY_TEST' => 0,
    'CNTTEST_KBN_SUMUP' => 1,
    'CNTTEST_KBN_MASTER' => 2,
    'CNTTEST_KBN_NO_OUTPUT' => 99,
    // 検索区分
    'SEARCHED' => 1,
    // 代理ログイン済み区分
    'SURROGATE_LOGINED' => 1,

    // 定数値
    // 代理ログイン用冠詞値
    'PREFIX_SURROGATE_LOGIN' => 'SURROGATE_LOGIN_',

    // 生徒マスタ ダウンロードCSVファイル名
    'PERSON_DOWNLOAD_CSV_FILENAME' => 'personList.csv',

    // 生徒マスタ ダウンロードCSVファイル名
    'PERSON_IMPORT_CSV_ERRFILENAME' => 'person_registration_error.csv',

    // 大学コード　インポートCSVエラーファイル名
    'UNIV_CODE_IMPORT_CSV_ERRFILENAME' => 'university_code_registration_error.csv',

    // 高校コード　インポートCSVエラーファイル名
    'HIGH_SCHOOL_CODE_IMPORT_CSV_ERRFILENAME' => 'high_school_code_registration_error.csv',

    // 生徒マスタCSV登録一回コミットにあたってコミットするレコード数
    'PSN_IMP_COMMIT_COUNT' => 10000,

    // 大学コードCSV登録一回コミットにあたってコミットするレコード数
    'UNVC_IMP_COMMIT_COUNT' => 10000,

    // 明細最大表示件数
    'VIEW_RECORD_COUNT' => 10000,

    // CSVインポート最大許容行数
    'CSV_IMPORT_MAX_LINE' => 50,

    // CSVインポート時間制限（秒単位）
    'PERSON_IMPORT_CSV_TIME_LIMIT' => 900,

    //学年
    'PSN_IMP_COMMIT_COUNT' => 1000,

    // CSVのヘッダーの順序
    'CSV_HEADER_NUM' => [
        'SCH_CODE' => [
            'NAME' => 'sch_code',
            'INDEX' => 0,
        ],
        'PSN_CODE' => [
            'NAME' => 'psn_code',
            'INDEX' => 1,
        ],
        'CLS_CODE' => [
            'NAME' => 'cls_code',
            'INDEX' => 2,
        ],
		'NAME' => [
            'NAME' => 'name',
            'INDEX' => 3,
        ],
        'BIRTHLOW' => [
            'NAME' => 'birthlow4',
            'INDEX' => 4,
        ],
        'POSTALLLOW' => [
            'NAME' => 'postallow4',
            'INDEX' => 5,
        ],
        'TELLOW' => [
            'NAME' => 'tellow4',
            'INDEX' => 6,
        ],
        'MAIL_ADDRESS' => [
            'NAME' => 'mail_address',
            'INDEX' => 7
        ],
         'PASSWORD' => [
            'NAME' => 'password',
            'INDEX' => 8,
         ],
        'TEMPPASS' => [
            'NAME' => 'temppassword',
            'INDEX' => 9,
        ],
    ],


    // 大学用CSVのヘッダーの順序
    'UNIV_CSV_HEADER' => [
        'UNIV_CODE' => [
            'NAME' => '大学コード',
            'INDEX' => 0,
        ],
        'PREF_CODE' => [
            'NAME' => '都道府県コード',
            'INDEX' => 1,
        ],
        'INSTALL_CODE' => [
            'NAME' => '設置区分コード',
            'INDEX' => 2,
        ],
        'DEPT_LINE_CODE1' => [
            'NAME' => '学部系統コード1',
            'INDEX' => 3,
        ],
        'DEPT_LINE_CODE2' => [
            'NAME' => '学部系統コード2',
            'INDEX' => 4,
        ],
        'DEPT_LINE_CODE3' => [
            'NAME' => '学部系統コード3',
            'INDEX' => 5,
        ],
        'DEPT_LINE_CODE4' => [
            'NAME' => '学部系統コード4',
            'INDEX' => 6,
        ],
        'DEPT_LINE_CODE5' => [
            'NAME' => '学部系統コード5',
            'INDEX' => 7,
        ],
        'DEPT_LINE_CODE6' => [
            'NAME' => '学部系統コード6',
            'INDEX' => 8,
        ],
        'DEPT_LINE_CODE7' => [
            'NAME' => '学部系統コード7',
            'INDEX' => 9,
        ],
        'NAME_HKN' => [
            'NAME' => '大学名（半角カナ）',
            'INDEX' => 10,
        ],
        'NAME' => [
            'NAME' => '大学名',
            'INDEX' => 11,
        ],
        'DEPT_CODE' => [
            'NAME' => '学部コード',
            'INDEX' => 12,
        ],
        'DEPT_SEQ' => [
            'NAME' => '学部表示順',
            'INDEX' => 13,
        ],
        'DEPT_NAME' => [
            'NAME' => '学部名',
            'INDEX' => 14,
        ],
        'SUBJ_CODE' => [
            'NAME' => '学科コード',
            'INDEX' => 15,
        ],
        'SUBJ_SEQ' => [
            'NAME' => '学科表示順',
            'INDEX' => 16,
        ],
        'SUBJ_NAME' => [
            'NAME' => '学科名',
            'INDEX' => 17,
        ],
        'SEARCH_SUBJ_NAME' => [
            'NAME' => '検索用学科名',
            'INDEX' => 18,
        ],
        'CMN_TEST_USE' => [
            'NAME' => '共通テスト利用',
            'INDEX' => 19,
        ],
        'DEVI_VAL' => [
            'NAME' => '偏差値',
            'INDEX' => 20,
        ],
    ],


    // 高校用CSVのヘッダーの順序
    'HIGH_SCHOOL_CSV_HEADER' => [
        'HIGH_SCHOOL_CODE' => [
            'NAME' => '高等学校等コード',
            'INDEX' => 0,
        ],
        'HIGH_SCHOOL_NAME_HKN' => [
            'NAME' => '高等学校等名（半角カナ）',
            'INDEX' => 1,
        ],
        'ZIP_NUM' => [
            'NAME' => '郵便番号',
            'INDEX' => 2,
        ],
		'TEL_NUM' => [
            'NAME' => '電話番号',
            'INDEX' => 3,
        ],
        'INSTALL_CODE' => [
            'NAME' => '設置者コード',
            'INDEX' => 4,
        ],
        'CLASS_CODE' => [
            'NAME' => '高校種別コード',
            'INDEX' => 5,
        ],
        'HIGH_SCHOOL_NAME' => [
            'NAME' => '高等学校等名（全角）',
            'INDEX' => 6,
        ],
        'ADDRESS' => [
            'NAME' => '所在地（全角）',
            'INDEX' => 7,
        ],
    ],

    // 教室用CSVのヘッダーの順序
    'CLS_CSV_HEADER_NUM' => [
        'SCH_CODE' => [
            'NAME' => 'sch_code',
            'INDEX' => 0,
        ],
        'CLS_CODE' => [
            'NAME' => 'cls_code',
            'INDEX' => 1,
        ],
        'CLS_NAME' => [
            'NAME' => 'cls_name',
            'INDEX' => 2,
        ],
        'PASSWORD' => [
            'NAME' => 'password',
            'INDEX' => 3,
        ],
        'TEMPPASS' => [
            'NAME' => 'temppassword',
            'INDEX' => 4,
        ],
    ],

    // 教室マスタ ダウンロードCSVファイル名
    'CLASS_IMPORT_CSV_ERRFILENAME' => 'class_registration_error.csv',

    // 教室マスタ ダウンロードCSVファイル名
    'CLASS_EXPORT_CSV_FILENAME' => 'classList.csv',

    // 日付フォーマット
    'DEFAULT_DATE_FORMAT' => 'Y-m-d H:i:s',

    // 学年
    'FIRST_GRADE' => 1,
    'SECOND_GRADE' => 2,
    'THIRD_GRADE' => 3,

    // SSO 認証
    'SSO_LOGIN' => array(
        'WASEDA_ACADEMY' => [ // 早稲アカ 1304267
            'SCH_CODE'   => '1304267',
            'SECRET_KEY' => '2PqfsjWmZ4uS3sp4kmcz'
        ],
    ),

    // 学部判定用コード番号表のパス
    'CODE_PDF_FACULTY_PATH' => '/pdf/cdn_1_コード番号表.pdf',
    // 学部判定用コード番号表の名称
    'CODE_PDF_FACULTY_NAME' => '学部判定用コード番号表',

    // 学科判定用コード番号表のパス
    'CODE_PDF_DEPARTMENT_PATH' => '/pdf/cdn_3_コード番号表.pdf',
    // 学科判定用コード番号表の名称
    'CODE_PDF_DEPARTMENT_NAME' => '学科判定用コード番号表',

    //受験番号のゼロ埋め桁数
    'EXAM_NUMBER_ZERO_PAD_DIGITS' => 6,

    //受験番号CSVのヘッダーの順序
    //塾コード、生徒コード、年度、回数、学年、受験番号
    'EXAM_NUM_CSV_HEADER_NUM' => [
        'SCH_CODE' => [
            'NAME' => 'sch_code',
            'INDEX' => 0,
        ],
        'PSN_CODE' => [
            'NAME' => 'psn_code',
            'INDEX' => 1,
        ],
        'YEAR' => [
            'NAME' => 'year',
            'INDEX' => 2,
        ],
        'CNT' => [
            'NAME' => 'cnttest',
            'INDEX' => 3,
        ],
        'GRADE' => [
            'NAME' => 'grade',
            'INDEX' => 4,
        ],
        'EXAM_NUM' => [
            'NAME' => 'exam_number',
            'INDEX' => 5,
        ],
    ],

    // 受験番号アップロードエラーCSVファイル名
    'EXAMNUM_IMPORT_ERR_FILENAME' => 'exam_number_registration_error.csv',

    //パスワード再設定メール用
    'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
    'MAIL_FROM_NAME'    => env('MAIL_FROM_NAME'),
    'REPLY_TO'          => 'info@seihoku.org',

    // 年度（過去年数）
    'PAST_YEAR' => 4,

    // 年度（未来年数）
    'FUTURE_YEAR' => 1,

    // コード表ダウンロード用のリンク
    'UNVC_LIST_DOWNLOAD_LINK' => 'https://m.seihoku.org/pdf/cdn_1_コード番号表.pdf',

    // 全国共通の塾情報
    'UNVC_COMMON_SCHOOL' => [
        'SCH_CODE' => 8888880,
    ],

    // 大学コード検索画面用内部API一回にあたって取得件数
    //'UNVC_PER_PAGE' => 5,  // TODO: テスト
    'UNVC_PER_PAGE' => 18,

    // 大学コード検索画面用最大取得件数
    'UNVC_RESULT_MAX' => 100,

    // コード表ディレクトリ
    'UNVC_CODEBOOK_PATH' => '/pdf/univ_search',

    // テスト情報取得APIで年度の条件
    'CURRENT_YEAR' => env('CURRENT_YEAR'),
];


