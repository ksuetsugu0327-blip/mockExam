@extends('layout.common')
@section('title', '教室マスタ登録')
@section('description', '教室マスタ新規登録')
@section('keywords', '')
@section('pageCss')
@endsection

@inject('passwordUtils','App\Utils\PasswordUtil')

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="kmaster">
    <div id="content_touroku">
        <h1>教室マスタ・登録</h1>
        @if (isset($message))
        echo $message;
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
            <form id="register_form" method="post" action="{{route('classRegist.store',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" >
                <!-- CSRF保護 -->
                @csrf
                <table class="mastertable" style="
    text-align: left;">
                    <tr>
                        <td>
                            <p>塾コード</p>　　　　　　　　　
                        </td>
                        <td>
                                <input type="text" name="sch_code" class="cell clear" value="{{ old('sch_code') }}"autofocus required maxlength='7'>
                                <br>
                                @if($errors->has('sch_code'))<div class="error">{{ $errors->first('sch_code') }}</div> @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>教室コード</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="cls_code" value="{{ old('cls_code') }}" required maxlength='4' >
                                <br>
                                @if($errors->has('cls_code'))<div class="error">{{ $errors->first('cls_code') }}</div> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>教室名</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="cls_name" value="{{ old('cls_name') }}" maxlength='20'>
                                <br>
                                @if($errors->has('cls_name'))<div class="error">{{ $errors->first('cls_name') }}</div> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>  
                                <label>
                                    <input type="checkbox" name="jpchange"  id="jpchange" class="checkbox"><span class="ctext">教室アカウントのパスワードを登録</span>
                                </label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td style="font-size: 70%;">
                            半角英数6～50文字を指定してください。<br>
                            （英数字混在推奨）
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="padding:0 0 1em 0">教室アカウントパスワード</p>
                        </td>
                        <td>
                            <p style="padding:0 0 1em 0">
                                <input class="cell clear" type="password" name="password" value="" disabled>
                                <br>
                                @if($errors->has('password'))<div class="error">{{ $errors->first('password') }}</div> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>教室アカウントパスワード(確認)</p>
                        </td>
                        <td>
                            <p>
                                <input  class="cell clear" type="password" name="password_confirmation" value="" disabled>
                                <br>
                                @if($errors->has('password_confirmation'))<div class="error">{{ $errors->first('password_confirmation') }}</div> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label>                                
                                <input type="checkbox" id="temppasswordReissueCheck" name="temppasswordReissueCheck" class="checkbox"><span class="ctext">仮パスワードを発行</span>
                            </label>
                            @if($errors->has('temppasswordReissueCheck'))<span class="error">{{ $errors->first('temppasswordReissueCheck') }}</span> @endif
                        </td>                        
                    </tr>
                </table>
                <div style="margin-top:20px;" class="buttons">
                    <p class="cell"><input class="button bulu" type="submit" name="submit" value="登録" id="button"></p>
                    <p class="cell"><input id="inputClear" class="button green" type="button" name="reset" value="クリア" id="button"></p>
                    <a href="{{ route('classes.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="cell"　id="button"><p class="button green">一覧に戻る</p></a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#register_form').on('submit', function(e){
        if(!$('#jpchange').prop('checked')&&!$('#temppasswordReissueCheck').prop('checked')){
            if($('.error_message').length > 0){
                $('.error_message').each(function(idx,ele){
                    $(ele).remove()
                });
            }
            $('<div class="error_message"><strong>"仮パスワードを発行\"または\"教室アカウントのパスワードを登録"<br>選択してください</strong></div>').insertBefore('#jm_touroku')

            return false
        }
        else{
            //console.log("submit plz");
        }
    });
    $('#temppasswordReissueCheck').change(function () {
        if($('#temppasswordReissueCheck').prop('checked')){
            $('#jpchange').prop('checked',false);
            $('input[name="password"]').prop('disabled',true);
            $('input[name="password_confirmation"]').prop('disabled',true);
        }
        $(':submit').prop('disabled',false);
     });
    $('#jpchange').change(function () {
        if($('#jpchange').prop('checked')){
            $('#temppasswordReissueCheck').prop('checked',false);
            $('input[name="password"]').prop('disabled',false);
            $('input[name="password_confirmation"]').prop('disabled',false);
        }
        else{
            $('input[name="password"]').prop('disabled',true);
            $('input[name="password_confirmation"]').prop('disabled',true);
        }
        $(':submit').prop('disabled',false);
     });
</script>
@endsection

@include('layout.footer')