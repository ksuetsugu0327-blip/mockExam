@extends('layout.common')

@section('title', '成績表一覧')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="stseiseki">
    <div id="content">
        <h1>成績表一覧</h1>
        <!-- エラーメッセージ表示欄が必要な場合はここへ追記 -->
        <div id="st_List" class="List">
            <p>(999件中9件表示)</p>
            <script type="text/javascript" language="javascript">
                function selectboxChange() {
                    target = document.getElementById("output");
                    $('.default').css('display', 'none');

                    selindex = document.form1.Select1.selectedIndex;
                    switch (selindex) {
                        case 1:
                            target.innerHTML = "<table><tr><td><a  target='_blank'>２０１９年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr><tr><td><a  target='_blank'>２０１９年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr><tr><td><a  target='_blank'>２０１９年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr><tr><td><a  target='_blank'>２０１９年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr><tr><td><a  target='_blank'>２０１９年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr></table>";
                            break;
                        case 2:
                            target.innerHTML = "<table><tr><td><a  target='_blank'>２０１８年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr><tr><td><a  target='_blank'>２０１８年度＿マーク０１回＿１学年1</a></td><td></td></tr><tr><td><a  target='_blank'>２０１８年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr><tr><td><a  target='_blank'>２０１８年度＿マーク０１回＿１学年1</a></td><td></td></tr><tr><td><a  target='_blank'>２０１８年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr></table>";
                            break;
                        case 3:
                            target.innerHTML = "<table><tr><td><a  target='_blank'>２０１７年度＿マーク０１回＿１学年1</a></td></tr><tr><td><a  target='_blank'>２０１７年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr><tr><td><a  target='_blank'>２０１７年度＿マーク０１回＿１学年1</a></td></tr><tr><td><a  target='_blank'>２０１７年度＿マーク０１回＿１学年1</a></td></tr><tr><td><a  target='_blank'>２０１７年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr></table>";
                            break;
                        case 4:
                            target.innerHTML = "<table><tr><td><a  target='_blank'>２０１６年度＿マーク０１回＿１学年1</a></td></tr><tr><td><a  target='_blank'>２０１６年度＿マーク０１回＿１学年1</a></td></tr><tr><td><a  target='_blank'>２０１６年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr><tr><td><a  target='_blank'>２０１６年度＿マーク０１回＿１学年1</a></td></tr><tr><td><a  target='_blank'>２０１６年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr></table>";
                            break;
                        case 5:
                            target.innerHTML = "<table><tr><td><a  target='_blank'>２０１５年度＿マーク０１回＿１学年1</a></td></tr><tr><td><a target='_blank'>２０１５年度＿マーク０１回＿１学年1</a></td></tr><tr><td><a  target='_blank'>２０１５年度＿マーク０１回＿１学年1</a></td></tr><tr><td><a  target='_blank'>２０１５年度＿マーク０１回＿１学年1</a></td></tr><tr><td><a  target='_blank'>２０１５年度＿マーク０１回＿１学年1</a></td><td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td></tr></table>";
                            break;
                    }
                }
            </script>
            </head>
            <body>
                <form name="form1" action="">
                    <select id="Select1" onchange="selectboxChange();">
                        <option>---</option>
                        <option>2019</option>
                        <option>2018</option>
                        <option>2017</option>
                        <option>2016</option>
                        <option>2015</option>
                    </select>
                </form>
                <div id="output">
                    <div class="default">
                        <table>
                            <tr>
                                <td><a  target='_blank'>２０１９年度＿マーク０１回＿１学年1</a></td>
                                <td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td>
                            </tr>
                            <tr>
                                <td><a  target='_blank'>２０１９年度＿マーク０１回＿１学年1</a></td>
                                <td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td>
                            </tr>
                            <tr>
                                <td><a  target='_blank'>２０１９年度＿マーク０１回＿１学年1</a></td>
                                <td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td>
                            </tr>
                            <tr>
                                <td><a  target='_blank'>２０１９年度＿マーク０１回＿１学年1</a></td>
                                <td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td>
                            </tr>
                            <tr>
                                <td><a  target='_blank'>２０１９年度＿マーク０１回＿１学年1</a></td>
                                <td><a  target='_blank' class='d_date, r_link'>マーク01回目データ集</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
        </div>
    </div>
</div>

@endsection

@include('layout.footer')