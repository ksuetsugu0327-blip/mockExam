@extends('layout.common')

@section('title', 'ログイン')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="kplogin">
    <div id="content">
        <h1>ログイン</h1>
        <div id="un_login" class="login">
        　  @if ($errors->has('message'))
      		 <div class="error_message">
                	<strong>{{ $errors->first('message') }}</strong>
            　　 </div>
            @endif
            <form action="{{route('personUnknownLogin.login',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <input type="hidden" id="psn_code" name="psn_code" value="{{$psn_code}}" />
                <input type="hidden" id="mode" name="mode" value="{{$mode}}" />
                <table>
                    <tr><td>塾生ID</td><td>{{$psn_code}}</td></tr>
                    @if($mode==0)
                        <tr><td>生年月日(月日)</td>
                            <td>
                                <input id="birthlow4" type="text" value="{{old('birthlow4')}}" name="birthlow4" placeholder="0101(例.1月1日の場合)" required autofocus maxlength="4" />
                                @if ($errors->has('birthlow4'))
                                <br/>
                                <span class="error">
                                        <strong>{{ $errors->first('birthlow4') }}</strong>
                                </span>
                                @endif
                            </td>
                        </tr>
                        <tr><td>郵便番号(下4桁)</td>
                            <td>
                                <input id="postallow4" type="text" value="{{old('postallow4')}}" name="postallow4" placeholder="0001" required maxlength="4" />
                            @if ($errors->has('postallow4')) 
                            <br/>
                            <span class="error">
                                 <strong>{{ $errors->first('postallow4') }}</strong>
                                </span>                    
                            @endif
                           </td>
                        </tr>
                    @elseif ($mode==1)
                        <tr><td>生年月日(月日)</td>
                            <td>
                                <input id="birthlow4" type="text" value="{{old('birthlow4')}}"  placeholder="0101(例 1月1日の場合)" name="birthlow4" required autofocus maxlength="4" />
                                @if ($errors->has('birthlow4'))
                                <br/>
                                <span class="error">
                                        <strong>{{ $errors->first('birthlow4') }}</strong>
                                </span>                                    
                                @endif
                            </td>
                        </tr>
                        <tr><td>電話番号(下4桁)</td>
                            <td>
                                <input id="tellow4" type="text" value="{{old('tellow4')}}"  name="tellow4" placeholder="0123" required maxlength="4" />
                                @if ($errors->has('tellow4'))
                                <br/>
                                <span class="error">
                                        <strong>{{ $errors->first('tellow4') }}</strong>
                                </span>                                    
                                @endif
                            </td>
                        </tr>                    
                    @else
                        <tr><td>郵便番号(下4桁)</td>
                            <td>
                                <input id="postallow4" type="text" value="{{old('postallow4')}}"  name="postallow4" placeholder="0001" autofocus required maxlength="4" />
                                @if ($errors->has('postallow4'))
                                <br/>
                                    <span class="error">
                                        <strong>{{ $errors->first('postallow4') }}</strong>
                                    </span>
                                @endif
                            </td>
                        </tr>                    
                        <tr><td>電話番号(下4桁)</td>
                            <td>
                                <input id="tellow4" type="text" value="{{old('tellow4')}}"  name="tellow4" placeholder="0123"  required maxlength="4" />
                                @if ($errors->has('tellow4'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('tellow4') }}</strong>
                                </span>                                    
                                @endif
                            </td>
                        </tr>                    
                    @endif
                </table>
                <div><input class="button  bulu" type="submit" name="login" value="ログイン" /></div>
            </form>
        </div>
    </div>
</div>

@endsection

@include('layout.footer')