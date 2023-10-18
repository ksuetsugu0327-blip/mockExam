<!-- ファイルのディレクトリは仮配置です -->

@extends('layout.common')

@section('title', '成績表一覧')
@section('keywords', 'A,B,C')
@section('description', '説明文')
@section('pageCss')
@endsection

@include('layout.head')

@include('layout.header')

@section('content')
<div id="site_content" class="seiseki">
    <div id="content">
        <h1>成績表一覧</h1>
        <div id="shikenmei"><p>２０１９年度＿マーク０１回＿１学年</p></div>
        <div id="se_List" class="List">
            <form method="post" action="" name="form">
                <script type="text/javascript">
                    function toggle_check(form, button) {
                        if (button.value == "全選択") {
                            var flag = 1;
                            button.value = "全解除";
                        } else {
                            var flag = 0;
                            button.value = "全選択";
                        }

                        for (i = 0; i < form.length; i++) {
                            if (form.elements[i].type == "checkbox")
                                form.elements[i].checked = flag;
                        }
                    }
                </script>
                <input class="choice button  bulu" type="button" onclick="toggle_check(this.form, this)" value="全選択">
                <label>
                    <input class="checkbox" type="checkbox" name="shiken" value="shiken1" onClick="DisChecked();"><span class="ctext"><a href="./pdf/test.pdf"> ２０１９年度＿マーク０１回＿１学年1</a></span>
                </label>
                <label>
                    <input class="checkbox" type="checkbox" name="shiken" value="shiken2" onClick="DisChecked();"><span class="ctext"><a href="./pdf/test.pdf"> ２０１９年度＿マーク０１回＿１学年2</a></span>
                </label>
                <label>
                    <input class="checkbox" type="checkbox" name="shiken" value="shiken3" onClick="DisChecked();"><span class="ctext"><a href="./pdf/test.pdf"> ２０１９年度＿マーク０１回＿１学年3</a></span>
                </label>
                <label>
                    <input class="checkbox" type="checkbox" name="shiken" value="shiken4" onClick="DisChecked();"><span class="ctext"><a href="./pdf/test.pdf"> ２０１９年度＿マーク０１回＿１学年4</a></span>
                </label>
                <label>
                    <input class="checkbox" type="checkbox" name="shiken" value="shiken5" onClick="DisChecked();"><span class="ctext"><a href="./pdf/test.pdf"> ２０１９年度＿マーク０１回＿１学年5</a></span>
                </label>
                <label>
                    <input class="checkbox" type="checkbox" name="shiken" value="shiken6" onClick="DisChecked();"><span class="ctext"><a href="./pdf/test.pdf"> ２０１９年度＿マーク０１回＿１学年6</a></span>
                </label>
                <label>
                    <input class="checkbox" type="checkbox" name="shiken" value="shiken7" onClick="DisChecked();"><span class="ctext"><a href="./pdf/test.pdf"> ２０１９年度＿マーク０１回＿１学年7</a></span>
                </label>
                <label>
                    <input class="checkbox" type="checkbox" name="shiken" value="shiken8" onClick="DisChecked();"><span class="ctext"><a href="./pdf/test.pdf"> ２０１９年度＿マーク０１回＿１学年8</a></span>
                </label>    
                <label>
                    <input class="checkbox" type="checkbox" name="shiken" value="shiken9" onClick="DisChecked();"><span class="ctext"><a href="./pdf/test.pdf"> ２０１９年度＿マーク０１回＿１学年9</a></span>
                </label>
            </form>
            <div class="buttons">
                <table>
                    <tr>
                        <td class="down"><a  class="button bulu" href="">ダウンロード</a></td><td class="return"><a class="button green"  href="./shikenkaisu.html">一覧に戻る</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')