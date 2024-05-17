
@extends('layout.common')

@section('title', '高校・大学CSV登録')
@section('keywords', '')
@section('description', '高校・大学CSV登録')
@section('pageCss')
<style>
    .container {
        max-width: 600px;
        margin: auto;
    }
    .form-group {
        margin-bottom: 15px;
        margin-left: 69px;
    }
    .btn-primary {
        width: 100%;
    }
    .form-check-inline {
        margin-right: 20px;
    }
    .top-right-link {
        text-align: right;
    }
    .disp_flex {
        display: flex;
        align-items: center;
    }
    .disp_flex label {
        margin-right: 20px;
    }
    .csv_upload_btn {
        margin-left: 185px;
        margin-bottom: 50px;
    }
</style>
@endsection
@section('pageScript')
<script>
$(document).ready(function() {
    const $radioButtons = $('input[name="data_type"]');
    const $year = $('#year');
    const $university_code_name_code = $('#university_code_name_code');
    const $dept_subject_div = $('#dept_subject_div');

    // Function to check the selected radio button and enable/disable the select element
    function checkRadioButtons() {
        let disableSelect = true;

        if ($(this).val() === "{{$codeData['UNIV_CODE']['VALUE']}}") { // Replace 'enable_select' with the appropriate value
            disableSelect = false;
        }

        $year.prop('disabled', disableSelect);
        $university_code_name_code.prop('disabled', disableSelect);
        $dept_subject_div.prop('disabled', disableSelect);
    }

    // Add event listeners to all radio buttons
    $radioButtons.on('change', checkRadioButtons);
});
</script>
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        {{-- 登録完了メッセージ --}}
        @if (isset($message))
            <div class="error_message">
                <strong>{{ $message }}</strong>
            </div>
        @endif
        @if ($errors->has('message'))
            <div class="error_message">
                <strong>{{ $errors->first('message') }}</strong>
            </div>
        @endif
        <h1>高校・大学コードCSV登録</h1>
        <div class="top-right-link">
           <a href="#" style="text-decoration: underline;">コードデータ削除</a>
        </div>
        <br>
        <form action="{{route('univ.upload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <div class="disp_flex">
                    <label>データ区分</label><br>
                    @foreach($codeData as $index => $item)
                    <div class="disp_flex form-check form-check-inline" style="gap:16px">
                        <input class="form-check-input" type="radio" name="data_type" id="university_code_{{ $index }}" value="{{ $item['VALUE'] }}">
                        <label class="form-check-label" for="university_code_{{ $index }}">{{ $item['NAME'] }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group disp_flex" style="gap:42px">
                <label for="year" style="margin-right:48px;">年度</label>
                <select class="form-control" id="year" name="fiscal_year">
                    <!-- Options should be populated dynamically -->
                    <option value="">選択してください</option>
                    @foreach($years as $year)
                    <option value="{{$year}}">{{$year}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group disp_flex" style="gap:42px">
                <label for="data_name">データ名</label>
                <select class="form-control" id="university_code_name_code" name="university_code_name_code" style="width:200px;">
                    <!-- Options should be populated dynamically -->
                    <option value="">選択してください</option>
                    @foreach($mUnivCodeName as $item)
                    <option value="{{$item->university_code_name_cd}}">{{$item->university_code_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group disp_flex" style="gap:42px">  
                <label for="department" style="margin-right:14px;">学部/学科</label>
                <select class="form-control" id="dept_subject_div" name="dept_subject_div">
                    <!-- Options should be populated dynamically -->
                    <option value="">選択してください</option>
                    @foreach($deptSubjSelect as $item)
                        <option value="{{$item['VALUE']}}">{{$item['NAME']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="csvup form-group disp_flex" style="gap:5px;float:left;">
                <table>
                    <tr>
                        <td>ＣＳＶファイル</td>
                        <td>
                            <label for="file_upload" style="margin-left:23px;">ファイルを選択して下さい
                                <input type="text" id="file_upload" value="ファイル選択" onClick="$('#file').click();">
                                <input type="file" class="form-control-file" name="csv_file" id="file" onchange="$('#fake_text_box').val($(this).val())">
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="csv_upload_btn">
                <input type="text" id="fake_text_box" value="" size="46" readonly onClick="$('#file').click();">
                {{-- エラーメッセージ表示 --}}
                    @if(count($errors) > 0)
                        <br>
                        @foreach($errors->all() as $error)
                            @if($loop->last)
                                {{-- 最後のメッセージはページ上部と重複するため出さない --}}
                            @else
                                <span class="error">{{ $error }}</span><br>
                            @endif
                        @endforeach
                    @endif
            </div>
            <div class="form-group disp_flex" style="justify-content: center;">
                <button type="submit" class="button bulu" style="width:193px;margin-right:55px;">CSVファイルアップロード</button>
            </div>
        </form>
    </div>
</div>
@endsection

@include('layout.footer')