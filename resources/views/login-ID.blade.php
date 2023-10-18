<!-- ファイルのディレクトリは仮配置です -->

@extends('layout.common')

@section('title', 'ログイン')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="idlogin">
    <div id="content">
        <h1>ログイン</h1>
        <div id="id_login" class="login">
        @if (isset($message))
            <div class="error_message">
               <strong>{{ $message }}</strong>
            </div>
        @endif
            <form action="{{route('personIdCheck.check',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <input type="hidden" id="sch_code" name="sch_code" value="{{session(config('constants.SESSION_KEY_SCHCODE'))}}" />
                <table>
                    <tr><td class="jm_id">塾生ID</td>
                        <td>
                            <input type="text" name="psn_code" value="{{old('psn_code')}}" required autofocus maxlength="11"/>
                            @if ($errors->has('psn_code'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('psn_code') }}</strong>
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
                <div><input class="button  bulu" type="submit" name="idcheck" value="ID確認" /></div>
            </form>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')