
@extends('layout.common')

@section('title', 'テスト・修正')
@section('keywords', '')
@section('description', 'テスト・修正')
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
        <h1>テスト・修正</h1>
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
        <div id="test_correction" class="correction">
            <form method="post" action="{{route('test.update',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'id' => $testInfo->id])}}">
                <!-- CSRF保護 -->
                @csrf
                <input type="hidden" name="updated_at" value="{{ $testInfo->updated_at }}">
                <table class="test-detail">
                    <tr>
                        <td><span>塾</span></td>
                        <td>
                            <span style="width:80%;">{{ $testInfo->oem_sch_name }}</span>
                        </td>
                        <td><span >年度</span></td>
                        <td>
                            <span style="width:100%">{{ $testInfo->fiscal_year }}</span>
                        <td>
                    </tr>
                    <tr>            
                        <td><span>学年</span></td>
                        <td colspan="3">
                            <span style="width:30%">{{ $grades[$testInfo->grade] }}</span>
                        </td>
                    </tr>
                    <tr> 
                        <td><span>テスト名</span></td>
                        <td colspan="3">
                            <span style="width:80%">{{ $testInfo->test_name }}</span>
                        </td>
                    </tr>
                    <tr> 
                        <td><span for="university_code_head_id">大学コード</span></td>
                        <td colspan="3">
                            {{ Form::select(
                                'university_code_head_id', 
                                ['' => '---'],
                                old('university_code_head_id', $testInfo->university_code_head_id), 
                                [
                                    'class' => 'cell',
                                    'style' => 'width:80%',
                                    'data-id' => old('university_code_head_id', $testInfo->university_code_head_id)
                                ]
                            )}}
                            @if($errors->has('university_code_head_id'))<br> <span class="error">{{ $errors->first('university_code_head_id') }}</span> @endif
                        </td>
                    </tr>
                    <tr> 
                        <td><span for="codebook_pdf">コード表PDF</span></td>
                        <td colspan="3">
                            {{ Form::select(
                                'codebook_pdf', 
                                ['' => '---'] + $codeBookList,
                                old('codebook_pdf', $testInfo->codebook_cd), 
                                [
                                    'class' => 'cell',
                                    'style' => 'width:80%;'
                                ]
                            )}}
                            @if($errors->has('codebook_pdf'))<br> <span class="error">{{ $errors->first('codebook_pdf') }}</span> @endif
                        </td>
                    </tr>
                    <tr> 
                        <td><span for="remarks">備考</span></td>
                        <td colspan="3">
                            {{ Form::textarea(
                                'remarks', 
                                old('remarks', $testInfo->remarks), 
                                [
                                    'class' => 'cell',
                                    'style' => 'width:100%;padding:2px;'
                                ]
                            )}}
                            @if($errors->has('remarks'))<br> <span class="error">{{ $errors->first('remarks') }}</span> @endif
                        </td>
                    </tr>
                </table>
                <div class="buttons">
                    <p class="cell"><input class="button bulu" type="submit" name="submit" value="修正"></p>
                    <p class="cell"><input class="button bulu delete_dialog" type="button" name="delete" value="削除" onclick='deleteTest();'></p>
                    <a href="{{ route('test.list',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="cell"><p class="button green">一覧に戻る</p></a>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="delete_form" action="{{ route('test.delete',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'id'=>$testInfo->id]) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    $(document).ready(function(){
        const fiscalYear = `{{ $testInfo->fiscal_year }}`;
        loadUnivCodeHeadId(fiscalYear);
    });

    // 年度プルダウンの変更イベント
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

    //削除イベント 
    function deleteTest(id){
        if(confirm('{{trans('message.CONFIRM_DELETE_DATA')}}')){
            event.preventDefault();
            document.getElementById('delete_form').submit();
        };
        
        return false;
    }

</script>

@endsection

@include('layout.footer')