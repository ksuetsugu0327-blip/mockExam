@extends('layout.common')

@section('title', 'パスワード')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="main">
    <!--<div id="header">
        <div id="sislog">
            <div id="sistem"><h1>全国高校共通テスト</h1></div>
    <div id="logout"><a href="login_hpass.html" id ="logoutCheck"> 前に戻る</a></div>
        </div>
        <div id="menubar">
            <p>9999973&nbsp;テストHMX塾</p>
        </div>
    </div> / #header -->
    <div id="site_content" class="logtouroku">
        <div id="content">
        <h1>パスワード変更</h1>
            @if ($errors->any() || isset($message))
            <div class="error_message">
                @if (isset($message))
                    <strong>{{ $message }}</strong></br>
                @else
                    <strong>{{ $errors->first('message') }}</strong></br>
                @endif
            </div></br>
            @endif
             <form action="{{route('personPasswordChange.update',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <input type="hidden" id="psn_code" name="psn_code" value="{{$psn_code}}" />
				<div id="log_touroku" class="login">
                <table >
					<tr><td>現在のパスワード&nbsp;</td>
                        <td>
                            <input type="text" name="current_password" value="" required/>
                            @if ($errors->has('current_password'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('current_password') }}</strong>
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td style="font-size: 70%;">
                            半角英数8～50文字を指定してください。<br>
                            （英数字混在推奨）
                        </td>
                    </tr>
                    <tr><td style="padding:0 6px 1em 0">新しいパスワード&nbsp;</td>
                        <td style="padding:0 0 0 0">
                            <input id="password" type="password" name="password" required autofocus>
                            @if ($errors->has('password'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                         </td>
                    </tr>
                    <tr>
                      <td>新しいパスワード確認&nbsp;</td>
                      <td>
                            <input id="password_confirmation" type="password" name="password_confirmation" required>
                      </td>
                    </tr>
					</table>
							<div><input class="button  bulu" type="submit" name="login" value="更 新" /></div>
					</form>
				</div>
        </div>
    </div>
</div><!-- /#main-->
@endsection

@include('layout.footer')