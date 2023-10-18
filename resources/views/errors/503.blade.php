@extends('layout.common')

@section('title', 'メンテナンス中')
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
       <h1>メンテナンス中</h1>
        <div id="error404">
            <p>メンテナンス中です。ご迷惑をおかけしますがしばらくおまちください。</p>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')