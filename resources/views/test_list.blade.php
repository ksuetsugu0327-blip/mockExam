
@extends('layout.common')

@section('title', 'テスト一覧')
@section('keywords', '')
@section('description', 'テスト一覧')
@section('pageCss')
<style>
    .form-row {
        display: flex;
        /*justify-content: center;*/
        align-items: center;
        margin-bottom: 10px; /* Space between rows */
    }

    .form-row span {
        margin-right: 10px; /* Space between label and select */
    }

    .top-right-link {
        text-align: right;
    }
</style>
@endsection

@include('layout.head')
@include('layout.header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        <h1>テスト一覧</h1><br>
        @if (isset($message))
        <div class="error_message">
            <strong>{{ $message }}</strong>
        </div>
        @endif
        <div class="top-right-link">新規登録は
            <a href="#" style="text-decoration: underline;">こちら</a>
        </div>
        <br>
        <div id="s_search" class="search">
            <form method="post" action="{{ route('test.search',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">
                @csrf
                <div class="form-group">
                    <!-- Container for label and select dropdown -->
                    <div class="form-row" style="gap:23px;">
                        <span for="year">年度</span>
                        {{ Form::select(
                           'year', 
                           $years,
                           $selectedYear, 
                           ['class' => 'cell',
                            'style' => 'width:100px']
                        )}}
                    </div>
                    <div class="form-row" style="gap:10px;">
                        <!-- Below the select dropdown, we have the input field -->
                        <span for="test_name">テスト</span>
                        <input type="text" name="test_name" id="test_name" value="{{ old('test_name', $testNameKeyword) }}" class="cell clear">
                        @if($errors->has('test_name'))<br> <span class="error">{{ $errors->first('test_name') }}</span> @endif
                    </div>
                </div>
                <div class="buttons">
                    <input class="button bulu" type="submit" name="submit" value="検索">
                    <input id="inputClear" class="button green" type="button" name="reset" value="クリア">
                </div>
            </form>
        </div>
        @if($resultCount>0)
        <div id="test_list" class="List2">
            <p>({{ $resultCount }}件表示)</p> 
            <table class="mastertable">
                <!-- Table header -->
                <tbody>
                    <tr>
                        <th style="width: 22px;"></th>
                        <th style="width: 88px;">年度</th>
                        <th style="width: 88px;">テスト名</th>
                        <th style="width: 88px;">学年</th>
                        <th style="width: 88px;">大学データ</th>
                        <th style="width: 88px;"></th>
                        <th style="width: 88px;"></th>
                    </tr>
                <!-- Table body -->
                    <!-- Example data rows - Replace these with your own data rows -->
                    @foreach($testList as $item)
                    <tr>
                        <td></td>
                        <td>{{$item->fiscal_year}}</td>
                        <td style="text-decoration: underline;">
                            <a href="{{ route('test.details',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">{{$item->test_name}}</a>
                        </td>
                        <td>{{$item->grade}}</td>
                        <td>{{$item->university_code_name}}</td>
                        <td><a href="{{ route('test.details',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">修正</a></td>
                        <td><a href="{{ route('test.details',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">削除</a></td>
                    </tr> 
                    @endforeach
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
            @endforeach --}}
        </div>
        @endif 
    </div>
</div>
@endsection
@include('layout.footer')