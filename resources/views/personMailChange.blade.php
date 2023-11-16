@extends('layout.common')

@section('title', 'メールアドレス')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="main">
    <div id="site_content" class="logtouroku">
        <div id="content">
        <h1>登録メールアドレス変更</h1>
            @if ($errors->any() || isset($message))
            <div class="error_message">
                @if (isset($message))
                    <strong>{{ $message }}</strong></br>
                @else
                    <strong>{{ $errors->first('message') }}</strong></br>
                @endif
            </div></br>
            @endif
             <form action="{{route('personMailChange.update',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}" method="post">
                @csrf
                <input type="hidden" id="psn_code" name="psn_code" value="{{$psn_code}}" />
				<div id="log_touroku" class="login">
                <table >
                   <tr><td colspan="2"  style="padding-bottom:30px;"><strong>パスワード再設定時のメールアドレスを変更できます。</strong></td></tr>
                   <tr><td>現在のメールアドレス&nbsp;</td><td>{{$current_mail}}</td></tr>
                   <tr><td>&nbsp</td><td>&nbsp</td></tr>
                   <tr><td style="padding:0 6px 1em 0">新しいメールアドレス&nbsp;</td>
                       <td style="padding:0 6px 1em 0">
                           <input id="mail_address" name="mail_address" required autofocus>
                           @if ($errors->has('mail_address'))
                                <br/>
                                <span class="error">
                                    <strong>{{ $errors->first('mail_address') }}</strong>
                                </span>
                           @endif
                           <br/>
                        </td>
                    </tr>
                    <tr>
                       <td>新しいメールアドレス確認&nbsp;</td>
                       <td>
                          <input id="mail_address_confirmation" name="mail_address_confirmation" required>
                       </td>
                    </tr>
				</table>
						<div><input class="button  bulu" type="submit" name="login" value="更 新" /></div>
			  </form>
			</div>
        </div>
    </div>
</div><!-- /#main-->
@endsection

@include('layout.footer')