
@extends('layout.common')

@section('title', 'テスト詳細')
@section('keywords', '')
@section('description', 'テスト詳細')
@section('pageCss')
<style>
    /* Apply styles to the table */
    .test-detail {
        border-collapse: separate; /* Make sure border-collapse is separate */
        /* border-spacing: 5px; Adjust the space between cells */
        margin:unset;
    }

    /* Optionally, apply padding inside each cell */
    .test-detail td {
        padding: 10px; /* Add padding inside the cell */
    }
    .button.grey{
        background-color: grey;
    }
</style>
@endsection

@include('layout.head')

@include('layout.portal-header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        <h1>テスト詳細</h1>
        <div class="form-group">
            <table class="test-detail">
                <tr>
                    <td><span for="sch" style="margin: 0 23px 0 23px;">塾</span></td>
                    <td>
                        {{ Form::select(
                            'sch', 
                            config('selectlistarray.SCHOOL_LIST'),
                            null, 
                            ['class' => 'cell',
                             'style' => 'width:70px;margin-right:87px;']
                        )}}
                    </td>
                    <td><span for="year" style="margin-right:23px;">年度</span></td>
                    <td>{{ Form::select(
                            'year', 
                            config('selectlistarray.YEAR_LIST'),
                            null, 
                            ['class' => 'cell',
                             'style' => 'width:100px']
                        )}}
                    <td>
                </tr>
                <tr>            
                    <td><span for="year" style="margin: 0 23px 0 23px;">学年</span></td>
                    <td>{{ Form::select(
                            'year', 
                            config('selectlistarray.GRADE_LIST'),
                            null, 
                            ['class' => 'cell',
                            'style' => 'width:100px']
                        )}}
                    </td>
                </tr>
                <tr> 
                    <td><span for="year" style="margin: 0 23px 0 23px;">テスト名</span></td>
                    <td>{{ Form::select(
                            'year', 
                            ['---', 'テストA', 'テストB'],
                            null, 
                            ['class' => 'cell',
                            'style' => 'width:177px']
                        )}}
                    </td>
                </tr>
                <tr> 
                    <td><span for="year" style="margin: 0 23px 0 23px;">大学コード</span></td>
                    <td>{{ Form::select(
                            'year', 
                            ['---', '大学コードA', '大学コードB'],
                            null, 
                            ['class' => 'cell',
                            'style' => 'width:177px']
                        )}}
                    </td>
                </tr>
            </table>
        </div>
        <div class="form-group">
            <div class="form-row">
                <span for="remarks" style="margin: 0 80px 0 35px;">備考</span>
                {{ Form::textarea(
                    'remarks', 
                    "テストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\nテストテキスト\n", 
                    ['id' => 'remarks',
                     'class' => 'text-area-class',
                     'style' => 'margin-bottom: -120px;width:380px;']
                )}}
            </div>
        </div>
        <div class="form-group">
            <div class="form-row">
                <div class="buttons" style="float:left;margin-top:133px;">
                    <input id="back" class="button green" type="button" name="back" value="戻る" onclick="window.location.href='{{route('test.list', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}'">
                </div>
                <div class="buttons" style="float:right;margin-top:133px;">
                    <input id="delete" class="button grey" type="button" name="delete" value="削除">
                    <input class="button bulu" type="submit" name="submit" value="登録">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')