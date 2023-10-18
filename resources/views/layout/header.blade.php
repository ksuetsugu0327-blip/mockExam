@section('header')
<div id="main">
    <div id="header">
        <div id="sislog">
            <div id="sistem"><h1>{{session(config('constants.SESSION_KEY_SCHTESTNAME'))}}</h1></div>
                @if(auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
                    <div id="logout"><a href="" id ="logoutCheck" onclick="event.preventDefault();
                                                         "> 代理ログインをログアウトする</a></div>
                    @if(session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL')))
                        <form id="logout-form" action="{{ route('surrogateLogin.schoolLogout',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endif
                    @if(session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS')))
                        <form id="logout-form" action="{{ route('surrogateLogin.classLogout',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endif
                    @if(session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_PERSON')))
                        <form id="logout-form" action="{{ route('surrogateLogin.personLogout',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="POST" style="display: none;" >
                            @csrf
                        </form>
                    @endif                    
                @endif

                @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
                    <div id="logout"><a href="" onclick="event.preventDefault();" id ="logoutCheck"> ログオフ </a></div>
                    <form id="logout-form" action="{{ route('schoolLogin.logout',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="POST" style="display: none;">
                        @csrf
                    </form>                
                @endif
                @auth(config('constants.GUARD_SCHOOL'))
                    <div id="logout"><a href="" onclick="event.preventDefault();"
                                                      id ="logoutCheck"> ログオフ</a></div>
                    <form id="logout-form" action="{{ route('schoolLogin.logout',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="POST" style="display: none;">
                        @csrf
                    </form>                
                @endauth
                @auth(config('constants.GUARD_CLASS'))
                    <div id="logout"><a href="" onclick="event.preventDefault();"
                                                        id ="logoutCheck"> ログオフ</a></div>
                    <form id="logout-form" action="{{ route('classLogin.logout',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="POST" style="display: none;">
                        @csrf
                    </form>                
                @endauth
                @auth(config('constants.GUARD_PERSON'))
                    <div id="logout"><a href="" onclick="event.preventDefault();"
                                                        id ="logoutCheck"> ログオフ</a></div>
                    <form id="logout-form" action="{{ route('personLogin.logout',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" method="POST" style="display: none;" onsubmit="return checkLogin();">
                        @csrf
                    </form>                
                @endauth
        </div>
        <div id="menubar">
            <p>
                {{session(config('constants.SESSION_KEY_SCHCODE'))}}
                @if(session(config('constants.SESSION_KEY_SCH_NAME_HIDE_FLG')) === config('constants.SCH_NAME_NO_HIDE_FLG'))
                    &nbsp;{{session(config('constants.SESSION_KEY_SCHNAME'))}}
                @endif
            </p>
            @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
            {{-- 代理ログインを実施していない管理者の場合 --}}
            <p>管理者</p>
                @if( !strpos(url()->current(),'/menu'))
                {{-- メニュー以外の場合は、メニューに戻るリンクを表示 --}}
                <a href="{{ route('schoolTopMenu.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">メニューに戻る</a>
                @endif
            @endif
            
            @if(auth(config('constants.GUARD_SCHOOL'))->check() ||
                (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))))
            {{-- 塾アカウント または 塾アカウントで代理ログインをしている管理者の場合--}}
             <p>管理者</p>
                @if( !strpos(url()->current(),'/menu'))
                {{-- メニュー以外の場合は、メニューに戻るリンクを表示 --}}
                <a href="{{ route('schoolTopMenu.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">メニューに戻る</a>
                @endif
            @endif
            
            @if(auth(config('constants.GUARD_CLASS'))->check() ||
                (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_CLASS'))))
            {{-- 教室アカウント または 教室アカウントで代理ログインをしている管理者の場合--}}
                <p>{{session(config('constants.SESSION_KEY_CLSCODE'))}}&nbsp;{{session(config('constants.SESSION_KEY_CLSNAME'))}}</p>
                <p>管理者</p>
                @if( !strpos(url()->current(),'/menu'))
                <a href="{{ route('classTopMenu.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">メニューに戻る</a>
                @endif
            @endif

            @if(auth(config('constants.GUARD_PERSON'))->check() ||
                (auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_PERSON'))))
            {{-- 個人アカウント または 個人アカウントで代理ログインをしている管理者の場合--}}
                <p>{{session(config('constants.SESSION_KEY_CLSCODE'))}}&nbsp;{{session(config('constants.SESSION_KEY_CLSNAME'))}}</p>
                <p>{{session(config('constants.SESSION_KEY_PSNNAME')).'様'}}</p>
                @if( !strpos(url()->current(),'/menu'))
                <a href="{{ route('personTopMenu.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}">メニューに戻る</a>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
//判定用フラグ
var isSubmit = false;
 
function checkLogin(){
  if(isSubmit){
    //フラグがtrueならアラートを表示してsubmitしない
    return false;
  }else{
    //フラグがtrueでなければ、フラグをtrueにした上でsubmitする
    isSubmit = true;
    return true;
  }
}
</script>
@endsection
