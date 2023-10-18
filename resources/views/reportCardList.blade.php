<!-- ファイルのディレクトリは仮配置です -->

@extends('layout.common')

@section('title', '成績表一覧')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')
@include('layout.header')

@inject('routeUtil','App\Utils\RouteUtil')

@section('content')
<script>
    function selectboxChange() {
        {{-- 成績一覧を非表示にする --}}
         $("[class^='classid']").css('display', 'none');
        
        {{-- 選択されたIndexの値を取得 --}}
        selindex = document.form.classBox.value;

        if(selindex){
            {{-- 選択されていたら該当データを表示する --}}
            $("[class='classid" + selindex + "']").css('display', 'block');
            
            {{-- 件数を設定する --}}
            $("#resultCount").text($("[class='classid" + selindex + "']").length);
        }
        else
        {
            {{-- 未選択 --}}
            $("[class^='classid']").css('display', 'block');

            {{-- 件数を設定する --}}
            $("#resultCount").text($("[class^='classid']").length);
        }
    };
</script>
<div id="site_content" class="seiseki">
    <div id="content">
        <h1>成績表一覧</h1>
        <div id="shikenmei"><p style="font-size: 14px;font-weight: bold;">試験名：{{ $year }}年度_{{ $testName }}_高{{ $grade }}</p></div>
        @if($resultCount>0)
        <form method="post" action="" name="form">
        @if((auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL')))
                        || auth(config('constants.GUARD_SCHOOL'))->check()
                )
        <div style="width:  292px; margin:  auto; margin-bottom:  1em">
            {{ Form::select('classList'
                            , $classBox
                            , null
                            , ['class' => 'cell'
                            ,'id' => 'classBox'
                            ,'onchange' => 'selectboxChange();']
                            ) 
            }}
        </div>
        @endif
        <div style="width:  292px; margin:  auto; margin-bottom:  1em">
            (<span id="resultCount">{{$resultCount}}</span>件表示)
        </div>
        <div id="se_List" class="List">
            <div id="output">
                <div class="default">
                @csrf
                <table>
                    <tbody>
                        @foreach ($directory as $key=>$val)
                        <tr class="classid{{ $val->cls_code }}">
                            <td><a href="{{ route($routeUtil->getPrefix().'.reportCard.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'index'=>$key]) }}" target="_blank">
                                    {{ $year }}年度_{{ $testName }}_高{{ $grade }}_{{$val->psn_code}}_{{$val->name}}.pdf
                                </a>
                            </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
                </div>
            </div>
        </div>
        </form> 
        @endif
        <div class="buttons text_right" style="margin-top:50px">
            <a class="button green" href="{{ route($routeUtil->getPrefix().'.testcountsearch.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">一覧に戻る</a>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')