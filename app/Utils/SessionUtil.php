<?php

declare(strict_types=1);

namespace App\Utils;

use Illuminate\Http\Request;

/**
 * セッションに関するユーティリティクラス
 */
class SessionUtil
{
    /**
     * 本システムで利用するセッションのクリア（Laravel部分ではなく、業務処理におけるセッション）
     */
    public static function InitializeSession(Request $request)
    {
        // ログイン用
        // 塾コード
        $request->session()->forget(config('constants.SESSION_KEY_SCHCODE'));
        // 塾名
        $request->session()->forget(config('constants.SESSION_KEY_SCHNAME'));
        // 教室コード
        $request->session()->forget(config('constants.SESSION_KEY_CLSCODE'));
        // 教室名
        $request->session()->forget(config('constants.SESSION_KEY_CLSNAME'));
        // 試験名
        $request->session()->forget(config('constants.SESSION_KEY_SCHTESTNAME'));
        // 管理ユーザ区分
        $request->session()->forget(config('constants.SESSION_KEY_SCHADMINKBN'));
        // 生徒機能非表示フラグ
        $request->session()->forget(config('constants.SESSION_KEY_PSN_HIDE_FLG'));
        // 塾名非表示フラグ
        $request->session()->forget(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG'));
        // 個人登録票利用フラグ
        $request->session()->forget(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG'));
        // 個人成績表公開フラグ
        $request->session()->forget(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG'));
        // 塾生コード
        $request->session()->forget(config('constants.SESSION_KEY_PSNCODE'));
        // 塾生氏名
        $request->session()->forget(config('constants.SESSION_KEY_PSNNAME'));

        // 塾マスタ一覧用
        $request->session()->forget(config('constants.SESSION_KEY_MSCHOOL_SEARCH_CONDITION'));

        // 教室マスタ一覧
        $request->session()->forget(config('constants.SESSION_KEY_MCLASS_SEARCH_CONDITION'));

        // 生徒マスタ一覧用
        $request->session()->forget(config('constants.SESSION_KEY_MPERSON_SEARCH_CONDITION'));
        // 検索結果一覧リスト
        $request->session()->forget(config('constants.SESSION_KEY_MPERSON_LIST'));

        // 代理ログイン用
        // 代理ログイン済み区分
        $request->session()->forget(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'));
        // 塾用
        $request->session()->forget(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'));
        // 教室用
        $request->session()->forget(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'));
        // 個人用
        $request->session()->forget(config('constants.SESSION_KEY_SURROGATE_LOGIN_PERSON'));

        // laravelで生成されるセッション
        // url
        $request->session()->forget('url');
    }
}
