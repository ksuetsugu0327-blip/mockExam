@extends('layout.common')

@section('title', '教室マスタ・修正')
@section('keywords', '')
@section('description', '教室マスタ修正、削除')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')
@inject('passwordUtils','App\Utils\PasswordUtil')
@inject('routeUtil','App\Utils\RouteUtil')

@section('content')
<div id="site_content" class="kmaster">
    <div id="content" >
        <h1>教室マスタ・修正</h1>
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
        <div id="jm_correction" class="correction">
            <form action="{{ route('classEdit.update', ['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'editSchCode'=>$class->sch_code,'editClsCode' => $class->cls_code])}}" method="post">
                <!-- CSRF保護 -->
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $class->id}}">
                <table>
                    <tr>
                        <td>
                            <p>塾</p>
                        </td>
                        <td>
                            <p>
                                @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) 
                                {{--　代理ログインしていない管理者アカウント--}}
                                {{$sch_name }} 
                                @endif
                                @if((auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) || 
                                auth(config('constants.GUARD_SCHOOL'))->check() )  
                                {{---塾アカウントと代理管理者アカウント --}}    
                                {{$sch_name}}
                              　 <input type="hidden" name="sch_name" value="{{ $sch_name }}">
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>教室コード</p>
                        </td>
                        <td>
                            <p>
                                <input type="hidden" name="cls_code" value="{{ $class->cls_code }}" autofocus>{{  $class->cls_code}}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>教室名</p>
                        </td>
                        <td>
                            <p>
                                @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) 
                                {{--　代理ログインしていない管理者アカウント--}}
                                <input  class="cell clear" type="text" name="cls_name" value="{{$class->name}}" maxlength='20'>
                                <br>
                                @endif
                                @if($errors->has('cls_name'))<span class="error">{{ $errors->first('cls_name') }}</span> @endif
                                @if((auth(config('constants.GUARD_ADMIN'))->check() && session()->has(config('constants.SESSION_KEY_SURROGATE_LOGIN_SCHOOL'))) || 
                                auth(config('constants.GUARD_SCHOOL'))->check() )  
                                {{---塾アカウントと代理管理者アカウント--}}
                                {{$class->name}}
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p>  
                                <label>
                                    <input type="checkbox" name="jpchange"  id="jpchange" class="checkbox"><span class="ctext">教室アカウントのパスワードを変更</span>
                                </label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td style="font-size: 70%;">
                            半角英数6～50文字を指定してください。<br>
                            （英数字混在推奨）
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="padding:0 0 1em 0">教室アカウントパスワード</p>
                        </td>
                        <td>
                            <p style="padding:0 0 1em 0">
                                <input id="pass" class="cell" type="password" name="password" value="">
                                <br>
                                @if($errors->has('password'))<span class="error" id="error_pass">{{ $errors->first('password') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 300px;">
                            <p>教室アカウントパスワード(確認)</p>
                        </td>
                        <td>
                            <p>
                                <input id="pass_confirmation" class="cell" type="password" name="password_confirmation" value="">
                                <br>
                                @if($errors->has('password_confirmation'))<span class="error">{{ $errors->first('password_confirmation') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>仮パスワード　　</p>
                        </td>
                        <td>
                            <p>
                                {{ $passwordUtils->decryptString($class->temppassword) }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label>                                
                                <input type="checkbox" id="temppasswordReissueCheck" name="temppasswordReissueCheck" class="checkbox"><span class="ctext">仮パスワード再発行</span>
                            </label>
                            @if($errors->has('temppasswordReissueCheck'))<span class="error">{{ $errors->first('temppasswordReissueCheck') }}</span> @endif
                        </td>                        
                    </tr>
                </table>
                <div style="margin-top:20px;" class="buttons">
                    <p class="cell"><input class="button bulu" type="submit" name="update" value="修正" ></p>   
                    @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED'))) 
                    {{--　代理ログインしていない管理者アカウント --}}
                    <p class="cell"><input class="button bulu delete_dialog" type="button" name="delete" value="削除" onclick='deleteClass();'></p>
                    @endif
<!--                    <p class="cell"><input name="reset" class="button green" name="reset"  value="クリア"></p>-->
                    <a href="{{ route('classes.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))],['clsCode'=>session(config('constants.SESSION_KEY_CLSCODE'))]) }}" class="cell"><p class="button green">一覧に戻る</p></a>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="delete_form" action="{{ route('classEdit.delete',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'delSchCode'=>$class->sch_code,'delClsCode'=>$class->cls_code]) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>  

<script>
    $('#temppasswordReissueCheck').change(function () {
        if($('#temppasswordReissueCheck').prop('checked')){
            $('#jpchange').prop('checked',false);
            $('input[name="password"]').prop('disabled',true);
            $('input[name="password_confirmation"]').prop('disabled',true);
        }
     });
    $('#jpchange').change(function () {
        if($('#jpchange').prop('checked')){
            $('#temppasswordReissueCheck').prop('checked',false);
            $('input[name="password"]').prop('disabled',false);
            $('input[name="password_confirmation"]').prop('disabled',false);
        }
        else{
            $('input[name="password"]').prop('disabled',true);
            $('input[name="password_confirmation"]').prop('disabled',true);
        }
     });
    
    function updateClass(id){
    if (confirm('{{trans('message.CONFIRM_UPDATE_DATA')}}')){
    event.preventDefault();
    document.getElementById('update_form').submit();
    };
    return false;
    }

    function deleteClass(id){
    if (confirm('{{trans('message.CONFIRM_DELETE_DATA')}}')){
    event.preventDefault();
    document.getElementById('delete_form').submit();
    };
    return false;
    }

</script>
@endsection

@include('layout.footer')