@extends('layout.common')

@section('title', '塾マスタ・一覧')
@section('keywords', '')
@section('description', '')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="jmaster">
    <div id="content">
        <h1>塾マスタ・一覧</h1>
        @if (isset($message))
        <div class="error_message">
        <strong>{{ $message }}</strong>
        </div>
        @endif
        @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
        <div class="stouroku">新規登録は<a href="{{ route('schoolRegist.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}"> こちら</a></div>        
        @else
        <div>　　</div>
        @endif
        <div id="ju_search" class="search">
            <form id='search_form' action="{{route('schools.search',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <table>
                    <tr>
                        <td>
                            <p>塾コード</p>
                        </td>
                        <td>
                            <p>
                                <input type="text" name="sch_code" id="sch_code" value="{{ old('sch_code', $sch_code) }}" class="cell clear" autofocus>
                                @if($errors->has('sch_code'))<br> <span class="error">{{ $errors->first('sch_code') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>塾名</p>
                        </td>
                        <td>
                            <p>
                                <input type="text" name="sch_name" id="sch_name" value="{{ old('sch_name', $sch_name) }}" class="cell clear">
                            </p>
                        </td>
                    </tr>
                </table>
                <div class="buttons">
                    <p><input class="button  bulu" type="submit" name="submit" value="検索"></p>
                    <p><input  id="inputClear" class="button green" type="button"   name="reset" value="クリア"></p>
                </div>
            </form>
        </div>
        @if($resultCount>0)
            <div id="ju_List" class="List2">
                <p>({{ $resultCount }}件表示)</p> 
                <table class="mastertable">
                    <tr>
                        <th style="width: 30px;">塾コード</th>
                        <th style="width: 40px;">塾アカウント名</th>
                        <th style="width: 80px;">塾名</th>
                        <th style="width: 15px;"></th>
                        <th style="width: 15px;"></th>
                        <th style="width: 20px;"></th>
                    </tr>
                    @foreach ($schools as $school)
                    <tr>
                        <td>{{ $school->sch_code }}</td>
                        <td>{{ $school->portal_account }}</td>
                        <td>{{ $school->name }}</td>
                        <td>
                            <a href="{{ route('schoolEdit.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),$school->sch_code]) }}">修正</a>
                        </td>
                        <td>
                            <div id="schools_delete"><a href="" onclick="return deleteSchool({{$school->sch_code}})"><div class="passhumei">削除</div></a></div>
                        </td>
                        @if($school->admin_kbn == 0)
                        <td>
                            <div id="schools_surrogate_login"><a href="" onclick="return surrogateLogin({{$school->sch_code}})"><div class="passhumei">ログイン</div></a></div>
                        </td>
                        @else
                        <td></td>
                        @endif                        
                    </tr>
                    @endforeach
                </table>
            </div>
        @endif
    </div>
</div>

<form id="delete_form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>  

<form id="surrogateLogin_form" action="" method="POST" style="display: none;">
    @csrf
</form>  

<script>
    function deleteSchool(delSchCode){
        if(confirm('{{trans('message.CONFIRM_DELETE_DATA')}}')){
            event.preventDefault();
            document.getElementById('delete_form').action='{{ route('schools.delete',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'delSchCode'=>'@delSchCode']) }}'.replace('@delSchCode',delSchCode);
            document.getElementById('delete_form').submit();
        };
        
        return false;
    }

    function surrogateLogin(loginSchCode){
        event.preventDefault();
        document.getElementById('surrogateLogin_form').action='{{ route('surrogateLogin.schoolLogin',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'loginSchCode'=>'@loginSchCode']) }}'.replace('@loginSchCode',loginSchCode);
        document.getElementById('surrogateLogin_form').submit();
    }

</script>

@endsection

@include('layout.footer')