@extends('layout.common')

@section('title', 'ページがみつかりません')
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
       <h1>只今メンテナンス中です</h1>
        <div id="error404">
            <p>申し訳ありませんが、暫くしてから再度アクセスをお願いします。</p>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')