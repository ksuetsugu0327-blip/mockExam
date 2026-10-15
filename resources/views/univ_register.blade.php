
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
    const $universityCodeNameCode = $('#university_code_name_code');
    const $deptSubjectDiv = $('#dept_subject_div');
//;
    // Function to check the selected radio button and enable/disable the select element
    function checkRadioButtons() {
        let disableSelect = true;
        let required = false;

        if ($(this).val() === "{{$codeData['UNIV_CODE']['VALUE']}}") { // Replace 'enable_select' with the appropriate value
            disableSelect = false;
            required = true;
        }

        $year.prop('required', required).prop('disabled', disableSelect).val('');
        $universityCodeNameCode.prop('required', required).prop('disabled', disableSelect).val('');
        $deptSubjectDiv.prop('required', required).prop('disabled', disableSelect).val('');
    }

    // Add event listeners to all radio buttons
    $radioButtons.on('change', checkRadioButtons);

    var errCsvLink = $('#err_csv_link');
    if (errCsvLink.length && $.isNumeric($('#err_csv_link').html())) {
        const mode = $('#err_csv_link').html().trim();
        var url = "{{route('univ.err_csv', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')), 'mode' => ':parameter'])}}"
        url = url.replace(':parameter', encodeURIComponent(mode));
        console.log(mode, url);
        window.location.href=url
    }
});
//バリデーションメッセージカスタマイズ
$(function(){
/*
//data_type

   $('input[name="data_type"]') 
    .on("invalid", function(e){
      if ( e.currentTarget.validity.valueMissing ) {
        this.setCustomValidity("データ区分を選択してください。");
      }
    })
//メッセージリセット
   .on("input",function(){
    this.blur();
    this.setCustomValidity("");
    this.focus();
    });
*/
//year
    $('#year')
    .on("invalid", function(e){
      if ( e.currentTarget.validity.valueMissing ) {
        this.setCustomValidity("年度を入力してください。");
      }
    })
//メッセージリセット
    .on("input",function(){
    this.blur();
    this.setCustomValidity("");
    this.focus();
    });
//university_code_name_code
    $('#university_code_name_code')
    .on("invalid", function(e){
      if ( e.currentTarget.validity.valueMissing ) {
        this.setCustomValidity("データ名を選択してください。");
      }
    })
//メッセージリセット
    .on("input",function(){
    this.blur();
    this.setCustomValidity("");
    this.focus();
    });
//dept_subject_div
    $('#dept_subject_div')
    .on("invalid", function(e){
      if ( e.currentTarget.validity.valueMissing ) {
        this.setCustomValidity("学部/学科を選択してください。");
      }
    })
//メッセージリセット
    .on("input",function(){
    this.blur();
    this.setCustomValidity("");
    this.focus();
    });
});

