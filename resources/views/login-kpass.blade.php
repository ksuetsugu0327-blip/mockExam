@extends('layout.common')

@section('title', 'ログイン')
@section('keywords', '')
@section('description', '生徒用仮パスワード入力')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="kplogin">
    <div id="content">
        <h1>ログイン</h1>
       <div id="kp_login" class="login">
　　　  @if ($errors->has('message'))
      	　  <div class="error_message">
               　<strong>{{ $errors->first('message') }}</strong>
      　　   </div>
            @endif
            <form action="{{route('personTempLogin.login',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <input type="hidden" id="psn_code" name="psn_code" value="{{$psn_code}}" />
                <table>
                    <tr><td>塾生ID</td><td>{{$psn_code}}</td></tr>
                    <tr><td>パスワード</td>
                        <td>
                            <input id="password" type="password" name="password" required autofocus />
                            @if ($errors->has('password'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
                @if($temp_pass)
                    <div id="unknownlogin"><a href="" onclick="event.preventDefault();
                        document.getElementById('unknown-form').submit();"><div class="passhumei">仮パスワードが不明の場合</div></a></div>
                @endif
                <div><input class="button  bulu" type="submit" name="login" value="ログイン" /></div>
            </form>
        </div>
    </div>
</div>

<form id="unknown-form" action="{{ route('personTempLogin.unknownauth',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="POST" style="display: none;">
    <input type="hidden" id="psn_code" name="psn_code" value="{{$psn_code}}" />
    @csrf
</form>  

@endsection

@include('layout.footer')