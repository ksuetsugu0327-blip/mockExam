
@extends('layout.common')

@section('title', '')
@section('keywords', '')
@section('description', 'ポータルトップ')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.portal-header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        <h1>入り口</h1>

        <div id="portal_psn_area">
            <a href="{{ route('PortalAccount.account', ['account'=>'psn']) }}">生徒様はこちら &raquo;</a>
        </div>

        <div id="portal_cls_area">
            <a href="{{ route('PortalAccount.account', ['account'=>'cls']) }}">教室様はこちら &raquo;</a>
        </div>

        <div id="portal_sch_area">
            <a href="{{ route('PortalAccount.account', ['account'=>'sch']) }}">本部様はこちら &raquo;</a>
        </div>

    </div>
</div>
@endsection

@include('layout.portal-footer')