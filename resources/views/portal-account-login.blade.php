@extends('layout.common')

@section('title', '')
@section('keywords', '')
@section('description', 'クラス用ログインチェック')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.portal-header')

@section('content')
    <div id="portal-content" class="idlogin">
        <div id="content">
            <h1>{{$account_title}}</h1>
            <div id="id_login" class="login">
                @if ($errors->has('message'))
                    <div class="error_message">
                        <strong>{{ $errors->first('message') }}</strong>
                    </div>
                @endif
                <form action="{{route('PortalAccount.check',['account'=>$account])}}"
                      method="post">
                    @csrf
                    <table>
                        <tr>
                            <td class="jm_id">塾アカウント名</td>
                            <td>
                                <input type="text" name="portal_account" value="{{old('portal_account')}}" required
                                       autofocus/>
                                @if ($errors->has('portal_account'))
                                    <br/>
                                    <span class="error">
                                    <strong>{{ $errors->first('portal_account') }}</strong>
                                </span>
                                @endif
                            </td>
                        </tr>
                    </table>
                    <div><input class="button bulu" type="submit" name="account_check" value="アカウント確認"/></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@include('layout.portal-footer')