@extends('layout.common')

@section('title', 'ログイン')
@section('keywords', '')
@section('description', '教室用仮パスワード入力')
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
            <form action="{{route('classTempLogin.login',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <input type="hidden" id="cls_code" name="cls_code" value="{{$cls_code}}" />
                <table>
                    <tr><td>教室ID</td><td>{{$cls_code}}</td></tr>
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
        </div>
    </div>
</div>

@endsection

@include('layout.footer')