
@extends('layout.common')

@section('title', 'テスト詳細')
@section('keywords', '')
@section('description', 'テスト詳細')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.portal-header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        <h1>テスト詳細</h1>
    </div>
</div>
@endsection

@include('layout.portal-footer')