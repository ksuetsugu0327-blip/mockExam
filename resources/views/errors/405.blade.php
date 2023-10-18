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
            <p>申し訳ございませんが、お探しのページが見つかりませんでした。</p>
            <p>お手数ですが、更新してもう一度やり直してください。</p>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')