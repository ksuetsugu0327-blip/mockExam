
@extends('layout.common')

@section('title', 'テスト・登録')
@section('keywords', '')
@section('description', 'テスト・登録')
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
@include('layout.header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        <h1>テスト・登録</h1>
        <form method="post" action="{{route('test.store',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}">
            <!-- CSRF保護 -->
            @csrf
            <div class="form-group">
                <table class="test-detail">
                    <tr>
                        <td><span for="oem_sch_code" style="margin: 0 23px 0 23px;">塾</span></td>
                        <td>
                            {{ Form::select(
                                'oem_sch_code', 
                                ['' => '---'] + $oemSchoolList,
                                old('oem_sch_code'),
                                ['class' => 'cell',
                                'style' => 'width:70px;margin-right:87px;']
                            )}}
                            @if($errors->has('oem_sch_code'))<br> <span class="error">{{ $errors->first('oem_sch_code') }}</span> @endif
                        </td>
                        <td><span for="year" style="margin-right:23px;">年度</span></td>
                        <td>
                            {{ Form::select(
                                'fiscal_year', 
                                ['' => '---'] + $years,
                                old('fiscal_year'), 
                                ['class' => 'cell',
                                'style' => 'width:100px']
                            )}}
                            @if($errors->has('fiscal_year'))<br> <span class="error">{{ $errors->first('fiscal_year') }}</span> @endif
                        <td>
                    </tr>
                    <tr>            
                        <td><span for="grade" style="margin: 0 23px 0 23px;">学年</span></td>
                        <td>{{ Form::select(
                                'grade', 
                                $grades,
                                old('grade'), 
                                ['class' => 'cell',
                                'style' => 'width:100px']
                            )}}
                            @if($errors->has('grade'))<br> <span class="error">{{ $errors->first('grade') }}</span> @endif
                        </td>
                    </tr>
                    <tr> 
                        <td><span for="test_name_cd" style="margin: 0 23px 0 23px;">テスト名</span></td>
                        <td>{{ Form::select(
                                'test_name_cd', 
                                ['' => '---'] + $testNameList,
                                old('test_name_cd'), 
                                ['class' => 'cell',
                                'style' => 'width:177px']
                            )}}
                            @if($errors->has('test_name_cd'))<br> <span class="error">{{ $errors->first('test_name_cd') }}</span> @endif
                        </td>
                    </tr>
                    <tr> 
                        <td><span for="university_code_head_id" style="margin: 0 23px 0 23px;">大学コード</span></td>
                        <td>{{ Form::select(
                                'university_code_head_id', 
                                ['' => '---'] + $universityCodeNameList,
                                old('university_code_head_id'), 
                                ['class' => 'cell',
                                'style' => 'width:177px']
                            )}}
                            @if($errors->has('university_code_head_id'))<br> <span class="error">{{ $errors->first('university_code_head_id') }}</span> @endif
                        </td>
                    </tr>
                    <tr> 
                        <td><span for="highschool_cls" style="margin: 0 23px 0 23px;">備考</span></td>
                        <td colspan="3">{{ Form::textarea(
                                'highschool_cls', 
                                old('highschool_cls'), 
                                ['class' => 'cell',
                                'style' => 'width:380px;']
                            )}}
                            @if($errors->has('highschool_cls'))<br> <span class="error">{{ $errors->first('highschool_cls') }}</span> @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div class="buttons" style="display: flex;justify-content: space-between;">
                                <input class="button bulu" type="submit" name="submit" value="登録">
                                <input id="reset" class="button green" type="reset" name="reset" value="クリア">
                                <input id="back" class="button green" type="button" name="back" value="一覧に戻る" onclick="window.location.href='{{route('test.list', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}'">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
</div>
@endsection

@include('layout.footer')