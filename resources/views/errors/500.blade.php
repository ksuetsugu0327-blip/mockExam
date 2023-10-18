@extends('layout.common')

@section('title', '不正な操作が検出されました')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@inject('routeUtil','App\Utils\RouteUtil')

@section('content')
<div id="site_content">
    <div id="content">
        <h1>エラーが検出されました</h1>
        <div id="error404">
            <p>サーバーがエラーを検出したため、実行することができませんでした。</p>
            <p>しばらく時間をおいてからもう一度アクセスして頂くか、</p>
            <p>管理者にお問い合わせください。</p>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')