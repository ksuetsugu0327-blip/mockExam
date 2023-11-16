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
                <div class="row">
                    <div class="student_top_menu">
                        <a href="{{route('personReportCardList.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}">
                            <div class="item">
                                <div class="icon the-icons span3" title="Code: 0xf0f6"><i 
                                    class="icons icon-chart-bar">&#xf0f6;</i></div>
                                個人成績表ダウンロード
                            </div>
                        </a>

                        @if(session(config('constants.SESSION_KEY_PSN_CARD_REGIST_FLG')) === config('constants.PSN_CARD_NO_HIDE_FLG'))
                        <a href="{{route($routeUtil->getPrefix().'.ticketList.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}">
                            <div class="item">
                                <div class="icon the-icons span3" title="Code: 0xf234"><i 
                                    class="icons icon-chart-bar">&#xf234;</i></div>
                                個人登録票
                            </div>
                        </a>
                        @endif

                        @if(session(config('constants.SESSION_KEY_PSN_PSW_CHANGE_FLG')) === config('constants.PSN_CHANGE_PW_NO_HIDE_FLG'))
                        <a href="{{route('personPasswordChange.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}">
                            <div class="item">
                                <div class="icon the-icons span3" title="Code:  0xe801"><i 
                                    class="icons  icon-edit">&#xe801;</i></div>
                                パスワード変更
                            </div>
                        </a>
                        <a href="{{route('personMailChange.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}">
                            <div class="item">
                                <div class="icon the-icons span3" title="Code:  0xe801"><i 
                                    class="icons  icon-edit">&#xe801;</i></div>
                                メールアドレス変更
                            </div>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')
