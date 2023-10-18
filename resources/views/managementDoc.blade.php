@extends('layout.common')

@section('title', '管理資料一覧')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')
@include('layout.header')

@inject('routeUtil','App\Utils\RouteUtil')

@section('content')
<div id="site_content" class="kanri">
    <div id="content" style="margin-bottom: 0px;">
        <h1>管理資料一覧</h1>
        <div id="shikenmei">
            <p style="font-size: 14px;font-weight: bold;">試験名：{{ $year }}年度_{{ $testName }}_高{{ $grade }}</p>
        </div>
        @if (isset($message))
        <div class="error_message">
            <strong>{{ $message }}</strong>
        </div>  
        @endif
        <script>
        function pwdchg(){
           document.getElementById('dload').style.display="inline-block";
           document.getElementById('guidelines').style.display="none";
        }
        function pwdchgcsv(){
           document.getElementById('dload_csv').style.display="inline-block";
           document.getElementById('guidelines_csv').style.display="none";
        }
        @if(isset($zip) && !isset($zipfilepass))
            setTimeout("location.reload(true)",10000);
        @endif
        @if(isset($zip_csv) && !isset($zipfilepass_csv))
            setTimeout("location.reload(true)",10000);
        @endif
        </script>
        
        <div id="ka_List" class="List">
            <div class="kaList">
                @if(
                    (
                        (
                        (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) 
                        || auth(config('constants.GUARD_SCHOOL'))->check() 
                        ) && (isset($ls1) || isset($ls2) || isset($sbj) || isset($cls) || isset($cmt) || isset($dat))
                    )
                ||
                    (
                        (
                        (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'))) 
                        || auth(config('constants.GUARD_CLASS'))->check() 
                        ) && (isset($lc1) || isset($lc2) || isset($sbj)  || isset($cmt) || isset($dat))
                    )
                )
                <div class="bulu_List">
                    @if(
                        (
                        (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) 
                        || auth(config('constants.GUARD_SCHOOL'))->check()
                        ) && isset($ls1))
                    <p>
                        <a href="{{ route($routeUtil->getPrefix().'.managementdoc.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'$category_kbn'=>config('constants.FILE_KBN_LS1')]) }}" target="_blank">
                            {{ $year }}年度_{{ $testName }}_高{{ $grade }}_会場内成績一覧表（科目編）.pdf
                        </a>
                    </p>
                    @endif
                    @if(
                        (
                        (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) 
                        || auth(config('constants.GUARD_SCHOOL'))->check()
                        ) && isset($ls2))
                    <p>
                        <a href="{{ route($routeUtil->getPrefix().'.managementdoc.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'$category_kbn'=>config('constants.FILE_KBN_LS2')]) }}" target="_blank">
                            {{ $year }}年度_{{ $testName }}_高{{ $grade }}_会場内成績一覧表（総合）.pdf
                        </a>
                    </p>
                    @endif
                    @if(
                        (
                        (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'))) 
                        || auth(config('constants.GUARD_CLASS'))->check() 
                        ) && isset($lc1))
                    <p>
                        <a href="{{ route($routeUtil->getPrefix().'.managementdoc.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'$category_kbn'=>config('constants.FILE_KBN_LC1')]) }}" target="_blank">
                            {{ $year }}年度_{{ $testName }}_高{{ $grade }}_教室内成績一覧表（科目編）.pdf
                        </a>
                    </p>
                    @endif
                    @if(
                        (
                        (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'))) 
                        || auth(config('constants.GUARD_CLASS'))->check() 
                        ) && isset($lc2))
                    <p>
                        <a href="{{ route($routeUtil->getPrefix().'.managementdoc.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'$category_kbn'=>config('constants.FILE_KBN_LC2')]) }}" target="_blank">
                            {{ $year }}年度_{{ $testName }}_高{{ $grade }}_教室内成績一覧表（総合）.pdf
                        </a>
                    </p>
                    @endif
                    @if(isset($sbj))
                    <p>
                        <a href="{{ route($routeUtil->getPrefix().'.managementdoc.pdfDownload',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')), '$category_kbn'=>config('constants.FILE_KBN_SBJ')]) }}" target="_blank">
                            {{ $year }}年度_{{ $testName }}_高{{ $grade }}_科目別学力分析表.pdf
                        </a>
                    </p>
                    @endif
                    @if(
                        (
                        (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) 
                        || auth(config('constants.GUARD_SCHOOL'))->check() 
                        ) && isset($cls))
                    <p>
                        <a href="{{ route($routeUtil->getPrefix().'.managementdoc.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')), '$category_kbn'=>config('constants.FILE_KBN_CLS')]) }}" target="_blank">
                           {{ $year }}年度_{{ $testName }}_高{{ $grade }}_教室別成績比較表.pdf
                        </a>
                    </p>
                    @endif
                    @if(isset($cmt))
                    <p>
                        <a href="{{ route($routeUtil->getPrefix().'.managementdoc.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'$category_kbn'=>config('constants.FILE_KBN_CMT')]) }}" target="_blank">
                            {{ $year }}年度_{{ $testName }}_高{{ $grade }}_解答解説.pdf
                        </a>
                    </p>
                    @endif
                    @if(isset($dat))
                    <p>
                        <a href="{{ route($routeUtil->getPrefix().'.managementdoc.pdfDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'$category_kbn'=>config('constants.FILE_KBN_DAT')]) }}" target="_blank">
                            {{ $year }}年度_{{ $testName }}_高{{ $grade }}_データ集.pdf
                        </a>
                    </p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    <div id="content_down_width_manage" style="margin-top:20px;margin-bottom:10px;{{!isset($dir)?'display:none':''}}" class="text_left">
        @if(!isset($zip))
                <div style="background : url('/png/pdf_icon(edge).png') no-repeat;background-size : cover;height : 32px; width : 32px;display:inline-block;margin-left: -32px;position: absolute;"></div><label style="display:inline-block;width: 320px;">{{ $year }}年度_{{ $testName }}_高{{ $grade }}_<b>個人成績表PDF</b>一括</label>
                <input class="down_button_content_fit bulu" type="button" onclick="location.href='{{ route($routeUtil->getPrefix().'.managementdoc.order', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}'" value="作成" {{(isset($zip_csv)&&($zip_csv->process_kbn)==0)?'disabled':''}}>
        @else
                <div style="background : url('/png/pdf_icon(edge).png') no-repeat;background-size : cover;height : 32px; width : 32px;display:inline-block;margin-left: -32px;position: absolute;"></div><label style="display:inline-block;width: 320px;">{{ $year }}年度_{{ $testName }}_高{{ $grade }}_<b>個人成績表PDF</b>一括</label>
                <input class="down_button_content_fit bulu" type="button" onclick="location.href='{{ route($routeUtil->getPrefix().'.managementdoc.order', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}'" value="{{ $zip->process_kbn == '2' ? '作成済' : '作成中' }}" disabled>
        @endif
        @if(isset($zipfilepass))
            <input class="openModal choice down_button_content_fit bulu" type="button" onclick="location.href='{{ route($routeUtil->getPrefix().'.managementdoc.zipDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}'; pwdchg();javascript:void(0); return false;" value="ダウンロード" >
            <p id="dload" style="display: none;" ><label　 style="text-align: left;">解凍パスワード：{{ $zipfilepass }}</label></p>
        @else
            <input class="openModal choice down_button_content_fit bulu" type="button" onclick="location.href='{{ route($routeUtil->getPrefix().'.managementdoc.zipDownload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}'; pwdchg();javascript:void(0); return false;" value="ダウンロード" disabled>
        @endif
        @if(!isset($zipfilepass))
            <label id="guidelines" style="display:inline;">作成が完了するとダウンロードが可能になります。</label>
        @else
            <label id="guidelines" style="display:inline;">ダウンロードすると解凍パスワードが表示されます。</label>
        @endif
    </div>
    <div id="content_down_width_manage" style="{{!isset($dir)?'margin-top:20px;':''}}margin-bottom:10px;{{!isset($csv_dir)?'display:none':''}}" class="text_left">
        @if(!isset($zip_csv))
                <div style="background : url('/png/csv_icon(excel).png') no-repeat;background-size : cover;height : 32px; width : 32px;display:inline-block;margin-left: -32px;position: absolute;"></div><label style="display:inline-block;width:320px;">{{ $year }}年度_{{ $testName }}_高{{ $grade }}_<b>成績データCSV</b>一括</label>
                <input class="down_button_content_fit bulu" type="button" onclick="location.href='{{ route($routeUtil->getPrefix().'.managementdoc.orderCSV', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}'" value="作成" {{(isset($zip)&&($zip->process_kbn)==0)?'disabled':''}}>
        @else
                <div style="background : url('/png/csv_icon(excel).png') no-repeat;background-size : cover;height : 32px; width : 32px;display:inline-block;margin-left: -32px;position: absolute;"></div><label style="display:inline-block;width:320px;">{{ $year }}年度_{{ $testName }}_高{{ $grade }}_<b>成績データCSV</b>一括</label>
                <input class="down_button_content_fit bulu" type="button" onclick="location.href='{{ route($routeUtil->getPrefix().'.managementdoc.orderCSV', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}'" value="{{ $zip_csv->process_kbn == '2' ? '作成済' : '作成中' }}" disabled>
        @endif
        @if(isset($zipfilepass_csv))
            <input class="openModal choice down_button_content_fit bulu" type="button" onclick="location.href='{{ route($routeUtil->getPrefix().'.managementdoc.zipDownloadCSV', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}'; pwdchgcsv();javascript:void(0); return false;" value="ダウンロード" >
            <p id="dload_csv" style="display: none;" ><label　 style="text-align: left;">解凍パスワード：{{ $zipfilepass_csv }}</label></p>
        @else
            <input class="openModal choice down_button_content_fit bulu" type="button" onclick="location.href='{{ route($routeUtil->getPrefix().'.managementdoc.zipDownloadCSV', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}'; pwdchg();javascript:void(0); return false;" value="ダウンロード" disabled>
        @endif
        @if(!isset($zipfilepass_csv))
            <label id="guidelines_csv" style="display:inline;">作成が完了するとダウンロードが可能になります。</label>
        @else
            <label id="guidelines_csv" style="display:inline;">ダウンロードすると解凍パスワードが表示されます。</label>
        @endif
    </div>
    <div class="buttons text_right" style="width:600px;margin-top:20px;">
        <div style="text-align:left;margin-left:10px;">
            <b>【Windowsの場合】</b><br>
            ダウンロードファイルを解凍する際は右クリックから「すべて展開」を選択してください。<br>
            （特定の解凍ソフトを利用するとファイル名が崩れて表示される場合があります。）<br>
            上記操作でもファイル名が崩れて表示される場合は下記推奨の解凍ソフトをお使いください。<br>
            ※推奨 7-zip(UTF-8対応)
        </div>
            <a class="button green" href="{{ route($routeUtil->getPrefix().'.management.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">一覧に戻る</a>
    </div>
</div>
@endsection

@include('layout.footer')
