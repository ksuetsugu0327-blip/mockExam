
@extends('layout.common')

@section('title', '大学コードデータ削除')
@section('keywords', '')
@section('description', '大学コードデータ削除')
@section('pageCss')
<style>
    .univ-code-search {
        margin:unset;
    }
    .univ-code-search td {
        padding: 0 20px 10px 0;
    }
    #data_name {
        width: 250px;
    }
</style>
@endsection

@include('layout.head')
@include('layout.header')

@section('content')
<div id="portal-content" class="idlogin">
    <div id="content">
        <h1>大学コードデータ削除</h1><br>
        @if (isset($message))
        <div class="error_message">
            <strong>{{ $message }}</strong>
        </div>
        @endif
        <br>
        <div id="s_search" class="search">
            <form method="post" action="{{ route('univ_code.search',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">
                @csrf
                <table class="univ-code-search">
                    <tr>
                        <td><span for="year">年度</span></td>
                        <td colspan="3">
                            <select class="form-select cell" id="year" name="year">
                                <option value="">---</option>
                                @foreach($years as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $selectedYear ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><span for="data_name">データ名</span></td>
                        <td colspan="3">
                            <input type="text" name="data_name" id="data_name" value="{{ old('data_name', $dataNameKeyword) }}" class="form-control cell clear">
                            @if($errors->has('data_name'))<br> <span class="error">{{ $errors->first('data_name') }}</span> @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span for="dept_subject_div">学部/学科</span></td>
                        <td colspan="3">
                            <select class="form-select cell" id="dept_subject_div" name="dept_subject_div">
                                <option value="">---</option>
                                @foreach($deptSubjSelect as $item)
                                    <option value="{{$item['VALUE']}}" {{ $item['VALUE'] == $selectedDeptSubjectDiv ? 'selected' : '' }}>
                                        {{$item['NAME']}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
                <div class="buttons">
                    <input class="button bulu" type="submit" name="submit" value="検索">
                    <input id="inputClear" class="button green" type="button" name="reset" value="クリア">
                    <a href="{{ route('univ.register',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="cell button green">戻る</a>
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
                            <th style="width: 88px;">データ名</th>
                            <th style="width: 88px;">学部/学科</th>
                            <th style="width: 88px;"></th>
                        </tr>
                    <!-- Table body -->
                        <!-- Example data rows - Replace these with your own data rows -->
                        @foreach($univCodeList as $item)
                        <tr data-id="{{ $item->id }}">
                            <td></td>
                            <td>{{ $item->fiscal_year }}</td>
                            <td>{{ $item->university_code_name }}</td>
                            <td>{{ collect($deptSubjSelect)->firstWhere('VALUE', $item->dept_subject_div)['NAME'] ?? '' }}</td>
                            <td><a href="" onclick="return deleteTest({{$item->id}})">削除</a></td>
                        </tr>
                        @endforeach
                        <!-- Add more rows here as needed -->
                    </tbody>
                </table>
            </div>
        @endif 
    </div>
</div>

<form id="delete_form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function deleteTest(id){
        if(confirm('{{trans('message.CONFIRM_DELETE_DATA')}}')){
            event.preventDefault();
            document.getElementById('delete_form').action='{{ route('univ_code.delete',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'id'=>'@id']) }}'.replace('@id',id);
            document.getElementById('delete_form').submit();
        };
        
        return false;
    }
</script>

@endsection
@include('layout.footer')