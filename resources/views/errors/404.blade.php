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
       <h1>ページがみつかりません</h1>
        <div id="error404">
            <p>申し訳ございませんが、お探しのページが見つかりませんでした。</p>
            <p>お探しのページは削除されたか、もしくは移動した可能性があります。</p>
            <p>お手数ですが、最初からやり直してください。</p>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')