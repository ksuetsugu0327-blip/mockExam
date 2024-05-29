
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
        <div id="test_touroku" class="correction">
            <form method="post" action="{{route('test.store',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}">
                <!-- CSRF保護 -->
                @csrf
                <table class="test-detail">
                    <tr>
                        <td><span for="oem_sch_code">塾</span></td>
                        <td>
                            {{ Form::select(
                                'oem_sch_code', 
                                ['' => '---'] + $oemSchoolList,
                                old('oem_sch_code'),
                                [
                                    'class' => 'cell',
                                    'style' => 'width:80%;'
                                ]
                            )}}
                            @if($errors->has('oem_sch_code'))<br> <span class="error">{{ $errors->first('oem_sch_code') }}</span> @endif
                        </td>
                        <td><span for="year">年度</span></td>
                        <td>
                            {{ Form::select(
                                'fiscal_year', 
                                ['' => '---'] + $years,
                                old('fiscal_year'), 
                                [
                                    'class' => 'cell',
                                    'style' => 'width:100%;'
                                ]
                            )}}
                            @if($errors->has('fiscal_year'))<br> <span class="error">{{ $errors->first('fiscal_year') }}</span> @endif
                        <td>
                    </tr>
                    <tr>            
                        <td><span for="grade">学年</span></td>
                        <td colspan="3">
                            {{ Form::select(
                                'grade', 
                                $grades,
                                old('grade'), 
                                [
                                    'class' => 'cell',
                                    'style' => 'width:30%;'
                                ]
                            )}}
                            @if($errors->has('grade'))<br> <span class="error">{{ $errors->first('grade') }}</span> @endif
                        </td>
                    </tr>
                    <tr> 
                        <td><span for="test_name_cd">テスト名</span></td>
                        <td colspan="3">
                            {{ Form::select(
                                'test_name_cd', 
                                ['' => '---'] + $testNameList,
                                old('test_name_cd'), 
                                [
                                    'class' => 'cell',
                                    'style' => 'width:80%;'
                                ]
                            )}}
                            @if($errors->has('test_name_cd'))<br> <span class="error">{{ $errors->first('test_name_cd') }}</span> @endif
                        </td>
                    </tr>
                    <tr> 
                        <td><span for="university_code_head_id">大学コード</span></td>
                        <td colspan="3">
                            {{ Form::select(
                                'university_code_head_id', 
                                ['' => '---'],
                                old('university_code_head_id'), 
                                [
                                    'class' => 'cell',
                                    'data-id' => old('university_code_head_id'),
                                    'style' => 'width:80%;'
                                ]
                            )}}
                            @if($errors->has('university_code_head_id'))<br> <span class="error">{{ $errors->first('university_code_head_id') }}</span> @endif
                        </td>
                    </tr>
                    <tr> 
                        <td><span for="highschool_cls">備考</span></td>
                        <td colspan="3">{{ Form::textarea(
                                'highschool_cls', 
                                old('highschool_cls'), 
                                ['class' => 'cell',
                                'style' => 'width:100%;padding:2px;']
                            )}}
                            @if($errors->has('highschool_cls'))<br> <span class="error">{{ $errors->first('highschool_cls') }}</span> @endif
                        </td>
                    </tr>
                </table>
                <div class="buttons">
                    <p class="cell"><input class="button bulu" type="submit" name="submit" value="登録"></p>
                    <p class="cell"><input id="reset" class="button green" type="reset" name="reset" value="クリア"></p>
                    <a href="{{ route('test.list',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="cell"><p class="button green">一覧に戻る</p></a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        const fiscalYear = $('[name="fiscal_year"]').val();
        loadUnivCodeHeadId(fiscalYear);
        // 年度プルダウンの変更イベント
        $('[name="fiscal_year"]').change(function() {
            $('[name="university_code_head_id"]').data('id','');
            loadUnivCodeHeadId($(this).val());
        });
    });

    // 大学コードリスト取得処理
    function loadUnivCodeHeadId(fiscalYear) {
        const univCodeHeadId = $('[name="university_code_head_id"]');
        const selectedHeadId = univCodeHeadId.data('id');
        if (!fiscalYear) {
            restDropdown(univCodeHeadId);
            return;
        }

        $.ajax({
            url: '{{ route('api.test_management.university_code_heads_list')}}',
            method: 'GET',
            data: {
                fy: fiscalYear
            },
            success: function(response) {
                console.log(response);
                restDropdown(univCodeHeadId);
                if (response.header.status === 'success') {
                    var data = response.data;
                    $.each(data, function(index, item) {
                        univCodeHeadId.append(`<option value="${item.id}">${item.university_code_name}</option>`);
                    });
                    if (selectedHeadId) {
                        univCodeHeadId.val(selectedHeadId);
                    }
                    univCodeHeadId.prop('disabled', false);
                }
            },
            error: function() {
                restDropdown(univCodeHeadId);
            }
        });
    }

    // 大学コードプルダウンのリセット
    function restDropdown(univCodeHeadId) {
        univCodeHeadId.empty();
        univCodeHeadId.append('<option value="">---</option>');
        univCodeHeadId.prop('disabled', true);
    }
</script>

@endsection

@include('layout.footer')