@extends('layout.common')

@section('title', '年度回数・一覧')
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
        <h1>年度回数・一覧</h1>
        @if (isset($message))
        <div class="error_message">
            <strong>{{ $message }}</strong>
        </div>
        <div></div>
        @endif
        <div id="s_search" class="search">
            <form method="post" action="{{route($routeUtil->getPrefix().'.ticketList.search',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}">
                @csrf
                <table>
                    <tr>
                        <td>
                            <p>塾</p>
                        </td>
                        <td>
                            <p>
                            @if ($viewMode == 1)
                                {{ Form::select('sch_code' 
                                    ,['' => '---'] + $sch_list
                                    ,$sch_code
                                    ,['class' => 'cell']
                                )}}
                            @else   
                                {{session(config('constants.SESSION_KEY_SCHCODE'))}}　{{session(config('constants.SESSION_KEY_SCHNAME'))}}
                            @endif
                            </p>
                        </td>
                        <td>
                        @if ($viewMode == 1 || $viewMode == 2)
                            <p>                           
                            </p>
                        @else
                            <p>教室</p>
                        @endif
                        </td>
                        <td class="select_td_width">
                        @if ($viewMode == 1 || $viewMode == 2)
                            <p>                                
                            </p>
                        @else
                            <p>
                                {{session(config('constants.SESSION_KEY_CLSCODE'))}}　{{session(config('constants.SESSION_KEY_CLSNAME'))}}
                            </p>
                        @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>年度</p>
                        </td>
                        <td>
                            <p>
                            @if ($viewMode == 4)                
                                    {{ Form::input('hidden', 'year', $year) }}
                                    {{ $year }}
                            @else
                                {{ Form::select('year' 
                                    ,config('selectlistarray.YEAR_LIST')
                                    ,$year
                                    ,['class' => 'cell']
                                )}}
                            @endif
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
            @if ($viewMode == 4)
                <div id="register_List_student" class="List">
                <ul>
            @else
                <div id="se_List" class="List">
            @endif
                    
            @foreach ($directory as $key => $val)
                @if ($viewMode == 4)
                        <li><span class="exam_name"><a href="{{ route('ticketRegist.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'index'=>$key]) }}">
                            {{ $val['year'] }}年度_{{ $val['testName'] }}_高{{ $val['grade'] }}
                        </a></span>
                            @if ($val['choiceflag'] == 1)
                                <span class="complete">登録済</span>
                            @else
                                <span class="incomplete">未登録</span>
                            @endif
                            @if ($val['reportflag'] == 1)
                                <span class="complete">返却済</span>
                            @else
                                <span class="incomplete">未返却</span>
                            @endif
                        </li>
                @else
                    <p>
                        <a href="{{ route($routeUtil->getPrefix() . '.ticketDetail.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'index'=>$key]) }}">
                            {{ $val['year'] }}年度_{{ $val['testName'] }}_高{{ $val['grade'] }}
                        </a>
                    </p>
                @endif
            @endforeach

            @if ($viewMode == 4)
                </ul>
            @endif
                </div>
        @endif
    </div>
</div>
@endsection

@include('layout.footer')