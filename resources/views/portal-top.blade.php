
@extends('layout.common')

@section('title', '模試サイトポータル')
@section('keywords', '')
@section('description', 'ポータルトップ')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.portal-header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        <h1>模試サイトポータル</h1>

        <div id="portal_psn_area">
            <a href="{{ route('PortalAccount.account', ['account'=>'psn']) }}">生徒様はこちら &raquo;</a>
        </div>

        <div id="portal_cls_area">
            <a href="{{ route('PortalAccount.account', ['account'=>'cls']) }}">教室様はこちら &raquo;</a>
        </div>

        <div id="portal_sch_area">
            <a href="{{ route('PortalAccount.account', ['account'=>'sch']) }}">本部様はこちら &raquo;</a>
        </div>

        <div id="portal_sch_area">
            <a href="{{ route('univ.search') }}">高校・大学コード検索はこちら &raquo;</a>
        </div>

    </div>
</div>
@endsection

@include('layout.portal-footer')