@extends('layout.common')

@section('title', 'パスワード')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="unlogin">
    <div class="content_ps">
        <h1>パスワード</h1>
        <div id="un_login" class="unlogin">
　　　 　　　 @if ($errors->has('message'))
      		  <div class="error_message">
           　　　　 	<strong>{{ $errors->first('message') }}</strong>
        	  </div>
    　　    @endif
            
            <form action="{{route('changePassword.register',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <input type="hidden" id="psn_code" name="psn_code" value="{{$psn_code}}" />
                <table style="width: 100%;">
                    <tr><td>塾生ID</td><td>{{$psn_code}}</td></tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td style="font-size: 70%;">
                            半角英数8～50文字を指定してください。<br>
                            （英数字混在推奨）
                        </td>
                    </tr>
                    <tr><td style="padding:0 6px 1em 0">新しいパスワード</td>
                        <td style="padding:0 0 1em 0">
                            <input id="password" type="password" name="password" required autofocus>
                            @if ($errors->has('password'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr><td>新しいパスワード確認</td>
                        <td>
                            <input id="password_confirmation" type="password" name="password_confirmation" required>
                            @if ($errors->has('password_confirm'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
                <div><input class="button  bulu" type="submit" name="register" value="登録" /></div>
            </form>

        </div>
    </div>
</div>
@endsection

@include('layout.footer')