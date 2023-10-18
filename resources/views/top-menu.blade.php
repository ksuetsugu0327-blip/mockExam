@extends('layout.common')

@section('title', 'メニュー')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@inject('routeUtil','App\Utils\RouteUtil')

@section('content')
<div id="site_content" class="top_menu">
    <div id="content">
        <div id="tyouhyou" class="menu">
            <div class="container" id="icons">
                {{-- 塾でログイン or 教室でログイン or (管理者ログイン and 塾代理ログイン) or (管理者ログイン and 教室代理ログイン) の時、帳票メニューを表示　--}}
                @if(auth(config('constants.GUARD_SCHOOL'))->check() || auth(config('constants.GUARD_CLASS'))->check() ||
                    (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) || 
                    (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS')))) 
                    <div class="row">
                        <h2>帳票</h2>
                        <div>
                            <a href="{{route($routeUtil->getPrefix().'.testcountsearch.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}"><div class="item"><div class="icon the-icons span3" title="Code: 0xf0f6"><i class="icons icon-chart-bar">&#xf0f6;</i> </div>個人成績表ダウンロード</div></a>
                            <a href="{{route($routeUtil->getPrefix().'.management.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}"><div class="item"><div class="icon the-icons span3" title="Code: 0xf0c5"><i class="icons icon-chart-bar">&#xf0c5;</i></div>管理資料ダウンロード</div></a>
                            @if(session(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG')) === config('constants.PSN_CARD_NO_HIDE_FLG'))
                            <a href="{{route($routeUtil->getPrefix().'.ticketList.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}"><div class="item"><div class="icon the-icons span3" title="Code: 0xf0f6"><i class="icons icon-chart-bar">&#xf0f6;</i> </div> 個人登録票</div></a>
                            @endif
                        </div>
                    </div>
                @endif
                {{-- マスタ機能が1つでも使えたらマスタ管理メニューを表示 --}}
                @if(
                    (auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) ||
                    (
                        auth(config('constants.GUARD_ADMIN'))->check() && 
                        (
                            !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')) || 
                            session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))
                        ) ||
                        auth(config('constants.GUARD_SCHOOL'))->check()
                    ) ||
                    (session(config('constants.SESSION_KEY_PSN_HIDE_FLG')) === config('constants.PSN_NO_HIDE_FLG')) ||
                    (auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
                )
                    <div id="masta" class="menu">
                        <h2>マスタ管理</h2>
                        <div>
                            @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
                                {{-- 管理者 かつ 代理ログインしていない場合 --}}
                                <a href="{{route('schools.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}"><div class="item"><div class="icon the-icons span3" title="Code: 0xe810"><i class="icons icon-chart-bar">&#xe810;</i> </div> 塾マスタ</div></a>
                            @endif
                            @if((auth(config('constants.GUARD_ADMIN'))->check() && 
                                    (!session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')) || session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL')))) ||
                                 auth(config('constants.GUARD_SCHOOL'))->check())
                                 {{-- 管理者 かつ 代理ログインしていない場合 もしくは 塾で代理ログイン または 塾アカウント --}}
                                <a href="{{route('classes.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}"><div class="item"><div class="icon the-icons span3" title="Code: 0xe809"><i class="icons icon-chart-bar">&#xe809;</i> </div> 教室マスタ</div></a>
                            @endif
                            {{-- 生徒機能非表示フラグが0の時 --}}
                            @if(session(config('constants.SESSION_KEY_PSN_HIDE_FLG')) === config('constants.PSN_NO_HIDE_FLG'))
                                <a href="{{route($routeUtil->getPrefix().'.persons.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}"><div class="item"><div class="icon the-icons span3" title="Code: 0xf2c3"><i class="icons icon-chart-bar">&#xf2c3;</i> </div> 生徒マスタ</div></a>
                            @endif
                            @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
                                {{-- 管理者 かつ 代理ログインしていない場合 --}}
                                <a href="{{route('csvUpload.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}"><div class="item"><div class="icon the-icons span3" title="Code: 0xf234"><i class="icons icon-chart-bar">&#xf234;</i> </div> 生徒CSV登録</div></a>
                                <a href="{{route('ClsCsvUpload.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}"><div class="item"><div class="icon the-icons span3" title="Code: 0xe805"><i class="icons icon-chart-bar">&#xe805;</i> </div> 教室CSV登録</div></a>
                                <a href="{{route('ExNumCsvUpload.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}"><div class="item"><div class="icon the-icons span3" title="Code: xe80a"><i class="icons icon-chart-bar">&#xe80a;</i> </div> 受験番号CSV登録</div></a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@include('layout.footer')