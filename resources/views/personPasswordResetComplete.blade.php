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
            <div style="width:400px;margin:80px 0px 10px 120px; text-align:left;">仮パスワードを「xxxx@xxxxx.jp」に送信しました。<br>メールが受信されない場合はメールアドレスが間違っているか<br>迷惑メールに登録されいる可能性があります。</div>
            </div>
            <!--div style="text-align:center;"><a href="index.html">ログイン画面に戻る ＞</a></div-->
            <div style="text-align:center;"><a href="https://stg-m.seihoku.org/{{session(config('constants.SESSION_KEY_SCHCODE'))}}/psn/login">ログイン画面に戻る ＞</a></div>
            </div>
        </div>
    </div>
</div><!-- /#main-->
@endsection

@include('layout.footer')