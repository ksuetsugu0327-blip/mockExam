
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

@include('layout.head')

@include('layout.header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        <h1>高校・大学コードCSV登録</h1>
        <div class="top-right-link">
           <a href="#" style="text-decoration: underline;">コードデータ削除</a>
        </div>
        <br>
        <div class="form-group">
            <div class="disp_flex">
                <label>データ区分</label><br>
                <div class="disp_flex form-check form-check-inline" style="gap:16px">
                    <input class="form-check-input" type="radio" name="data_type" id="university_code" value="university_code" checked>
                    <label class="form-check-label" for="university_code">大学コードデータ</label>
                </div>
                <div class="disp_flex form-check form-check-inline" style="gap:16px">
                    <input class="form-check-input" type="radio" name="data_type" id="highschool_code" value="highschool_code">
                    <label class="form-check-label" for="highschool_code">高校コードデータ</label>
                </div>
            </div>
        </div>
        <form action="/upload_csv" method="post" enctype="multipart/form-data">
            <div class="form-group disp_flex" style="gap:42px">
                <label for="year" style="margin-right:48px;">年度</label>
                <select class="form-control" id="year" name="year">
                    <!-- Options should be populated dynamically -->
                    <option value="">選択してください</option>
                </select>
            </div>
            <div class="form-group disp_flex" style="gap:42px">
                <label for="data_name">データ名</label>
                <select class="form-control" id="data_name" name="data_name" style="width:200px;">
                    <!-- Options should be populated dynamically -->
                    <option value="">選択してください</option>
                </select>
            </div>
            <div class="form-group disp_flex" style="gap:42px">  
                <label for="department" style="margin-right:14px;">学部/学科</label>
                <select class="form-control" id="department" name="department">
                    <!-- Options should be populated dynamically -->
                    <option value="">選択してください</option>
                </select>
            </div>
            <div class="csvup form-group disp_flex" style="gap:5px;float:left;">
                <table>
                    <tr>
                        <td>ＣＳＶファイル</td>
                        <td>
                            <label for="file_upload" style="margin-left:23px;">ファイルを選択して下さい
                                <input type="file" class="form-control-file" id="file" onchange="$('#fake_text_box').val($(this).val())">
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="csv_upload_btn">
                <input type="text" id="fake_text_box" value="" size="46" readonly onClick="$('#file').click();">
            </div>
            <div class="form-group disp_flex" style="justify-content: center;">
                <button type="submit" class="button bulu" style="width:193px;margin-right:55px;">CSVファイルアップロード</button>
            </div>
        </form>
    </div>
</div>
@endsection

@include('layout.portal-footer')