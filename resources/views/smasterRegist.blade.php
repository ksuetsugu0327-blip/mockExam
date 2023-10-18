@extends('layout.common')

@section('title', '個人マスタ・登録')
@section('keywords', '')
@section('description', '個人マスタの新規登録')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="smaster">
    <div id="content">
        <h1>生徒マスタ・登録</h1>
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
        <div id="sm_touroku" class="touroku">
            <form method="post" action="{{route('personRegist.store',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" >
                <!-- CSRF保護 -->
                @csrf
                <table>
                    <tr>
                        <td>
                            <p>塾</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="sch_code" value="{{ old('sch_code') }}" autofocus required maxlength='7' >
                                <br>
                                @if($errors->has('sch_code'))<span class="error">{{ $errors->first('sch_code') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>教室</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="cls_code" value="{{ old('cls_code') }}" required maxlength='4' >
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
                                <input class="cell clear" type="text" name="psn_code" value="{{ old('psn_code') }}" required maxlength='11' >
                                <br>
                                @if($errors->has('psn_code'))<span class="error">{{ $errors->first('psn_code') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>氏名</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="psn_name" id='psn_name' value="{{ old('psn_name') }}" maxlength='20'>
                                <br>
                                @if($errors->has('psn_name'))<span class="error">{{ $errors->first('psn_name') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>生年月日（月日）</p>
                        </td>
                        <td>
                            <p>
                                <input class="cell clear" type="text" name="birthlow4" id='birthlow4'  value="{{ old('birthlow4') }}" maxlength='4'>
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
                                <input class="cell clear" type="text" name="postallow4" id="postallow4" value="{{ old('postallow4') }}" maxlength="4">
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
                                <input class="cell clear" type="text" name="tellow4" id="tellow4" value="{{ old('tellow4') }}" maxlength="4">
                                <br>
                                @if($errors->has('tellow4'))<span class="error">{{ $errors->first('tellow4') }}</span> @endif
                            </p>
                        </td>
                    </tr>
                </table>
                <div class="sbuttons">
                    <p class="cell"><input class="button bulu" type="submit" name="submit" value="登録"></p>
                    <p class="cell"><input id="inputClear" class="button green" type="button" name="reset" value="クリア"></p>
                    <a href="{{ route('sch.persons.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="cell"><p class="button green">一覧に戻る</p></a>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@include('layout.footer')
