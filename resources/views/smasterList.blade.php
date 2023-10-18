@extends('layout.common')

@section('title', '生徒マスタ・一覧')
@section('keywords', '')
@section('description', '生徒マスタ一覧')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@inject('passwordUtils','App\Utils\PasswordUtil')
@inject('routeUtil','App\Utils\RouteUtil')

@section('content')
<div id="site_content" class="smaster">
    <div id="content">
        <h1>生徒マスタ・一覧</h1>
    @if (isset($message))
        <div class="error_message">
           <strong>{{ $message }}</strong>
        </div>
    @endif
    @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
    <div class="stouroku">新規登録は<a href="{{ route('personRegist.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}"> こちら</a></div>
    @else
    <div>　　</div>
    @endif
        <div id="sm_search" class="search">
            <form id="search_form" action="{{ route($routeUtil->getPrefix().'.persons.search',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="post">
                @csrf
            @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
                {{-- 代理ログインしていない管理者の場合  --}}
                <table>
                    <tr><td><p>塾コード</p></td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name='sch_code' id='sch_code' value="{{ $sch_code }}" autofocus maxlength='7'>
                            </p>
                        </td>
                        <td><p>塾名</p></td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name='sch_name' id='sch_name' value="{{ $sch_name}}" maxlength='40'>
                            </p>
                        </td>
                    </tr>
                    <tr><td><p>教室コード</p></td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name='cls_code' id='cls_code' value="{{ $cls_code }}" maxlength='4'>
                            </p>
                        </td>
                        <td><p>教室名</p></td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name='cls_name' id='cls_name' value="{{ $cls_name }}" maxlength='40'>
                            </p>
                        </td>
                    </tr>
                    <tr><td><p>塾生ID</p></td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="psn_code" id='psn_code' value="{{$psn_code }}" maxlength="11" >
                            </p>
                        </td>
                        <td>
                            <p>氏名</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name='psn_name' id='psn_name' value="{{ $psn_name }}">
                            </p>
                        </td>
                    </tr>
                </table>
                
            @elseif((auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) || 
                     auth(config('constants.GUARD_SCHOOL'))->check() )
                {{-- 塾アカウント または 塾アカウントで代理ログインしている管理者 --}}
                <input class="cell" type="hidden" name='sch_code' id='sch_code' value="{{session(config('constants.SESSION_KEY_SCHCODE'))}}">
                <table>
                    <tr><td><p>教室コード</p></td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name='cls_code' id='cls_code' value="{{ $cls_code }}" autofocus maxlength='4'>
                            </p>
                        </td>
                        <td><p>教室名</p></td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name='cls_name' id='cls_name' value="{{ $cls_name }}" maxlength='40'>
                            </p>
                        </td>
                    </tr>
                    <tr><td><p>塾生ID</p></td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="psn_code" id='psn_code' value="{{$psn_code }}" maxlength="11" >
                            </p>
                        </td>
                        <td>
                            <p>氏名</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name='psn_name' id='psn_name' value="{{ $psn_name }}">
                            </p>
                        </td>
                    </tr>
                </table>

            @else
                {{-- 教室アカウント または 教室アカウントで代理ログインしている管理者 --}}
                <input class="cell" type="hidden" name='sch_code' id='sch_code' value="{{session(config('constants.SESSION_KEY_SCHCODE'))}}">
                <input class="cell" type="hidden" name='cls_code' id='cls_code' value="{{session(config('constants.SESSION_KEY_CLSCODE'))}}">                
                <table>
                    <tr><td><p>塾生ID</p></td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="psn_code" id='psn_code' value="{{$psn_code }}" maxlength="11" autofocus >
                            </p>
                        </td>
                        <td>
                            <p>氏名</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name='psn_name' id='psn_name' value="{{ $psn_name }}">
                            </p>
                        </td>
                    </tr>
                </table>
            @endif
            
                <div class="buttons">
                    <p><input class="button  bulu" type="submit" name="search" value="検索"></p>
                    <p><input id="inputClear" class="button green" type="button" name="reset" value="クリア"></p>
                @if($resultCount > 0)
                    <p><input class="button  bulu" type="button" name="csv" onclick="return downloadCSV();" value="CSV出力" ></p>
                @else
                {{-- TODO: ボタン非活性時のスタイルシートの切り替えが必要 --}}
                    <p><input class="button  bulu" type="button" name="csvOutput" value="CSV出力" disabled></p>
                @endif
                </div>
            </form>
        </div>
    @if($resultCount > 0)
        <div id="sm_List" class="List2">
            <p>({{ $resultCount }}件表示)</p>
        @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
        {{-- 代理ログインしていない管理者の場合  --}}
            <table class="mastertable">
                <tr><th style="width: 110px;">塾生ID</th>
                    <th style="width:  90px;">氏名</th>
                    <th style="width: 105px;">塾</th>
                    <th style="width: 100px;">教室</th>
                    <th style="width: 100px;">仮パスワード</th>
                    <th style="width: 40px;"></th>
                    <th style="width: 40px;"></th>
                    <th style="width: 60px;"></th>
                </tr>
            @foreach ($persons as $person)
                <tr>
                    <td>{{ $person->psn_code }}</td>
                    <td class="td1">{{ $person->psn_name }}</td>
                    <td class="td1">{{ $person->sch_name }}</td>
                    <td class="td1">{{ $person->cls_name }}</td>
                    <td>{{ $passwordUtils->decryptString($person->temppassword) }}</td>     
                    <td>
                        <a href="{{ route($routeUtil->getPrefix().'.personEdit.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),
                                                                'editSchCode'=>$person->sch_code,'editPsnCode'=>$person->psn_code]) }}">修正</a>
                    </td>
                    <td>
                        <a href="" onclick="return deletePerson({{$person->sch_code.','."'$person->psn_code'"}});">削除</a>
                    </td>
                    <td>
                        <a href="" onclick="return surrogateLogin({{$person->sch_code.','."'$person->psn_code'"}});">ログイン</a>
                    </td>
                </tr>
            @endforeach
            </table>
        @elseif((auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) || 
                 auth(config('constants.GUARD_SCHOOL'))->check() )
        {{-- 塾アカウント または 塾アカウントで代理ログインしている管理者 --}}
            <table class="mastertable">
                <tr><th style="width: 105px;">塾生ID</th>
                    <th style="width:  90px;">氏名</th>
                    <th style="width:  60px;">教室CD</th>
                    <th style="width: 100px;">教室</th>
                    <th style="width: 100px;">仮パスワード</th>
                    <th style="width:  40px;"></th>
                </tr>
            @foreach ($persons as $person)
                <tr>
                    <td>{{ $person->psn_code }}</td>
                    <td class="td1">{{ $person->psn_name }}</td>
                    <td>{{ $person->cls_code }}</td>  
                    <td class="td1">{{ $person->cls_name }}</td>  
                    <td>{{ $passwordUtils->decryptString($person->temppassword) }}</td>     
                    <td><a href="{{ route($routeUtil->getPrefix().'.personEdit.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),
                                                                'editSchCode'=>$person->sch_code,'editPsnCode'=>$person->psn_code]) }}">修正</a>
                    </td>
                </tr>
            @endforeach
            </table>
        @else
            {{-- 教室アカウント または 教室アカウントで代理ログインしている管理者 --}}
            <table class="mastertable">
                <tr><th style="width: 5px">塾生ID</th>
                    <th style="width: 10px">氏名</th>
                    <th style="width: 10px;">仮パスワード</th>
                    <th style="width: 3px"></th>
                </tr>
            @foreach ($persons as $person)
                <tr>
                    <td>{{ $person->psn_code }}</td>
                    <td class="td1">{{ $person->psn_name }}</td>
                    <td>{{ $passwordUtils->decryptString($person->temppassword) }}</td>     
                    <td><a href="{{ route($routeUtil->getPrefix().'.personEdit.index', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),
                'editSchCode'=>$person->sch_code,'editPsnCode'=>$person->psn_code]) }}">修正</a>
                    </td>
                </tr>
            @endforeach
            </table>
        @endif

        </div>
    @endif
    </div>
