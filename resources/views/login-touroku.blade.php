@extends('layout.common')

@section('title', 'ログイン')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="logtouroku">
    <div id="content">
        <h1>ログイン</h1>
        <div id="logtouroku" class="login">
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
                    <tr><td>塾生ID</td><td>xxxxxxxx</td></tr>
                    <tr><td>新しいパスワード</td><td><input type="text" name="jukuid" value="" /></td></tr>
                    <tr><td>新しいパスワード確認</td><td><input type="text" name="jukuid" value="" /></td></tr>
                </table>
                <div><input class="button  bulu" type="submit" name="login" value="登録" /></div>
            </form>
        </div>
    </div>
</div>

@endsection

@include('layout.footer')