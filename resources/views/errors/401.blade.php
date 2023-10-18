@extends('layout.common')

@section('title', 'ログインエラーが発生しました。')
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
       <h1>ログインエラーが発生しました。</h1>
        <div id="error404">
            <p>ログイン情報を確認して、再度アクセスをお願いします。</p>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')