</div>

<form id="csvdownload_form" action="{{ route($routeUtil->getPrefix().'.persons.downloadCsv',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="POST" style="display: none;">
    @csrf
</form>  

<form id="delete_form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>  

<form id="surrogateLogin_form" action="" method="POST" style="display: none;">
    @csrf
</form>  


<script>
    function downloadCSV(){
        event.preventDefault();
        document.getElementById('csvdownload_form').submit();
    }

    function surrogateLogin(loginSchCode,loginPsnCode){
        event.preventDefault();
        document.getElementById('surrogateLogin_form').action='{{ route('surrogateLogin.personLogin',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'loginSchCode'=>'@loginSchCode','loginPsnCode'=>'@loginPsnCode']) }}'.replace('@loginSchCode',loginSchCode).replace('@loginPsnCode',loginPsnCode);
        document.getElementById('surrogateLogin_form').submit();
    }

    function deletePerson(delSchCode,delPsnCode){
        if (confirm('{{trans('message.CONFIRM_DELETE_DATA')}}')){
            event.preventDefault();
            document.getElementById('delete_form').action = '{{ route('persons.delete',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'delSchCode'=>'@delSchCode','delPsnCode'=>'@delPsnCode']) }}'.replace('@delSchCode',delSchCode).replace('@delPsnCode',delPsnCode);
            document.getElementById('delete_form').submit();
        };
        
        return false;
    }

</script>

@endsection

@include('layout.footer')
