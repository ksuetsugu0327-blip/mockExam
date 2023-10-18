@extends('layout.common')

@section('title', '教室マスタ・一覧')
@section('keywords', '')
@section('description', '')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')
@inject('routeUtil','App\Utils\RouteUtil')
@inject('passwordUtils','App\Utils\PasswordUtil')

@section('content')
<div id="site_content" class="kmaster">
    <div id="content">
        <h1>教室マスタ・一覧</h1>
        @if (isset($message))
        <div class="error_message">
            <strong>{{ $message }}</strong>
        </div>
        @endif
        @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
        {{-- 代理ログインしていない管理者の場合 --}}
        <div class="stouroku">新規登録は<a href="{{ route('classRegist.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}"> こちら</a></div> 
        @else
        <div>　　</div>
        @endif
        <div id="cl_search" class="search">
            <form id='search_form' action="{{route('classes.search',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                <!-- CSRF保護 -->
                @csrf
                @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
                {{-- 代理ログインしていない管理者の場合 --}}
                <table>
                    <tr>
                        <td>　
                            塾コード
                        </td>
                        <td>
                            <p>
                                <input type="text" name="sch_code" id="sch_code" value="{{ old('sch_code', $sch_code) }}" class="cell clear" autofocus>
                                @if($errors->has('sch_code'))<br> <span class="error">{{ $errors->first('sch_code') }}</span> @endif
                            </p>
                        </td>
                        <td>
                            <p>塾名</p>
                        </td>
                        <td>
                            <p>
                                <input type="text" name="sch_name"  value="{{ old('sch_name', $sch_name) }}" class="cell clear"> 
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>    
                            <p>教室コード </p> 
                        </td>
                        <td>
                            <p>
                                <input type="text" name="cls_code" value="{{ old('cls_code', $cls_code)}}"  class="cell clear">
                                @if($errors->has('cls_code'))<br> <span class="error">{{ $errors->first('cls_code') }}</span> @endif
                            </p>
                        </td>
                        <td>
                            <p>教室名</p>
                        </td>
                        <td>
                            <p>
                                <input type="text" name="cls_name"  value="{{old('cls_name', $cls_name)}}" class="cell clear">
                            </p>
                        </td>
                    </tr>
                </table>    
                @endif
                @if((auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) || 
                auth(config('constants.GUARD_SCHOOL'))->check() )  
                {{---塾アカウントと代理管理者アカウント --}}    
                <input class="cell" type="hidden" name='sch_code' id='sch_code' value="{{session(config('constants.SESSION_KEY_SCHCODE'))}}">
                <table>                        
                    <tr>
                        <td>    
                            <p>教室コード </p> 
                        </td>
                        <td>
                            <p>
                                <input type="text" name="cls_code" value="{{$cls_code}}"  class="cell clear">
                                @if($errors->has('cls_code'))<br> <span class="error">{{ $errors->first('cls_code') }}</span> @endif
                            </p>
                        </td>
                        <td>
                            <p>教室名</p>
                        </td>
                        <td>
                            <p>
                                <input type="text" name="cls_name"  value="{{$cls_name}}" class="cell clear">
                            </p>
                        </td>
                    </tr>
                </table> 
                @endif
                <div class="buttons">
                    <p><input class="button  bulu" type="submit" name="submit" value="検索"></a> </p>
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
        <div id="cl_List" class="List2">
            <p>({{ $resultCount}}件表示)</p> 
         <!--   <table id='table1' style="table-layout:fixed;width: 700px;">-->
            @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) 
            {{-- 代理ログインしていない管理者アカウント--}}
            <table class="mastertable">
                <tr>
                    <th style="width: 60px;">塾コード</th>
                    <th style="width: 120px;">塾名</th>
                    <th style="width: 50px;">教室CD</th>
                    <th style="width: 60px;">教室名</th>
                    <th style="width: 70px;">仮パスワード</th>
                    <th style="width: 25px;"></th>
                    <th style="width: 25px;"></th>
                    <th style="width: 50px;"></th>
                </tr>
                @foreach ($classes as $class)
                <tr>                 
                    <td>{{ $class->sch_code}} </td>
                    <td class="td1">{{ $class->sch_name}} </td>
                    <td>{{ $class->cls_code}} </td>
                    <td class="td1">{{ $class->name}} </td>
                    <td>{{empty($class->temppassword)?'':$passwordUtils->decryptString($class->temppassword)}}</td>

                    <td>
                        <a href="{{ route('classEdit.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),$class->sch_code,$class->cls_code])}}">修正</a>
                    </td>
                    <td>
                        <a href="" onclick="return deleteClass({{$class->sch_code.','.$class->cls_code}});">削除</a>
                    </td>
                    <td>
                        <a href="" onclick="return surrogateLogin({{$class->sch_code.','.$class->cls_code}});"font-size:1px>ログイン</a>
                    </td>
                </tr>   
                @endforeach
            </table> 
            @endif
            @if((auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) || 
            auth(config('constants.GUARD_SCHOOL'))->check() )  
            {{---塾アカウントと代理管理者アカウント --}}      
            <table class="mastertable">
                <th style="width: 70px;">教室CD</th>
                <th style="width: 250px;">教室名</th>
                <th style="width: 150px;">仮パスワード</th>
                <th style="width:  35px;"></th>
                </tr>  
                <tr id="boder">
                    @foreach ($classes as $class)
                <tr> 
                    <td>{{ $class->cls_code}} </td>
                    <td class="td1">{{ $class->name}} </td>
                    <td>{{empty($class->temppassword)?'':$passwordUtils->decryptString($class->temppassword)}}</td>
                    <td>
                        <a href="{{ route('classEdit.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),$class->sch_code,$class->cls_code])}}">修正</a>
                    </td>
                </tr>   
                @endforeach
            </table> 
            @endif
            @endif
        </div>
    </div>
</div>

<form id="csvdownload_form" action="{{ route($routeUtil->getPrefix().'.classes.downloadCsv',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="POST" style="display: none;">
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

    function surrogateLogin(loginSchCode, loginClsCode){
    event.preventDefault();
    document.getElementById('surrogateLogin_form').action = '{{ route('surrogateLogin.classLogin',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'loginSchCode'=>'@loginSchCode','loginClsCode'=>'@loginClsCode']) }}'.replace('@loginSchCode', loginSchCode).replace('@loginClsCode', loginClsCode);
    document.getElementById('surrogateLogin_form').submit();
    }

    function deleteClass(delSchCode, delClsCode){
    if (confirm('{{trans('message.CONFIRM_DELETE_DATA')}}')){
    event.preventDefault();
    document.getElementById('delete_form').action = '{{ route('classes.delete',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'delSchCode'=>'@delSchCode','delClsCode'=>'@delClsCode']) }}'.replace('@delSchCode', delSchCode).replace('@delClsCode', delClsCode);
    document.getElementById('delete_form').submit();
    };
    return false;
    }
</script>

@endsection

@include('layout.footer')