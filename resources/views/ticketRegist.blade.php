@extends('layout.common')

@section('title', '志望校登録')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')
@include('layout.header')

@inject('routeUtil','App\Utils\RouteUtil')

@section('content')
<script>
hscUrl = '{{route('ticketRegist.hsclist',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}';
univUrl = '{{route('ticketRegist.univlist',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}';
fclUrl = '{{route('ticketRegist.fcllist',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}';
dptUrl = '{{route('ticketRegist.dptlist',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))])}}';
</script>
<script src="{{asset('/js/schoolofchoiseregist.js?' . time())}}"></script>

<div id="site_content" class="kanri">
    <div id="registration_content">
        <div class="registration_content_head">
            <h1>個人登録票</h1>
            <div class="univ_code_doc"><a href="{{ asset($code_pdf_url) }}" style="text-decoration:underline;" target="code_pdf">{{ $code_pdf_name }}</a></div>
        </div>
        <div id="shikenmei">
            <p style="font-size: 14px;font-weight: bold;">試験名：{{ $year }}年度_{{ $testName }}_高{{ $grade }}</p>
        </div>
        <div id="exam_number">
            <h3>受験番号</h3>
            <p>{{ $exam_number }}</p>
        </div>
        @foreach ($errors->all() as $error)
            <div class="error_message">
                <strong>{{ $error }}</strong>
            </div>
        @endforeach
        @if (isset($message))
            <div class="success_message">
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div class="error_message" id="isHscPrefCode" style="display:none;">
            <strong>高校情報の都道府県は必須入力です。</strong>
        </div>
        <form id="fm_univ_choice" method="post" action="{{route('ticketRegist.store',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')), 'index' => $key])}}">
            @csrf
            <div id="registration_List" class="List" data-grade_id="{{ $grade }}">
                <h3>高校情報<span class="head_alert_text">*「都道府県」を選択すると「高校」が選択可能になります。</span></h3>
                <table class="hsc_cond_table">
                    <tbody>
                    <tr class="no_bottom_border">
                        <th class="required ft_normal">都道府県</th>
                        <td>
                            {{ Form::select('hsc_pref_code' 
                                ,['' => '---'] + $pref_list + ['99' => 'その他']
                                ,$hsc_pref_code
                                ,['class' => 'pref_id', 'id' => 'hsc_pref_code']
                            )}}
                        </td>
                        <th class="ft_normal">高校名で絞込み</th>
                        <td>{{ Form::input('text', 'hsc_name', $hsc_name, ['class' => 'search_school_name', 'id' => 'hsc_name']) }}（部分一致）</td>
                    </tr>
                    <tr>
                        <th>高校</th>
                        <td colspan="3" class="td_hsc_code">
                            {{ Form::select('hsc_code' 
                                ,['' => '選択してください'] + $hsc_list
                                ,$hsc_code
                                ,['class' => 'school_id', 'id' => 'hsc_code']
                            )}}
                            <div class="right_btn">
                                <input id="formsubmit" class="button bulu" type="submit" name="submit" value="志望校を登録する">
                                <a href="{{ route($routeUtil->getPrefix() . '.ticketList.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="button green">一覧に戻る</a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h3>志望大学<span class="head_alert_text2">*「設置区分」、「都道府県」いずれかを選択すると「大学」が選択可能になります。</span></h3>
                {{ Form::input('hidden', 'enable_flag', $dptEnable, ['id' => 'enable_flag']) }}
                <table>
                    @for ($i = 1; $i <= 6; $i++)
                    @php
                        $instClass = 'inst_class' . $i;
                        $univPref = 'univ_pref' . $i;
                        $srchUnivName = 'search_univ_name' . $i;
                        $univCode = 'univ_code' . $i;
                        $univList = 'univ_list' . $i;
                        $fclCode = 'faculty_code' . $i;
                        $fclList = 'faculty_list' . $i;
                        $dptCode = 'department_code' . $i;
                        $dptList = 'department_list' . $i;
                    @endphp
                    <tr>
                        <th>第{{ $i }}志望</th>
                        <td>
                            <ul class="univ_choice_list">
                                <li><p class="required">設置区分
                                    {{ Form::select($instClass
                                        ,config('selectlistarray.INST_CLASS_LIST')
                                        ,$$instClass
                                        ,['class' => 'univ_type', 'id' => $instClass]
                                    )}}</p>
                                </li>
                                <li><p class="required">都道府県
                                    {{ Form::select($univPref
                                        ,['' => '---'] + $pref_list
                                        ,$$univPref
                                        ,['class' => 'univ_pref', 'id' => $univPref]
                                    )}}</p>
                                </li>
                                <li>大学名で絞込み
                                    {{ Form::input('text', $srchUnivName, $$srchUnivName, ['class' => 'search_univ_name', 'id' => $srchUnivName]) }}
                                    （部分一致）
                                </li>
                                <li class="ftb">大学
                                    {{ Form::select($univCode
                                        ,['' => '選択してください'] + $$univList
                                        ,$$univCode
                                        ,['class' => 'univ_name', 'id' => $univCode]
                                    )}}
                                </li>
                                <li class="ftb">学部
                                    {{ Form::select($fclCode
                                        ,['' => '選択してください'] + $$fclList
                                        ,$$fclCode
                                        ,['class' => 'univ_fac', 'id' => $fclCode]
                                    )}}
                                </li>
                                <li class="ftb">学科
                                    {{ Form::select($dptCode
                                        ,['' => '選択してください'] + $$dptList
                                        ,$$dptCode
                                        ,['class' => 'univ_dep', 'id' => $dptCode]
                                    )}}
                                </li>
                            </ul>
                        </td>
                        <td><a class="button green clear_univ_choice half_width" href="#" id="univclear{{ $i }}">クリア</a></td>
                    </tr>
                    @endfor
                </table>
            </div>
{{--  下段は消す
            <div class="buttons text_right" style="margin-top:50px">
                <input id="formsubmit" class="button bulu" type="submit" name="submit" value="登 録">
                <a href="{{ route($routeUtil->getPrefix() . '.ticketList.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="button green">一覧に戻る</a>
            </div>
--}}
        </form>
    </div>
</div>

@endsection

@include('layout.footer')