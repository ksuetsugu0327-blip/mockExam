@extends('layout.common')

@section('title', '試験回数検索・一覧')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .submit-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
</style>
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
        @php
            $showPublishCheckbox = "false";
            $isBoth = false;
            $isTbc = false;
            if((session(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')) === config('constants.PSN_REPORT_PUBLISH_FLG_SCHOOL'))&&
              (session(config('constants.SESSION_KEY_CLSCODE')) == null)){
                $showPublishCheckbox = "true";
            }
            if((session(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')) === config('constants.PSN_REPORT_PUBLISH_FLG_CLASS'))&&
              (session(config('constants.SESSION_KEY_CLSCODE')) != null)){
                $showPublishCheckbox = "true";
            }
            if(session(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')) === config('constants.PSN_REPORT_PUBLISH_FLG_BOTH')){
                $isBoth = true;            
                if(session(config('constants.SESSION_KEY_CLSCODE')) == null){
                    $showPublishCheckbox = "true";
              } else{
                $isTbc = true;
              }
            }
            //dd(session(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')), config('constants.PSN_REPORT_PUBLISH_FLG_SCHOOL'), config('constants.PSN_REPORT_PUBLISH_FLG_CLASS'), config('constants.PSN_REPORT_PUBLISH_FLG_BOTH'), $showPublishCheckbox);
        @endphp
        @if($resultCount>0)
        <div id="se_List" class="List">
            @if($showPublishCheckbox === "true" || $isTbc)
            <p style="display: flex; align-items: center;">
                <span style="width: 200px; display: inline-block;"> </span>
                <span style="margin-left: 10px;">
                    生徒公開
                </span>
            </p>
            @endif
            @foreach ($directory as $key=>$val)
                @php
                    $isDisabled = false;
                    if($isTbc){
                        if($val["psn_report_publish_flg"] === config('constants.PSN_REPORT_PUBLISH_NOT_USE_FLG')){
                            $isDisabled = true;
                        }
                    }
                    // if($key === 1) {
                    //     dd($showPublishCheckbox, $val["psn_report_publish_flg"], config('constants.PSN_REPORT_PUBLISH_NOT_USE_FLG'), $isDisabled, $isTbc);
                    // }
                @endphp
                <p style="display: flex; align-items: center;">
                    <a href="{{ route($routeUtil->getPrefix().'.reportCard.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'index'=>$key]) }}" style="width: 220px; display: inline-block;"> 
                        {{ $val["year"] }}年度_{{ $val["testName"] }}_高{{ $val["grade"] }}
                    </a>
                    @if($showPublishCheckbox === "true" || $isTbc)
                    <input type="checkbox" name="{{$val["year"]}}_{{ $val["cnttest"] }}_{{ $val["grade"] }}" style="margin-left: 10px;" onclick="handlePublishCheckbox(event, this);" {{ $val["psn_report_publish_flg"] === '1' ||(($val["psn_report_publish_flg"] === '2') && (session(config('constants.SESSION_KEY_CLSCODE')) == null ) && $isBoth) ? 'checked' : '' }} {{ $isDisabled ? 'disabled' : '' }}>
                    @endif
                </p>
            @endforeach
        </div>
        @endif
    </div>
</div>
<script>
    function handlePublishCheckbox(event,checkbox) {
        const publishFlag = '{{ session(config('constants.SESSION_KEY_PSN_REPORT_PUBLISH_FLG')) }}';
        const clsCode = '{{ session(config('constants.SESSION_KEY_CLSCODE')) }}';
        const message = checkbox.checked ? '選択した試験の個人成績表を公開しますか？':'選択した試験の個人成績表を非公開にしますか？';
        const confirmed = confirm(message);
        console.log('publishFlag:', publishFlag, 'clsCode:', clsCode);
        console.log('checkbox.checked:', checkbox.checked);
        if (clsCode === '') {
            console.log('clsCode is empty');
        }

        // console.log(event);
        // console.log(checkbox.checked);
        // console.log(checkbox.name.split('_'));
        if (!confirmed) {
            event.preventDefault();
        }

        if (confirmed) {
            // Add overlay to prevent double submit
            const overlay = document.createElement('div');
            overlay.className = 'submit-overlay';
            
            const spinner = document.createElement('div'); 
            spinner.className = 'loading-spinner';
            
            overlay.appendChild(spinner);
            document.body.appendChild(overlay);

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
            publishInput.value = getUpdateFlag(publishFlag, clsCode, checkbox.checked);
            
            //console.log('csrfToken:', csrfToken.value);
            //console.log('yearInput:', yearInput.value);
            //console.log('cnttestInput:', cnttestInput.value);
            //console.log('gradeInput:', gradeInput.value);
            //console.log('publishInput:', publishInput.value);

            form.appendChild(publishInput);

            document.body.appendChild(form);
            //console.log('checkbox.checked:', checkbox.checked);
            // Make API call to check publish flag before submitting
            if(!checkbox.checked && publishFlag === '{{ config('constants.PSN_REPORT_PUBLISH_FLG_BOTH') }}' && clsCode === ''){
                $.ajax({
                    url: '{{ route('check_publish_flg', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}',
                    method: 'GET',
                    data: {
                        sch_code: '{{ session(config('constants.SESSION_KEY_SCHCODE')) }}',
                        cls_code: '{{ session(config('constants.SESSION_KEY_CLSCODE')) }}',
                        year: year,
                        cnttest: cnttest, 
                        grade: grade
                    },
                    success: function(response) {
                        //console.log('response:', response);
                        // If API check passes, submit the form
                        if(response === 0){
                            form.submit();
                        } else {
                            // Remove any existing overlay
                            const existingOverlay = document.querySelector('.submit-overlay');
                            if (existingOverlay) {
                                existingOverlay.remove();
                            }
                            if(confirm('公開中の教室があります。公開を取り消して良いですか？')){
                                form.submit();
                                // Add overlay to prevent double submit
                                const overlay = document.createElement('div');
                                overlay.className = 'submit-overlay';
                                
                                const spinner = document.createElement('div'); 
                                spinner.className = 'loading-spinner';
                                
                                overlay.appendChild(spinner);
                                document.body.appendChild(overlay);
                            } else {
                                checkbox.checked = !checkbox.checked;
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('API Error:', error);
                    }
                });
            } else {
                form.submit();
            }
            
        }

        function getUpdateFlag(publishFlag, clsCode, checked){
            switch(publishFlag) {
                case '1':
                    return checked ? '1' : '0';
                case '2':
                    return checked ? '1' : '0';
                case '3':
                    if(checked){
                        return (!clsCode||clsCode === '') ? '2' : '1';
                    } else {
                        return (!clsCode||clsCode === '') ? '0' : '2';
                    }
                default:
                    return checked ? '1' : '0';
            }
        }
    }
</script>
@endsection

@include('layout.footer')