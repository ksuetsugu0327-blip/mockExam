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
                            <input type="password" name="current_password" value="{{old('current_password')}}" required/>
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
                            パスワードは、半角英数12～50文字以内で<br>
                            半角の英大文字、英小文字、数字を混在させてください。
                        </td>
                    </tr>
                    <tr><td style="padding:0 6px 1em 0">新しいパスワード&nbsp;</td>
                        <td style="padding:0 0 0 0">
                            <input id="password" type="password" name="password" value="{{old('password')}}" required autofocus>
                            @if ($errors->has('password'))
                                <br/>
                                <span class="error">
                                    <strong>{!! nl2br(e($errors->first('password'))) !!}</strong>
                                </span>
                            @endif
                         </td>
                    </tr>
                    <tr>
                      <td>新しいパスワード確認&nbsp;</td>
                      <td>
                            <input id="password_confirmation" type="password" name="password_confirmation" value="{{old('password_confirmation')}}" required>
                      </td>
                    </tr>
					</table>
                            <div style="margin-top:10px"><input class="show-password" type="checkbox" name="hide" value="" style="margin-top:10px"><span style="margin-left:5px">パスワードを表示する</span></div>
							<div><input class="button  bulu" type="submit" name="login" value="更 新" /></div>
					</form>
				</div>
        </div>
    </div>
</div><!-- /#main-->
@endsection

@include('layout.footer')