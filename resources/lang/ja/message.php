<?php
 
/* 
 * メッセージ用 定数クラスです。
 * 
 */
return[
    
    // 入力画面エラーメッセージ
    'INPUT_ERROR_MESSAGE' => '入力内容を確認してください。',
    // 検索画面エラーメッセージ
    'SEARCH_NO_DATA' => '指定された検索条件に該当する情報は存在しません。',
    // 登録時の確認メッセージ
    'CONFIRM_STORE_DATA' => '登録してもよろしいでしょうか？',
    // 削除時の確認メッセージ
    'CONFIRM_DELETE_DATA' => '削除してもよろしいでしょうか？',
    // 更新時の確認メッセージ
    'CONFIRM_UPDATE_DATA' => '修正してもよろしいでしょうか？',
    // 検索画面明細表示件数最大メッセージ
    'WARN_OVER_VIEW_RECORD_COUNT' => '明細が最大表示件数を超えたため、:COUNT件まで表示しました。絞り込みを行ってください。',

    // ダウンロードエラーメッセージ
    'DOWNLODE_NOT_FILE' => 'ダウンロードに失敗しました。時間を空けてダウンロードしてください。',
    
    // 桁数チェック
    // 塾名
    'SCHOOL_NAME_MAX' => '塾名には全角20文字の文字列を指定してください。',
    // テスト名
    'TESTNAME_MAX' => 'テスト名には全角40文字の文字列を指定してください。',
    // 教室名チェック
    'CLASS_NAME_MAX' => '教室名には20文字の文字列を指定してください。',
    // 氏名チェック
    'PEARSON_NAME_MAX' => '氏名には全角20文字の文字列を指定してください。',
    
    // 正規表現チェック
    // 回数コンバート区分
    'CNTCONVERT_KBN_REGIX' => '回数コンバート区分には半角1文字の文字列を指定してください。',
    // パスワード
    'PASSWORD_REGIX' => 'パスワードには英数字のみかつ混在した文字列を指定してください。',
    // 塾生ID（個人コード）
    'PSN_CODE_REGIX' => '塾生IDには半角11文字までの文字列を指定してください。',
    // 教室ID（教室コード）
    'KST_CODE_REGIX' => '教室IDには半角11文字までの文字列を指定してください。',
    
    // 存在チェック
    // 塾マスタ(一覧画面)
    'JSCHOOL_EXISTS' => '指定された塾は、塾マスタに存在しません。',
    
    // 教室マスタ(一覧画面)
    'JCLASS_EXISTS' => '指定された教室は、教室マスタに存在しません。',
    
    // 塾マスタ(一覧画面)
    'MPERSON_EXISTS' => '指定された個人は、個人マスタに存在しません。',
    
    // 排他エラー
    // 塾マスタ削除処理
    'MSCHOOL_EXCLUSIVE_EXCEPTION' => '指定された塾(塾コード：:sch_code)は既に削除されています。最新の情報を確認してください。',
    // 教室マスタ削除処理
    'MCLASS_EXCLUSIVE_EXCEPTION' => '指定された教室(塾コード：:sch_code 教室コード：:cls_code)は既に削除されています。最新の情報を確認してください。',
    // 個人マスタ削除処理
    'MPERSON_EXCLUSIVE_EXCEPTION' => '指定された生徒(塾コード：:sch_code 塾生ID：:psn_code)は既に削除されています。最新の情報を確認してください。',
    // テスト情報削除処理
    'TESTINFO_EXCLUSIVE_EXCEPTION' => '指定されたテスト情報は既に削除されています。最新の情報を確認してください。',

    // 正常終了メッセージ
    // 塾マスタ 登録処理の正常終了時メッセージ
    'SUCCESS_MSCHOOL_REGISTER_DATA' => '新しい塾（塾コード：:sch_code）の登録が完了しました。',
    // 塾マスタ 更新処理の正常終了時メッセージ
    'SUCCESS_MSCHOOL_UPDATE_DATA' => '指定された塾（塾コード：:sch_code）の修正が完了しました。',
    // 塾マスタ 削除処理の正常終了時メッセージ
    'SUCCESS_MSCHOOL_DELETE_DATA' => '指定された塾（塾コード：:sch_code）を削除しました。',
    //教室マスタ
    // 教室マスタ 登録処理の正常終了時メッセージ
    'SUCCESS_MCLASS_REGISTER_DATA' => '新しい教室(教室コード：:cls_code)の登録が完了しました。',
    // 教室マスタ 更新処理の正常終了時メッセージ
    'SUCCESS_MCLASS_UPDATE_DATA' => '指定された教室(教室コード：:cls_code)の修正が完了しました。',
    // 教室マスタ 削除処理の正常終了時メッセージ
    'SUCCESS_MCLASS_DELETE_DATA' => '指定された教室(教室コード：:cls_code)を削除しました。',
    // CSVにて教室マスタ 登録処理の正常終了時メッセージ
    'SUCCESS_MCLASS_CSV_DATA' => 'CSVﾃﾞｰﾀの登録が完了しました。',
    // テスト情報 登録処理の正常終了時メッセージ
    'SUCCESS_TESTINFO_REGISTER_DATA' => '新しいテスト情報の登録が完了しました。',
    // テスト情報 更新処理の正常終了時メッセージ
    'SUCCESS_TESTINFO_UPDATE_DATA' => '指定されたテスト情報の修正が完了しました。',
    // テスト情報 削除処理の正常終了時メッセージ
    'SUCCESS_TESTINFO_DELETE_DATA' => '指定されたテスト情報を削除しました。',

    // 個人マスタ 登録処理の正常終了時メッセージ
    'SUCCESS_MPERSON_REGISTER_DATA' => '新しい生徒（塾コード：:sch_code 塾生ID：:psn_code）の登録が完了しました。',
    // 個人マスタ 更新処理の正常終了時メッセージ
    'SUCCESS_MPERSON_UPDATE_DATA' => '指定された生徒（塾コード：:sch_code 塾生ID：:psn_code）の修正が完了しました。',
    // 個人マスタ 削除処理の正常終了時メッセージ
    'SUCCESS_MPERSON_DELETE_DATA' => '指定された生徒（塾コード：:sch_code 塾生ID：:psn_code）を削除しました。',
    // CSVにて個人マスタ 登録処理の正常終了時メッセージ
    'SUCCESS_MPERSON_CSV_DATA' => 'CSVﾃﾞｰﾀの登録が完了しました。',

    // 大学コードマスタ 登録処理の正常終了時メッセージ
    'SUCCESS_MUNIVCODE_REGISTER_DATA' => '新しい大学コード（コードデーター名コード：:code_head_id ）の登録が完了しました。',
    // 大学コードマスタ 登録処理の異常終了時メッセージ
    'FAILURE_MUNIVCODE_REGISTER_DATA' => '登録データにエラーがあります。エラーファイルを確認してください。',
    // 大学コードマスタ 更新処理の正常終了時メッセージ
    'SUCCESS_MUNIVCODE_UPDATE_DATA' => '指定された大学コード（塾コード：:sch_code 塾生ID：:psn_code）の修正が完了しました。',
    // 大学コードマスタ 削除処理の正常終了時メッセージ
    'SUCCESS_MUNIVCODE_DELETE_DATA' => '指定された大学コード（塾コード：:sch_code 塾生ID：:psn_code）を削除しました。',
    // CSVにて大学コードマスタ 登録処理の正常終了時メッセージ
    'SUCCESS_MUNIVCODE_CSV_DATA' => 'CSVﾃﾞｰﾀの登録が完了しました。',

    // 志望校テーブル 登録処理の正常終了時メッセージ
    'SUCCESS_TICKET_REGISTER_DATA' => ':testname 志望校の登録が完了しました。',
    // 不正処理
    'ILLEGAL_TICKET_REGISTER_DATA' => '不正な処理が行われました。',


    // 管理資料一覧表示 処理の正常終了時メッセージ
    'SUCCESS_TZIPORDER_REGISTER_DATA' => '作成目安時間 : :time 分',
    // 管理資料個人成績一括指示発行指示済みメッセージ
    'TZIPORDER_DONE_EXCEPTION' => '選択された試験はすでに発行済みまたは発行中です。',
    // 管理資料個人成績一登録
    'TZIPORDER_EXCLUSIVE_EXCEPTION' => 'すでに削除済みです。',
    
    // ログイン関連
    // 塾コード不正
    'SCHOOL_CODE_NO_EXISTS' => '塾コードが不正です。塾コードを確認してください。',
    // 教室コード不正
    'CLASS_CODE_NO_EXISTS' => '教室コードが不正です。教室コードを確認してください。',
    // 塾生ID不正
    'PERSON_ID_NO_EXISTS' => '塾生IDが不正です。塾生IDを確認してください。',
    // 代理ログイン済みエラー
    'ALREADY_SURROGATE_LOGINDED' => '既に代理ログイン済みのため、ログインできません。',
    
    //CSV登録
    'CSV_MPERSON_DUPLICATE_DATA' => 'アップロードファイル内で重複しています。（塾コード：:sch_code 塾生ID：:psn_code）',
    'CSV_MPERSON_NOT_DATA' => 'アップロードファイルのデータが0件です。',

    //教室CSV登録
    'CSV_MCLASS_DUPLICATE_DATA' => 'アップロードファイル内で重複しています。（塾コード：:sch_code 教室コード：:cls_code）',
    'CSV_MCLASS_NOT_DATA' => 'アップロードファイルのデータが0件です。',

    //受験番号登録
    'CSV_TICKET_DUPLICATE_DATA' => 'アップロードファイル内で重複しています。（塾コード：:sch_code 生徒コード：:psn_code 年度：:year 回数：:cnttest 学年：:grade）',
    'CSV_TICKET_PERSON_NO_EXISTS' => '指定された生徒（塾コード：:sch_code 生徒コード：:psn_code）は存在しません。',
    'CSV_TICKET_NOT_DATA' => 'アップロードファイルのデータが0件です。',

    //大学CSV登録
    'CSV_MUNIVCODE_DUPLICATE_DATA' => 'アップロードファイル内で重複しています。（大学コード：:univ_code）',
    'CSV_MUNIVCODE_NOT_DATA' => 'アップロードファイルのデータが0件です。',
    'CSV_MUNIVCODE_UPDATE_EXCLUSION' => [
        'update' => '該当する大学コードデータが他のユーザーによって更新されました。もう一度操作を行ってください。',
    ],

    //高校CSV登録
    'CSV_MHIGHSCHOOL_DUPLICATE_DATA' => 'アップロードファイル内で重複しています。（高等学校コード：:high_school_code）',
    'CSV_MHIGHSCHOOL_NOT_DATA' => 'アップロードファイルのデータが0件です。',

];