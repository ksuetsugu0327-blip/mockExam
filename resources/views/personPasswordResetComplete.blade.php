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
            <div style="width:470px;margin:80px 0px 10px 90px; text-align:left;">仮パスワードを登録されているメールアドレスに送信しました。<br>メールが受信されない場合は、登録メールアドレスが間違っているか、迷惑メールに登録されいる可能性がありますので、塾の管理者にご確認ください。</div>
            </div>
            <!--div style="text-align:center;"><a href="index.html">ログイン画面に戻る ＞</a></div-->
            <div style="text-align:center;"><a href="https://stg-m.seihoku.org/{{session(config('constants.SESSION_KEY_SCHCODE'))}}/psn/login">ログイン画面に戻る ＞</a></div>
            </div>
        </div>
    </div>
</div><!-- /#main-->
@endsection

@include('layout.footer')