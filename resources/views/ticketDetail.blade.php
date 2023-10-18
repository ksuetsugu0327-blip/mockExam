@extends('layout.common')

@section('title', '個人登録票一覧')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')
@include('layout.header')

@inject('routeUtil','App\Utils\RouteUtil')

@section('content')

<script src="{{asset('/js/schoolofchoisedetail.js?' . time())}}"></script>

<div id="site_content" class="shiken">
    <div id="content">
        <h1>個人登録票一覧</h1>
        @if ($viewMode == 1 || $viewMode == 2)

        <div class="classroom_select_box">
            <form id='form1' action="{{ route($routeUtil->getPrefix() . '.ticketDetail.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE')), 'index'=>$key]) }}" method="post">
            @csrf
            <label>教室
            
                {{ Form::select('cls_code' 
                    ,[ '' => '---'] + $clsList
                    ,$clsCode
                    ,['class' => 'classroom_select', 'id' => 'clssearch']
                )}}
            </label>
            </form>
        </div>

        @endif
        <div class="personal_register_list_head">
            <ul class="exam_head">
                <li>年度 {{ $year }}</li>
                <li>回数 {{ $testName }}</li>
                <li>学年 高校{{ $grade }}年</li>
            </ul>
            <a href="{{ route($routeUtil->getPrefix() . '.ticketList.index',['schCode'=>session(config('constants.SESSION_KEY_SCHCODE'))]) }}" class="button green personal_list_back">一覧に戻る</a>
        </div>
        <div class="personal_register_table_outer">
        <!-- <p>({{ $socList->count() }}件表示)</p> -->
            <table class="personal_register_table">
                <thead>
                <tr>
                    <th>教室CD</th>
                    <th>教室名</th>
                    <th>塾生ID</th>
                    <th>氏名</th>
                    <th>高校名</th>
                    <th>受験番号</th>

                    @for($i = 1; $i <= 6; $i++)
                    <th>大学名{{ $i }}</th>
                    <th>学部名{{ $i }}</th>
                    <th>学科名{{ $i }}</th>
                    @endfor
                </tr>
                </thead>
                <tbody>
                @if($socList->count() > 0)
                    @foreach ($socList as $psn)
                    <tr>
                        <td>{{ $psn['cls_code'] }}</td>
                        <td>{{ $psn['cls_name'] }}</td>
                        <td>{{ $psn['psn_code'] }}</td>
                        <td>{{ $psn['psn_name'] }}</td>
                        <td>{{ $psn['hsc_name'] }}</td>
                        <td class="text_center">{{ $psn['exam_number'] }}</td>

                        @for($i = 1; $i <= 6; $i++)
                        <td>{{ $psn['c_univ_name' . $i] }}</td>
                        <td>{{ $psn['c_faculty_name' . $i] }}</td>
                        <td>{{ $psn['c_department_name' . $i] }}</td>
                        @endfor
                    </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')