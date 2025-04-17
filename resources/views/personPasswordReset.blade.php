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
            <h1>パスワード再設定</h1>
            <div id="log_touroku" class="login">
                @if (session('mail_reset_failed'))
                <div class="error_message">
                    <strong>{{ session('mail_reset_failed') }}</strong></br>
                </div></br>
                @endif
                <form action="{{route('personPasswordReset.reset',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                    @csrf
                    {{-- <table>
                        <tr><td>塾生ID　</td><td>{{$psn_code}}</td></tr>
                        <input type="hidden" name="psn_code" value="{{$psn_code}}" />
                    </table> --}}
                    <input type="hidden" name="psn_code" value="{{$psn_code}}" />
                    <div style="width:400px;margin:70px 0px 10px 120px; text-align:left;">登録されているメールアドレスに仮パスワードを送信します。<br>現在お使いのパスワードはリセットされますのでご注意ください。</div>
                    <div><input style="width:250px;" class="button  bulu" type="submit" name="login" value="パスワード再設定" /></div>
                </form>
            </div>
        </div>
    </div>
</div><!-- /#main-->
@endsection

@include('layout.footer')