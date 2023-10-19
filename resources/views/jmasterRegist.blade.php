@extends('layout.common')

@section('title', '塾マスタ登録')
@section('keywords', '')
@section('description', '塾マスタ新規登録')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="jmaster">
    <div id="content">
        <h1>塾マスタ・登録</h1>
        @if (isset($message))
        <div class="error_message">
         <strong>{{ $message }}</strong>
        </div>
        @endif
        @if ($errors->has('message'))
        <div class="error_message">
         <strong>{{ $errors->first('message') }}</strong>
        </div>
        @endif
        <div id="jm_touroku" class="touroku">
            <form method="post" action="{{route('schoolRegist.store',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}">
                <!-- CSRF保護 -->
                @csrf
                <table class="mastertable">
                    <tr><td><p>塾コード</p></td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="sch_code" value="{{ old('sch_code') }}" autofocus required maxlength='7' >
                                <br>
                                @if($errors->has('sch_code'))<span class="error">{{ $errors->first('sch_code') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>塾名</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="sch_name" id='sch_name' value="{{ old('sch_name') }}" maxlength="40">
                                <br>
                                @if($errors->has('sch_name'))<span class="error">{{ $errors->first('sch_name') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>塾アカウント名</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="portal_account" id='portal_account' value="{{ old('portal_account') }}" maxlength="40">
                                <br>
                                @if($errors->has('portal_account'))<span class="error">{{ $errors->first('portal_account') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>回数コンバート区分</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="cntconvert_kbn" value="{{ old('cntconvert_kbn', ' ') }}" maxlength='1' required >
                                <br>
                                @if($errors->has('cntconvert_kbn'))<span class="error">{{ $errors->first('cntconvert_kbn') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>テスト回数表示区分</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="cnttest_kbn" value="{{ old('cnttest_kbn', config('constants.CNTTEST_KBN_BY_TEST')) }}" required maxlength="2">
                                <br>
                                @if($errors->has('cnttest_kbn'))<span class="error">{{ $errors->first('cnttest_kbn') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>テスト名</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="testname" value="{{ old('testname', '全国高校共通テスト') }}" required  maxlength='40'>
                                <br>
                                @if($errors->has('testname'))<span class="error">{{ $errors->first('testname') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>管理ユーザー区分</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="admin_kbn" value="{{ old('admin_kbn', config('constants.ADMIN_KBN_GENERAL')) }}" required maxlength='1'>
                                <br>
                                @if($errors->has('admin_kbn'))<span class="error">{{ $errors->first('admin_kbn') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>生徒機能非表示フラグ</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="psn_hide_flg" value="{{ old('psn_hide_flg', config('constants.PSN_NO_HIDE_FLG')) }}" required maxlength='1'>
                                <br>
                                @if($errors->has('psn_hide_flg'))<span class="error">{{ $errors->first('psn_hide_flg') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>塾名非表示フラグ</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="schname_hide_flg" value="{{ old('schname_hide_flg', config('constants.SCH_NAME_NO_HIDE_FLG')) }}" required maxlength='1'>
                                <br>
                                @if($errors->has('schname_hide_flg'))<span class="error">{{ $errors->first('schname_hide_flg') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>個人登録票利用フラグ</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="psncard_regist_flg" value="{{ old('psncard_regist_flg', config('constants.PSN_CARD_HIDE_FLG')) }}" required maxlength='1'>
                                <br>
                                @if($errors->has('psncard_regist_flg'))<span class="error">{{ $errors->first('psncard_regist_flg') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>生徒パスワード変更可能フラグ</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="psn_psw_update_flg" value="{{ old('psn_psw_update_flg', config('constants.PSN_CARD_NOT_USE_FLG')) }}" required maxlength='1'>
                                <br>
                                @if($errors->has('psn_psw_update_flg'))<span class="error">{{ $errors->first('psn_psw_update_flg') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>教室成績データCSVダウンロード可能フラグ</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="cls_grade_csv_flg" value="{{ old('cls_grade_csv_flg', config('constants.CLS_GRADE_CSV_NOT_USE_FLG')) }}" required maxlength='1'>
                                <br>
                                @if($errors->has('cls_grade_csv_flg'))<span class="error">{{ $errors->first('cls_grade_csv_flg') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>アクセス許可IPアドレス</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="permit_ip" value="{{ old('permit_ip', '') }}" maxlength='100'>
                                <br>
                                @if($errors->has('permit_ip'))<span class="error">{{ $errors->first('permit_ip') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td >&nbsp;</td>
                        <td style="font-size: 70%;">
                            半角英数8～50文字を指定してください。<br>
                            （英数字混在推奨）
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="padding:0 0 1em 0">塾アカウントパスワード</p>
                        </td>
                        <td>
                            <p style="padding:0 0 1em 0">
                                <input class="cell clear" type="password" name="password" value="" required>
                                <br>
                                @if($errors->has('password'))<span class="error">{{ $errors->first('password') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>塾アカウントパスワード(確認)</p>
                        </td>
                        <td>
                            <p>
                                <input  class="cell clear" type="password" name="password_confirmation" value="" required>
                                <br>
                                @if($errors->has('password_confirmation'))<span class="error">{{ $errors->first('password_confirmation') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                </table>
                <div class="jbuttons">
                    <p class="cell"><input class="button bulu" type="submit" name="submit" value="登録" onclick="return registSchool()"></p>
                    <p class="cell"><input id="inputClear" class="button green" type="button" name="reset" value="クリア"></p>
                    <a href="{{ route('schools.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="cell"><p class="button green">一覧に戻る</p></a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@include('layout.footer')

