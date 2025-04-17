@extends('layout.common')

@section('title', '個人マスタ・修正')
@section('keywords', '')
@section('description', '個人マスタの修正')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@inject('passwordUtils','App\Utils\PasswordUtil')
@inject('routeUtil','App\Utils\RouteUtil')

@section('content')
<div id="site_content" class="smaster">
    <div id="content">
        <h1>生徒マスタ・修正</h1>
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
        <div id="sm_correction" class="correction">

            <form method="post" action="{{route($routeUtil->getPrefix().'.personEdit.update',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'editSchCode'=>$person->sch_code,'editPsnCode'=>$person->psn_code])}}" >
                <!-- CSRF保護 -->
                @csrf
                @method('PUT')
                <input type='hidden' name='person_id' id='person_id' value='{{ $person->id }}' />
                @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
                {{--管理者の場合 --}}
                <table>
                    <tr>
                        <td>
                            <p>塾</p>
                        </td>
                        <td>
                            <p>
                                {{ $person->sch_code.' '.$person->sch_name }}
                                <input class="cell clear" type="hidden" name="sch_code" value="{{ $person->sch_code }}">
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>教室</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="cls_code" value="{{ old('cls_code',$person->cls_code) }}" autofocus required maxlength='4' >
                                <br>
                                @if($errors->has('cls_code'))<span class="error">{{ $errors->first('cls_code') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>塾生ID</p>
                        </td>
                        <td>
                            <p>
                                {{ $person->psn_code }}
                                <input class="cell clear" type="hidden" name="psn_code" value="{{ $person->psn_code }}" >
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>氏名</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="psn_name" id='psn_name' value="{{ old('psn_name',$person->psn_name) }}" maxlength='20'>
                                <br>
                                @if($errors->has('psn_name'))<span class="error">{{ $errors->first('psn_name') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>生年月日（月日）</ps>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="birthlow4" id='birthlow4'  value="{{ old('birthlow4',$person->birthlow4) }}"  maxlength='4'>
                                <br>
                                @if($errors->has('birthlow4'))<span class="error">{{ $errors->first('birthlow4') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>郵便番号（下４桁）</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="postallow4" id="postallow4" value="{{ old('postallow4',$person->postallow4) }}"  maxlength="4">
                                <br>
                                @if($errors->has('postallow4'))<span class="error">{{ $errors->first('postallow4') }}</span> @endif
                            </p>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>電話番号（下４桁）</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="tellow4" id="tellow4" value="{{ old('tellow4',$person->tellow4) }}" maxlength="4">
                                <br>
                                @if($errors->has('tellow4'))<span class="error">{{ $errors->first('tellow4') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    @if($psn_psw_update_flg)
                    <tr>
                        <td>
                            <p>メールアドレス</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" style="width: 220px;" type="text" name="email" id="email" value="{{ old('email',$person->mail_address) }}" maxlength="50">
                                <br>
                                @if($errors->has('email'))<span class="error">{{ $errors->first('email') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td>
                            <p>仮パスワード</p>
                        </td>
                        <td>
                            <p>
                                {{ $passwordUtils->decryptString($person->temppassword) }}
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
                @else
                {{-- 管理者以外の場合 --}}
                <table>
                    <tr>
                        <td>
                            <p>塾</p>
                        </td>
                        <td>
                            <p>
                                {{ $person->sch_code.' '.$person->sch_name }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>教室</p>
                        </td>
                        <td>
                            <p>
                                {{ $person->cls_code.' '.$person->cls_name }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>塾生ID</p>
                        </td>
                        <td>
                            <p>
                                {{ $person->psn_code }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>氏名</p>
                        </td>
                        <td>
                            <p>
                                {{ $person->psn_name }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>生年月日（月日）　　</p>
                        </td>
                        <td>
                            <p>
                                {{ $person->birthlow4 }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>郵便番号（下４桁）　　　　</p>
                        </td>
                        <td>
                            <p>
                                {{ $person->postallow4 }}
                            </p>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>電話番号（下４桁）　　　　</p>
                        </td>
                        <td>
                            <p>
                                {{ $person->tellow4 }}
                            </p>
                        </td>
                    </tr>
                    @if($psn_psw_update_flg)
                    <tr>
                        <td>
                            <p>メールアドレス</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" style="width: 220px;" type="text" name="email" id="email" value="{{ old('email',$person->mail_address) }}" maxlength="50">
                                <br>
                                @if($errors->has('email'))<span class="error">{{ $errors->first('email') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td>
                            <p>仮パスワード　　</p>
                        </td>
                        <td>
                            <p>
                                {{ $passwordUtils->decryptString($person->temppassword) }}
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
                @endif

                <div class="buttons">
                    <p class="cell"><input class="button bulu" type="submit" name="update" value="修正"></p>
                    @if(auth(config('constants.GUARD_ADMIN'))->check() && !session()->has(config('constants.SESSION_KEY_IS_SURROGATE_LOGINED')))
                    <p class="cell"><input class="button bulu delete_dialog" type="button" name="delete" value="削除" onclick='return deletePerson();'></p>
                    @endif     
                    
                    <a href="{{ route($routeUtil->getPrefix().'.persons.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="cell"><p class="button green">一覧に戻る</p></a>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="delete_form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>  

<script>

    function deletePerson(){
    if (confirm('{{trans('message.CONFIRM_DELETE_DATA')}}')){
    event.preventDefault();
    document.getElementById('delete_form').action = '{{ route('personEdit.delete',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')),'editSchCode'=>$person->sch_code,'delPsnCode'=>$person->psn_code]) }}';
    document.getElementById('delete_form').submit();
    };
    return false;
    }

</script>

@endsection

@include('layout.footer')