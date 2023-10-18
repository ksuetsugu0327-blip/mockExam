@extends('layout.common')

@section('title', '教室ＣＳＶ登録')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="csvup">
    <div id="content">
        <h1>教室ＣＳＶ登録</h1>
        <div id="csv_up" class="upload">
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
            <form role="form" method="post" action="{{route('ClsCsvUpload.upload',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" enctype="multipart/form-data">
                <!-- CSRF保護 -->
                @csrf
                <table>
                    <tr>
                        <td>ＣＳＶファイル選択</td>
                        <td>
                            <label for="file_upload">ファイルを選択して下さい
                                <input type="text" id="file_upload" value="ファイル選択" onClick="$('#file').click();">
                                <input type="file" name="csv_file" id="file" onchange="$('#fake_text_box').val($(this).val());$('#submit_btn').prop('disabled',false);">
                            </label>
                        </td>
                    </tr>
               </table>
                <div>
                    <input type="text" id="fake_text_box" value="" size="35" readonly onClick="$('#file').click();">
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
                <div>
                    <input id="submit_btn" class="button  bulu" type="submit" name="uplode" value="CSVファイルアップロード">
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@include('layout.footer')