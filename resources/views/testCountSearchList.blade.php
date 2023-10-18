@extends('layout.common')

@section('title', '試験回数検索・一覧')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')
@include('layout.header')

@inject('routeUtil','App\Utils\RouteUtil')

@section('content')
<div id="site_content" class="shiken">
    <div id="content">
        <h1>試験回数検索・一覧</h1>
        @if (isset($message))
        <div class="error_message">
            <strong>{{ $message }}</strong>
        </div>
        <div></div>
        @endif
        <div id="s_search" class="search">
            <form method="post" action="{{route($routeUtil->getPrefix().'.testcountsearch.search',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}">
                @csrf
                <table>
                    <tr>
                        <td>
                        <p>塾</p>
                        </td>
                        <td class="select_td_width" >
                            <p>
                                {{session(config('constants.SESSION_KEY_SCHCODE'))}} {{session(config('constants.SESSION_KEY_SCHNAME'))}}
                            </p>
                        </td>
                        <td>
                            @if((auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'))) || 
                                auth(config('constants.GUARD_CLASS'))->check() )
                                <p>教室</p>
                            @endif
                        </td>
                        <td class="select_td_width">
                            <p>
                                @if((auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'))) || 
                                    auth(config('constants.GUARD_CLASS'))->check() )
                                    <label>{{session(config('constants.SESSION_KEY_CLSCODE'))}} {{session(config('constants.SESSION_KEY_CLSNAME'))}}</label>   
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <p>年度</p>
                        </td>
                        <td>
                            <p>
                            {{ Form::select('year' 
                                ,config('selectlistarray.YEAR_LIST')
                                ,$year
                                ,['class' => 'cell']
                             )}}
                            </p>
                        </td>
                        <td>
                        <p>学年</p>
                        </td>
                        <td>
                            <p>
                            {{ Form::select('grade'
                                ,config('selectlistarray.GRADE_LIST')
                                ,$grade
                                ,['class' => 'cell']
                             )}}
                            </p>
                        </td>
                    </tr>
                </table>
                <div class="buttons">
                    <p><input class="button bulu" type="submit" name="submit" value="検索"></p>
                    <p><input id="inputClear" class="button green" type="button" name="reset" value="クリア"></p>
                </div>
            </form>
        </div>
        @if($resultCount>0)
        <div id="se_List" class="List">
            @foreach ($directory as $key=>$val)
                <p>
                    <a href="{{ route($routeUtil->getPrefix().'.reportCard.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'index'=>$key]) }}"> 
                        {{ $val["year"] }}年度_{{ $val["testName"] }}_高{{ $val["grade"] }}
                    </a>
                </p>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection

@include('layout.footer')