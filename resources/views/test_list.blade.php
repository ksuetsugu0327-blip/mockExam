
@extends('layout.common')

@section('title', 'テスト一覧')
@section('keywords', '')
@section('description', 'テスト一覧')
@section('pageCss')
<style>
    .form-row {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 10px; /* Space between rows */
    }

    .form-row span {
        margin-right: 10px; /* Space between label and select */
    }
</style>
@endsection

@include('layout.head')
@include('layout.header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        <h1>テスト一覧</h1><br>
        <div id="s_search" class="search">
            <form method="post" action="#">
                @csrf
                <!-- <div class="form-group"> -->
                    <!-- Container for label and select dropdown -->
                    <div class="form-row">
                        <span for="year" style="margin-right:23px;">年度</span>
                        {{ Form::select(
                           'year', 
                           config('selectlistarray.YEAR_LIST'),
                           null, 
                           ['class' => 'cell',
                            'style' => 'width:100px']
                        )}}

                        <!-- Below the select dropdown, we have the input field -->
                        <span for="test_name" style="margin-left:23px">テスト</span>
                        <input type="text" name="test_name" id="test_name" value="">
                    </div>
                <!-- </div> -->
                <div class="buttons">
                    <input class="button bulu" type="submit" name="submit" value="検索">
                    <input id="inputClear" class="button green" type="button" name="reset" value="新規登録">
                </div>
            </form>
        </div>
        {{-- @if($resultCount>0) --}}
        <div id="sm_List" class="List2">
            <table class="mastertable">
                <!-- Table header -->
                <tbody>
                    <tr>
                        <th style="width: 22px;"></th>
                        <th style="width: 88px;">年度</th>
                        <th style="width: 88px;">テスト名</th>
                        <th style="width: 88px;">学年</th>
                        <th style="width: 88px;">大学データ</th>
                        <th style="width: 88px;">備考</th>
                    </tr>
                <!-- Table body -->
                    <!-- Example data rows - Replace these with your own data rows -->
                    <tr>
                        <td></td>
                        <td>2024</td>
                        <td style="text-decoration: underline;">
                            <a href="{{ route('test.details',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">テストA</a>
                        </td>
                        <td>1年生</td>
                        <td>データA</td>
                        <td>備考A</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>2023</td>
                        <td style="text-decoration: underline;">
                            <a href="{{ route('test.details',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">テストB</a>
                        </td>
                        <td>2年生</td>
                        <td>データB</td>
                        <td>備考B</td>
                    </tr>
                    <!-- Add more rows here as needed -->
                </tbody>
            </table>
        </div>
        {{-- <div id="se_List" class="List">
            @foreach ($directory as $key=>$val)
                <p>
                    <a href="#"> 
                        {{ $val["year"] }}年度_{{ $val["testName"] }}_高{{ $val["grade"] }}
                    </a>
                </p>
            @endforeach
        </div>
        @endif --}}
    </div>
</div>
@endsection
@include('layout.footer')