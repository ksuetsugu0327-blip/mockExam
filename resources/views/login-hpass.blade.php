@extends('layout.common')

@section('title', 'ログイン')
@section('keywords', '')
@section('description', '生徒用本パスワード入力')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="hplogin">
    <div id="content">
        <h1>ログイン</h1>
        <div id="hp_login" class="login">
         @if ($errors->has('message'))
      	     <div class="error_message">
                   <strong>{{ $errors->first('message') }}</strong>
             </div>
         @endif
            <form action="{{route('personLogin.login',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <input type="hidden" id="sch_code" name="sch_code" value="{{session(config('constants.SESSION_KEY_SCHCODE'))}}" />
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
                <div><input class="button  bulu" type="submit" name="login" value="ログイン" /></div>
            </form>
            @if(session(config('constants.SESSION_KEY_PSN_PSW_CHANGE_FLG')) === config('constants.PSN_CHANGE_PW_NO_HIDE_FLG'))
            <form method="post" name="form1" action="{{route('personLogin.passwordreset', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}">
                @csrf
                <input type="hidden" id="redirect_sch_code" name="redirect_sch_code" value="{{session(config('constants.SESSION_KEY_SCHCODE'))}}" />
                <input type="hidden" id="redirect_psn_code" name="redirect_psn_code" value="{{$psn_code}}" />
                <div><a href="javascript:form1.submit()">パスワードを忘れた方</a></div>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

@include('layout.footer')