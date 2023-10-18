@extends('layout.common')

@section('title', '指定したページにはアクセスできません。')
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
       <h1>指定したページにはアクセスできません。</h1>
        <div id="error404">
            <p>URLを確認して、再度アクセスをお願いします。</p>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')