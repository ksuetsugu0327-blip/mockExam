@extends('layout.common')

@section('title', '塾マスタ・修正')
@section('keywords', '')
@section('description', '塾マスタ修正、削除')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="jmaster">
    <div class="content">
        <h1>塾マスタ・修正</h1>
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
        <div id="jm_correction" class="correction">
            <form action="{{ route('schoolEdit.update',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'edit_schCode'=>$school->sch_code]) }}" method="post">
                <!-- CSRF保護 -->
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $school->id}}">
                <table class="mastertable">
                    <tr><td><p>塾コード</p></td>
                        <td>
                            <p>
                                {{ $school->sch_code }}
                                <input type="hidden" name="sch_code" value="{{ $school->sch_code }}">
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>塾名</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell" type="text" name="sch_name" value="{{ old('sch_name', $school->name) }}" maxlength='40' autofocus>
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
                                <input class="cell" type="text" name="portal_account" value="{{ old('portal_account', $school->portal_account) }}" maxlength='40' autofocus>
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
                                <input class="cell" type="text" name="cntconvert_kbn" value="{{ old('cntconvert_kbn', $school->cntconvert_kbn) }}" maxlength='1'>
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
                                <input class="cells" type="text" name="cnttest_kbn" value="{{ old('cnttest_kbn', $school->cnttest_kbn) }}" maxlength='2'>
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
                                <input class="cell clear" type="text" name="testname" value="{{ old('testname', $school->testname) }}"required  maxlength='40'>
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
                                <input class="cell clear" type="text" name="admin_kbn" value="{{ old('admin_kbn', $school->admin_kbn) }}" maxlength='1'>
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
                                <input class="cell clear" type="text" name="psn_hide_flg" value="{{ old('psn_hide_flg', $school->psn_hide_flg) }}" maxlength='1'>
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
                                <input class="cell clear" type="text" name="schname_hide_flg" value="{{ old('schname_hide_flg', $school->schname_hide_flg) }}" maxlength='1'>
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
                                <input class="cell clear" type="text" name="psncard_regist_flg" value="{{ old('psncard_regist_flg', $school->psncard_regist_flg) }}" maxlength='1'>
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
                                <input class="cell clear" type="text" name="psn_psw_update_flg" value="{{ old('psn_psw_update_flg', $school->psn_psw_update_flg) }}" required maxlength='1'>
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
                                <input class="cell clear" type="text" name="cls_grade_csv_flg" value="{{ old('cls_grade_csv_flg', $school->cls_grade_csv_flg) }}" required maxlength='1'>
                                <br>
                                @if($errors->has('cls_grade_csv_flg'))<span class="error">{{ $errors->first('cls_grade_csv_flg') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>個人成績表公開機能利用フラグ</p>
                        </td>
                        <!-- <td>
                            <p>
                                <input class="cell clear" type="text" name="psn_report_publish_flg" value="{{ old('psn_report_publish_flg', $school->psn_report_publish_flg) }}" required maxlength='1'>
                                
                            </p>
                        </td> -->
                        <td>
                            <label for="radio1" style="display:inline-block; margin-right:10px;"><input type="radio" name="psn_report_publish_flg" value="0" {{ old('psn_report_publish_flg', $school->psn_report_publish_flg) == '0' ? 'checked' : '' }}>&nbsp;&nbsp;&nbsp;無し</label>
                            <label for="radio1" style="display:inline-block; margin-right:10px;"><input type="radio" name="psn_report_publish_flg" value="1" {{ old('psn_report_publish_flg', $school->psn_report_publish_flg) == '1' ? 'checked' : '' }}>&nbsp;&nbsp;&nbsp;塾で管理</label>
                            <label for="radio2" style="display:inline-block; margin-right:10px;"><input type="radio" name="psn_report_publish_flg" value="2" {{ old('psn_report_publish_flg', $school->psn_report_publish_flg) == '2' ? 'checked' : '' }}>&nbsp;&nbsp;&nbsp;教室で管理</label>
                            <label for="radio3" style="display:inline-block; margin-right:10px;"><input type="radio" name="psn_report_publish_flg" value="3" {{ old('psn_report_publish_flg', $school->psn_report_publish_flg) == '3' ? 'checked' : '' }}>&nbsp;&nbsp;&nbsp;塾と教室で管理</label>
                            <br>
                            @if($errors->has('psn_report_publish_flg'))<span class="error">{{ $errors->first('psn_report_publish_flg') }}</span> @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>アクセス許可IPアドレス</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="permit_ip" value="{{ old('permit_ip', $school->permit_ip) }}" maxlength='100'>
                                <br>
                                @if($errors->has('permit_ip'))<span class="error">{{ $errors->first('permit_ip') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p>
                                <label>
                                    <input type="checkbox" name="jpchange" id="jpchange" class="checkbox"><span class="ctext">塾アカウントのパスワードを変更</span>
                                </label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
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
                                <input id="pass" class="cell" type="password" name="password" value="">
                                <br>
                                @if($errors->has('password'))<span class="error" id="error_pass">{{ $errors->first('password') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>塾アカウントパスワード(確認)</p>
                        </td>
                        <td>
                            <p>
                                <input id="pass_confirmation" class="cell" type="password" name="password_confirmation" value="">
                                <br>
                                @if($errors->has('password_confirmation'))<span class="error">{{ $errors->first('password_confirmation') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                </table>
                <div class="buttons">
                    <p class="cell"><input class="button bulu" type="submit" name="update"  value="修正"></p>
                    <p class="cell"><input class="button bulu delete_dialog" type="button" name="delete" value="削除" onclick='deleteSchool();'></p>
                    <a href="{{ route('schools.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="cell"><p class="button green">一覧に戻る</p></a>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="delete_form" action="{{ route('schoolEdit.update',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'delete_schCode'=>$school->sch_code]) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>  

<script>
    function updateSchool(id){
        if(confirm('{{trans('message.CONFIRM_UPDATE_DATA')}}')){
            event.preventDefault();
            document.getElementById('update_form').submit();
        };
        
        return false;
    }

    function deleteSchool(id){
        if(confirm('{{trans('message.CONFIRM_DELETE_DATA')}}')){
            event.preventDefault();
            document.getElementById('delete_form').submit();
        };
        
        return false;
    }

</script>

@endsection

@include('layout.footer')