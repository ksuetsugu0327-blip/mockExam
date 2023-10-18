@extends('layout.common')

@section('title', '生徒ＣＳＶ登録')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="csvup">
    <div id="content">
        <h1>生徒ＣＳＶ登録</h1>
        <div id="csv_up" class="upload">
            @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form action="">
                <table>
                    <tr>
                        <td>ＣＳＶファイル選択</td>
                        <td>
                            <label for="file_upload">ファイルを選択して下さい
                                <input type="text" id="file_upload" value="ファイル選択" onClick="$('#file').click();">
                                <input type="file" id="file" onchange="$('#fake_text_box').val($(this).val())">
                            </label>
                        </td>
                    </tr>
                </table>
                <div>
                    <input type="text" id="fake_text_box" value="" size="35" readonly onClick="$('#file').click();">
                </div>
                <div>
                    <input class="button  bulu" type="submit" name="login" value="CSVファイルアップロード" />
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@include('layout.footer')