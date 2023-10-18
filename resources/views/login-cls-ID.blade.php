
@extends('layout.common')

@section('title', '')
@section('keywords', '')
@section('description', 'クラス用ログインチェック')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="idlogin">
    <div id="content">
        <h1>ログイン</h1>
        <div id="id_login" class="login">
        @if ($errors->has('message'))
             <div class="error_message">
                 <strong>{{ $errors->first('message') }}</strong>
             </div>
         @endif
            <form action="{{route('classIdCheck.check',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <input type="hidden" id="sch_code" name="sch_code" value="{{session(config('constants.SESSION_KEY_SCHCODE'))}}" />
                <table>
                    <tr><td class="jm_id">教室コード</td>
                        <td>
                            <input type="text" name="cls_code" value="{{old('cls_code')}}" required autofocus maxlength="4"/>
                            @if ($errors->has('cls_code'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('cls_code') }}</strong>
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
                <div><input class="button  bulu" type="submit" name="idcheck" value="コード確認" /></div>
            </form>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')