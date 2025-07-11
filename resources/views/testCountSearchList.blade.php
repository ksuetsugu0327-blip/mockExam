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
            @if(session(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')) == config('constants.PSN_REPORT_PUBLISH_USE_FLG'))
            <p style="display: flex; align-items: center;">
                <span style="width: 200px; display: inline-block;"> </span>
                <span style="margin-left: 10px;">
                    生徒公開
                </span>
            </p>
            @endif
            @foreach ($directory as $key=>$val)
                <p style="display: flex; align-items: center;">
                    <a href="{{ route($routeUtil->getPrefix().'.reportCard.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'index'=>$key]) }}" style="width: 220px; display: inline-block;"> 
                        {{ $val["year"] }}年度_{{ $val["testName"] }}_高{{ $val["grade"] }}
                    </a>
                    @if(session(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')) == config('constants.PSN_REPORT_PUBLISH_USE_FLG'))
                    <input type="checkbox" name="{{$val["year"]}}_{{ $val["cnttest"] }}_{{ $val["grade"] }}" style="margin-left: 10px;" onclick="handlePublishCheckbox(event, this);" {{ $val["psn_report_publish_flg"] == '1' ? 'checked' : '' }}>
                    @endif
                </p>
            @endforeach
        </div>
        @endif
    </div>
</div>
<script>
    function handlePublishCheckbox(event,checkbox) {
        const message = checkbox.checked ? '選択した試験の個人成績表を公開しますか？':'選択した試験の個人成績表を非公開しますか？';
        const confirmed = confirm(message);

        console.log(event);
        console.log(checkbox.checked);
        console.log(checkbox.name.split('_'));
        if (!confirmed) {
            event.preventDefault();
        }

        if (confirmed) {
            const [year, cnttest, grade] = checkbox.name.split('_');
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("sch.testcountsearch.update", ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add year input
            const yearInput = document.createElement('input');
            yearInput.type = 'hidden';
            yearInput.name = 'year';
            yearInput.value = year;
            form.appendChild(yearInput);

            // Add cnttest input  
            const cnttestInput = document.createElement('input');
            cnttestInput.type = 'hidden';
            cnttestInput.name = 'cnttest';
            cnttestInput.value = cnttest;
            form.appendChild(cnttestInput);

            // Add grade input
            const gradeInput = document.createElement('input');
            gradeInput.type = 'hidden';
            gradeInput.name = 'grade';
            gradeInput.value = grade;
            form.appendChild(gradeInput);

            // Add publish flag input
            const publishInput = document.createElement('input');
            publishInput.type = 'hidden';
            publishInput.name = 'psn_report_publish_flg';
            publishInput.value = checkbox.checked ? '1' : '0';
            form.appendChild(publishInput);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection

@include('layout.footer')