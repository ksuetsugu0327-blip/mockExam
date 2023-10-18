@extends('layout.common')

@section('title', '個人成績表一覧')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="stseiseki">
    <div id="content">
        <h1>個人成績表一覧</h1>
        @if (isset($message))
        　　<div class="error_message">
         　　   <strong>{{ $message }}</strong>
        　　</div>
        @endif
        <div id="st_List" class="List">
            <p>(<span id="resultCount">{{$resultCount}}</span>件表示)</p>
             <script>
                function selectboxChange() {
                    {{-- 一覧を非表示にする --}}
                    $("[class^='listData']").css('display', 'none');
                    
                    {{-- 選択されたIndexの値を取得 --}}
                    selindex = document.form.yearList.value;
                    
                    if(selindex){
                        {{-- 選択されていたら該当データを表示する --}}
                        $("[class='listData" + selindex + "']").css('display', '');
                        
                        {{-- 件数を設定する --}}
                        $("#resultCount").text($("[class='listData" + selindex + "']").length);
                    }
                    else
                    {
                        {{-- 未選択 --}}
                        $("[class^='listData']").css('display', '');

                        {{-- 件数を設定する --}}
                        $("#resultCount").text($("[class^='listData']").length);
                    }
                };
            </script>
            <form name="form" action="">
            @csrf
            <span>年度</span>
            {{ Form::select(
                'year' 
                ,config('selectlistarray.YEAR_LIST')
                ,""
                ,['class' => 'cell'
                ,'id' => 'yearList'
                ,'onchange' => 'selectboxChange();']
            )}}
            </form>
            @if($resultCount>0)
            <div id="output" style="width:768px;">
                <div class="default">
                    <table>
                        @foreach ($directory as $key=>$val)
                        <tr class="listData{{ $val["year"] }}">
                            <td><a href="{{ route('personReportCardList.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'index'=>$key,'category_kbn'=>config('constants.FILE_KBN_PSN') ]) }}" target="_blank"> {{ $val["year"] }}年度_{{ $val["testName"] }}_高{{ $val["grade"] }}.pdf</a></td>
                            <td>
                            @if(isset($val["cmtfullpath"]))
                            <a href="{{ route('personReportCardList.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'index'=>$key,'category_kbn'=>config('constants.FILE_KBN_CMT') ]) }}" target="_blank" class="d_date, r_link">解答解説</a>
                            @endif
                            </td>
                            <td>
                            @if(isset($val["datfullpath"]))
                            <a href="{{ route('personReportCardList.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'index'=>$key,'category_kbn'=>config('constants.FILE_KBN_DAT') ]) }}" target="_blank" class="d_date, r_link">データ集</a>
                            @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@include('layout.footer')