</script>
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        {{-- 登録完了メッセージ --}}
        @if (session()->has('message'))
            <div class="error_message">
                <strong>{{ session('message') }}</strong>
            </div>
        @endif
        @if ($errors->has('message'))
            <div class="error_message">
                <strong>{{ $errors->first('message') }}</strong>
            </div>
        @endif
        <h1>高校・大学コードCSV登録</h1>
        <div class="top-right-link">
           <a href="{{ route('univ_code.list', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" style="text-decoration: underline;">大学コードデータ一覧</a>
        </div>
        <br>
        <form id ="univCodeCSVUploadForm" action="{{route('univ.upload', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <div class="disp_flex">
                    <label>データ区分</label><br>
                    @foreach($codeData as $index => $item)
                    <div class="disp_flex form-check form-check-inline" style="gap:16px">
                        <input class="form-check-input" type="radio" name="data_type" id="university_code_{{ $index }}" value="{{ $item['VALUE'] }}" {{ old('data_type') == $item['VALUE'] ? 'checked' : '' }} @if($loop->iteration == 1) checked required @endif>
                        <label class="form-check-label" for="university_code_{{ $index }}">{{ $item['NAME'] }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group disp_flex" style="gap:42px">
                <label for="year" style="margin-right:48px;">年度</label>
                <select class="form-control" id="year" name="fiscal_year" {{ old('data_type') == 2 ? 'disabled' : '' }} required>
                    <!-- Options should be populated dynamically -->
                    <option value="">選択してください</option>
                    @foreach($years as $year)
                    <option value="{{$year}}" {{ old('fiscal_year') == $year ? 'selected' : '' }}>{{$year}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group disp_flex" style="gap:42px">
                <label for="university_code_name_code">データ名</label>
                <select class="form-control" id="university_code_name_code" name="university_code_name_code" style="width:200px;" {{ old('data_type') == 2  ? 'disabled' : '' }} required>
                    <!-- Options should be populated dynamically -->
                    <option value="">選択してください</option>
                    @foreach($mUnivCodeName as $item)
                    <option value="{{$item->university_code_name_cd}}" {{ old('university_code_name_code') == $item->university_code_name_cd ? 'selected' : '' }}>{{$item->university_code_name}}</option>
                    @endforeach
                </select>
                <input type="hidden" id="univ_code_head_updated_at" name="univ_code_head_updated_at" value="" />
            </div>
            <div class="form-group disp_flex" style="gap:42px">  
                <label for="dept_subject_div" style="margin-right:14px;">学部/学科</label>
                <select class="form-control" id="dept_subject_div" name="dept_subject_div" {{ old('data_type') == 2  ? 'disabled' : '' }} required>
                    <!-- Options should be populated dynamically -->
                    <option value="">選択してください</option>
                    @foreach($deptSubjSelect as $item)
                        <option value="{{$item['VALUE']}}" {{ old('dept_subject_div') == $item['VALUE'] ? 'selected' : '' }}>{{$item['NAME']}}</option>
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
                                @if($error == 1)
                                    <button id="err_csv_link" type="button" style="display: none;">{{$error}}</button>       
                                @elseif($error == 2)
                                    <button id="err_csv_link" type="button" style="display: none;">{{$error}}</button>                         
                                @endif
                            @else
                                <span class="error">{{ $error }}</span><br>
                            @endif
                        @endforeach
                    @endif
            </div>
            <div class="form-group disp_flex" style="justify-content: center;">
                <button id="univCodeCSVUploadBtn" type="button" class="button bulu" style="width:193px;margin-right:55px;">CSVファイルアップロード</button>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        var form = $("#univCodeCSVUploadForm");
            
        $('#univCodeCSVUploadBtn').on('click', function(event) {
            //event.preventDefault();

            // バリデーションを実行
            if(form[0].reportValidity()){
                //高校の時はそのままサブミット
                if ($("input[name='data_type']:checked").val() == 2) {
                    $('#univCodeCSVUploadForm').submit();
                } else {
                    var formData = {
                        fy: $('#year').val(),
                        dtcd: $('#university_code_name_code').val(),
                        dptsub: $('#dept_subject_div').val()
                    };
                    
                    $.ajax({
                        url: "{{ route('api.test_management.university_code_heads_check') }}", // Route defined in web.php
                        type: 'GET',
                        data: formData,
                        success: function(response) {
                            let dataNum = response.header.data_num;
// console.log(response);
                            if(dataNum !== 0) { // 大学コードヘッダーが存在しているので上書き更新の確認。
                                if(!confirm("入力された年度、データ名、学部/学科の大学データは登録済です。\nデータ登録は上書きで行います。")){
                                    $('#year').focus();
                                    return;
                                }
                            }
                            if(dataNum > 1){ // 大学コードヘッダー情報が複数存在しているはずがないので確認必要、アップロードを中止する。
                                alert("指定された条件で大学コードヘッダー情報が複数存在しています。ご確認ください。");
                                return;
                            }
                            if (response.data[0] && response.data[0].updated_at){ // 大学コードヘッダーが存在する場合、最終更新日時をパラメータに追加する。
                                $('#univ_code_head_updated_at').val(response.data[0].updated_at);
                            }

                            // CSVアップロードフォームをPOSTする。
                            $('#univCodeCSVUploadForm').submit(); 
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            alert("大学コードヘッダー情報取得できません。システムエラーが発生しました。");
                        }
                    });
                } //else
            }
        });
    });
</script>
@endsection

@include('layout.footer')