@extends('layout.common')

@section('title', '')
@section('keywords', '')
@section('description', '塾用アカウントログイン用')
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
            <form action="{{route('schoolIdCheck.check',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <input type="hidden" id="sch_code_url" name="sch_code_url" value="{{session(config('constants.SESSION_KEY_SCHCODE'))}}" />
                <table>
                    <tr><td class="jm_id">塾コード</td>
                        <td>
                            <input type="text" name="sch_code" value="{{old('sch_code')}}" required autofocus maxlength="7"/>
                            @if ($errors->has('sch_code'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('sch_code') }}</strong>
                                </span>
                            @endif
                            @if ($errors->has('sch_code_org'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('sch_code_org') }}</strong>
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